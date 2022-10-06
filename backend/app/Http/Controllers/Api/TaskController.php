<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pdf\FaultReportPdf;
use App\Http\Controllers\Pdf\ShippingInformationPdf;
use App\Http\Resources\ClaimSimpleResource;
use App\Models\Claim;
use App\Models\ClaimFile;
use App\Models\Comment;
use App\Models\Customer;
use App\Models\Dealer;
use App\Models\Member;
use App\Models\Printer;
use App\Models\PrinterPartCord;
use App\Models\PrinterPartReplacement;
use App\Models\PrintHead;
use App\Models\PrintHeadReplacement;

//use App\Notifications\DispatcherClaimAddedNotification;
use App\Notifications\GuaranteeWarrantyApprovedNotification;
use App\Notifications\TeamClaimAddedNotification;
use App\Notifications\TeamInternalCheckCompletedNotification;
use App\Notifications\UserClaimAddedNotification;
use App\Notifications\UserClaimStatusNotification;
use App\Notifications\UserFinalCheckApprovedNotification;
use App\Notifications\UserFinalCheckDeclinedNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ClaimResource;
use Illuminate\Support\Str;
use Omniphx\Forrest\Providers\Laravel\Facades\Forrest;

class ClaimController extends Controller
{
    /**
     * Get all claims
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $qs = Task
//        $qs = Task::with('internalCheckUser', 'customer', 'dealer', 'printer', 'engineer', 'printHeadReplacements', 'files')
            ->orderBy('created_at', 'desc');
        return TaskResource::collection($qs);
    }

    /**
     * Get an claim by id
     *
     * @param $id
     * @return ClaimResource
     */
    public function getTask($id)
    {
        return new TaskResource(Task::whereRaw('id = ?', [$id])->firstOrFail());
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function storeTask(Request $request)
    {
        if (auth()->user()->role !== "manager" && auth()->user()->role !== "staff" && auth()->user()->role !== "engineer") {
            return response()->json([
                'unauthorized'
            ], 401);
        }

        $data = json_decode($request->get('data'));

        $validator = Validator::make((array)$data, [
            'printer_serial_no' => 'required|max:255',
            'customer_id' => 'required|max:255',
            'number_of_prints' => 'required|max:255',
            'date_of_visit' => 'required|max:255',
            'compensation' => 'required|max:255',
            'replacement_type' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
//                'errors' => $validator->errors()
                'error' => 'Missing required fields'
            ], 422);
        }

//        foreach ($request->file('file') as $file) {
//            if (!in_array($file->getClientOriginalExtension(), ['log', 'jpg', 'pdf', 'png', 'PNG', 'JPG', 'JPG'])) {
//                return response()->json([
//                    'error' => 'File is not valid',
//                    'extension' => $file->getClientOriginalExtension(),
//                ], 422);
//            }
//        }

        $engineer = Member::where('id', auth()->user()->id)->firstOrFail();
        $dealer = Dealer::where('id', auth()->user()->dealer_id)->firstOrFail();

        $claim = new Claim;
        $claim->printer_serial_no = $data->printer_serial_no;
        $claim->customer_id = $data->customer_id;
        $claim->engineer_id = $engineer->id;
        $claim->dealer_id = $dealer->id;
        $claim->number_of_prints = $data->number_of_prints;
        $claim->date_of_visit = date('Y-m-d', strtotime($data->date_of_visit));
        $claim->information = $data->information;
        $claim->err_code = $data->err_code;
        $claim->status = 1; // 1 = open, 2 = In Progress, 3 = Done
        $claim->compensation = $data->compensation; // 1 = open, 2 = In Progress, 3 = Done
        $claim->replacement_type = $data->replacement_type; // 1 = printhead, 2 = other
        $claim->internal_check_status = 0;
        $claim->final_check_status = 0;

        try {
            $claim->save();
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }

        // set fault report number based on id
        $claim->fault_report_no = $this->getFaultReportNo($claim->id);
        try {
            $claim->save();
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }

        $files = $request->file('file');
        foreach ($files as $file) {
            $path = Storage::disk('s3')->putFileAs($file->getFilename(), $file, Str::random(40) . "." . $file->getClientOriginalExtension());
            $claimFile = new ClaimFile();
            $claimFile->claim_id = $claim->id;
            $claimFile->path = $path;
            $claimFile->name = $file->getClientOriginalExtension() === "log" ? $claim->printer_serial_no . '_' . $file->getClientOriginalName() : $file->getClientOriginalName();
            $claimFile->size = $file->getSize();
            $claimFile->extension = $file->getClientOriginalExtension();

            try {
                $claimFile->save();
            } catch (Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                ], 500);
            }
        }

        $print_head_replacements = isset($data->print_head_replacements) ? $data->print_head_replacements : [];
        foreach ($print_head_replacements as $printHead) {

            $oldPrintHead = PrintHead::where('serial_no', $printHead->original_serial_no)->first();
            $oldPrintHead->installed = 0;
            $oldPrintHead->removing_date = $claim->date_of_visit;

            try {
                $oldPrintHead->save();
            } catch (Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                ], 500);
            }

            $printHeadReplacement = new PrintHeadReplacement();
            $printHeadReplacement->original_serial_no = $printHead->original_serial_no;
            $printHeadReplacement->replaced_serial_no = $printHead->replaced_serial_no;
            $printHeadReplacement->original_ph_rev = isset($printHead->original_ph_rev) ? $printHead->original_ph_rev : '';
            $printHeadReplacement->replaced_ph_rev = isset($printHead->replaced_ph_rev) ? $printHead->replaced_ph_rev : '';
            $printHeadReplacement->replacement_date = $claim->date_of_visit;
            $printHeadReplacement->ph_position = $oldPrintHead->ph_position;
            $printHeadReplacement->color = $oldPrintHead->color;
            $printHeadReplacement->claim_id = $claim->id;
            $printHeadReplacement->printer_serial_no = $claim->printer_serial_no;
            $printHeadReplacement->dealer_id = $claim->dealer_id;
            $printHeadReplacement->is_dispatched = 0;

            try {
                $printHeadReplacement->save();
            } catch (Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                ], 500);
            }

            // check for duplicate
            if (PrintHead::where('serial_no', $printHeadReplacement->replaced_serial_no)->first()) {
                return response()->json([
                    'error' => 'Serial already exists',
                ], 500);
            }

            $newPrintHead = new PrintHead();
            $newPrintHead->serial_no = $printHeadReplacement->replaced_serial_no;
            $newPrintHead->rev_no = isset($printHeadReplacement->replaced_ph_rev) ? $printHeadReplacement->replaced_ph_rev : '';
            $newPrintHead->initial_number_of_prints = 0;
            $newPrintHead->installation_date = $printHeadReplacement->replacement_date;
            $newPrintHead->ph_position = $printHeadReplacement->ph_position;
            $newPrintHead->color = $printHeadReplacement->color;
            $newPrintHead->printer_serial_no = $printHeadReplacement->printer_serial_no;
            $newPrintHead->dealer_id = $printHeadReplacement->dealer_id;
            $newPrintHead->installed = 1;
            $newPrintHead->head_guarantee_state = 1;
            $newPrintHead->head_guarantee_date = date('Y-m-d', strtotime("+3 months", strtotime($claim->date_of_visit)));

            try {
                $newPrintHead->save();
            } catch (Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                ], 500);
            }
        }

        $other_parts_replacements = isset($data->other_parts_replacements) ? $data->other_parts_replacements : [];
        foreach ($other_parts_replacements as $otherPart) {
            $printerPartReplacement = new PrinterPartReplacement();
            $printerPartReplacement->claim_id = $claim->id;
            $printerPartReplacement->printer_serial_no = $claim->printer_serial_no;
            $printerPartReplacement->replacement_date = $claim->date_of_visit;
            $printerPartReplacement->number = $otherPart->number;
            $printerPartReplacement->name = $otherPart->name;
            $printerPartReplacement->quantity = $otherPart->quantity;
            $printerPartReplacement->dealer_id = $claim->dealer_id;
            $printerPartReplacement->is_dispatched = 0;;

            try {
                $printerPartReplacement->save();
            } catch (Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                ], 500);
            }
        }

        // Overwrite number of prints
        $printer = Printer::where('printer_serial_no', $claim->printer_serial_no)->first();
        try {
            $printer->number_of_prints = $claim->number_of_prints;
            $printer->number_of_prints_changed = date('Y-m-d');;
            $printer->save();
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }

        $customer = Customer::where('id', $claim->customer_id)->first();

        // Generierung PDFs
        $faultReport = new FaultReportPdf($claim, $printer, $engineer, $dealer, $customer, $print_head_replacements, $other_parts_replacements);
        $shippingInformationFileName = "Shipping_Information_" . $claim->fault_report_no . ".pdf";
        $shippingInformation = new ShippingInformationPdf($claim, $printer, $engineer, $dealer, $customer, $print_head_replacements, $other_parts_replacements);

        // Notifizierung Staff
        $staff = Member::where('groups', '2')->get();
        Notification::send($staff, new TeamClaimAddedNotification($claim, $printer, $engineer, $dealer, $faultReport));

		if (config('hive.realm') !== 'bma') {
			Notification::route('mail', 't.korte@brother-ism.com')->notify(new TeamClaimAddedNotification($claim, $printer, $engineer, $dealer, $faultReport));
		}

        // Notifizierung Techniker
        $engineer->notify(new UserClaimAddedNotification($claim, $faultReport, $shippingInformation));

        // Notifizierung Dispatcher
//        $dispatcher = Member::where('groups', '6')->get();
//        foreach ($dispatcher as $dispatcherUser) {
//            $dispatcherUser->notify(new DispatcherClaimAddedNotification($claim, $dealer));
//        }

//        // create ticket at zendesk
//        $zendeskController = new ZendeskController();
//        try {
//            $ticketId = $zendeskController->createTicket($claim, $faultReport);
//        } catch (\Exception $e) {
//            return response()->json([
//                'error' => $e->getMessage(),
//            ], 500);
//        }
//
//        // save ticket id
//        $claim->ticket_id = $ticketId;
//        try {
//            $claim->save();
//        } catch (\Exception $e) {
//            return response()->json([
//                'error' => $e->getMessage(),
//            ], 500);
//        }

        return response()->json([
            "success" => true,
            "file" => [
                'content' => base64_encode($shippingInformation->generateOutput()),
                'filename' => $shippingInformationFileName,
            ]
        ], 201);
    }

//    /**
//     * @param $id
//     * @return \Symfony\Component\HttpFoundation\StreamedResponse
//     */
//    public function getShippingInformation($id)
//    {
////        $data = json_decode($request->get('data'));
//
//        $claim = Claim::where('id', $id)->first();
//        $printer = Printer::where('printer_serial_no', $claim->printer_serial_no)->first();
//        $engineer = Member::where('id', $claim->engineer_id)->first();
//        $dealer = Dealer::where('id', $claim->dealer_id)->first();
//        $customer = Customer::where('id', $claim->customer_id)->first();
//        $print_head_replacements = PrintHeadReplacement::where('claim_id', $claim->id)->get();
//
//        $shippingInformation = new ShippingInformationPdf($claim, $printer, $engineer, $dealer, $customer, $print_head_replacements);
//
//        $shippingInformationContent = $shippingInformation->generateOutput();
//
//        return response()->streamDownload(function () use ($shippingInformationContent) {
//            echo $shippingInformationContent;
//        }, $shippingInformationFileName);
//    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function processInternalCheck(Request $request)
    {
        if (auth()->user()->role !== "manager" && auth()->user()->role !== "staff") {
            return response()->json([
                'unauthorized'
            ], 401);
        }

        $data = json_decode($request->get('data'));

        $validator = Validator::make((array)$data, [
            'claim_id' => 'required|max:255',
            'status' => 'required|max:255'
        ]);
        if ($validator->fails()) {
            return response()->json([
//                'errors' => $validator->errors()
                'error' => 'Missing required fields'
            ], 422);
        }

        $user = Member::where('id', auth()->user()->id)->firstOrFail();

        $claim = Claim::where('id', $data->claim_id)->firstOrFail();
        $claim->internal_check_status = $data->status;
        $claim->internal_check_user_id = $user->id;
        $claim->internal_check_date = date('Y-m-d');
        $claim->reply_choice = $data->reply_choice;
        $claim->standard_reply = isset($data->standard_reply) ? $data->standard_reply : '';
        $claim->individual_reply = $data->individual_reply;
        $claim->reason_choice = $data->reason_choice;
        $claim->reason = $data->reason;
        $claim->problem_type = $data->problem_type;

        try {
            $claim->save();
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }

        // Notifizierung Manager
        $manager = Member::where('groups', '1')->get();
        foreach ($manager as $managerUser) {
            $managerUser->notify(new TeamInternalCheckCompletedNotification($claim, $user));
        }

        return response()->json([
            'success' => true,
        ], 201);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function processFinalCheck(Request $request)
    {
        if (auth()->user()->role !== "manager") {
            return response()->json([
                'unauthorized'
            ], 401);
        }

        $data = json_decode($request->get('data'));

        $validator = Validator::make((array)$data, [
            'claim_id' => 'required|max:255',
            'status' => 'required|max:255'
        ]);
        if ($validator->fails()) {
            return response()->json([
//                'error' => $validator->errors()
                'error' => 'Missing required fields'
            ], 422);
        }

        $user = Member::where('id', auth()->user()->id)->firstOrFail();

        $claim = Claim::where('id', $data->claim_id)->firstOrFail();
        $claim->final_check_status = $data->status;
        $claim->final_check_user_id = $user->id;
        $claim->closing_date = date('Y-m-d');
        $claim->status = 3; // 3 = done
        $claim->reply_choice = $data->reply_choice;
        $claim->standard_reply = $data->standard_reply;
        $claim->individual_reply = $data->individual_reply;
        $claim->reason_choice = $data->reason_choice;
        $claim->reason = $data->reason;
        $claim->problem_type = $data->problem_type;

        try {
            $claim->save();
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }

        $engineer = Member::where('id', $claim->engineer_id)->firstOrFail();

        if ($claim->final_check_status === "1" || $claim->final_check_status === "2") { // warranty granted || internal granted

            $printer = Printer::where('printer_serial_no', $claim->printer_serial_no)->firstOrFail();
            $dealer = Dealer::where('id', $claim->dealer_id)->firstOrFail();
            $customer = Customer::where('id', $claim->customer_id)->firstOrFail();
            $print_head_replacements = PrintHeadReplacement::where('claim_id', $claim->id)->get();
            $other_parts_replacements = PrinterPartReplacement::where('claim_id', $claim->id)->get();

            // Generierung PDFs
            $faultReport = new FaultReportPdf($claim, $printer, $engineer, $dealer, $customer, $print_head_replacements, $other_parts_replacements);

            // Mail an Engineer
            $engineer->notify(new UserFinalCheckApprovedNotification($claim, $faultReport));

            // Mail an Guarantee-Team
            if (config('hive.realm') === 'bma') {
                Notification::route('mail', 'wai.lcw@brother.com.hk')->notify(new GuaranteeWarrantyApprovedNotification($claim, $user, $faultReport, $dealer));
            } else {
                Notification::route('mail', 'guarantee@brother.digital')->notify(new GuaranteeWarrantyApprovedNotification($claim, $user, $faultReport, $dealer));
            }

            if ($claim->final_check_status === "1") {

                // Sende Customer an Salesforce
                if ($customer->salesforce_id === "" || $customer->salesforce_id === null) {
                    try {
                        $this->sendCustomerToSalesforce($customer);
                    } catch (\Exception $e) {
                        return response()->json([
                            'error' => $e->getMessage(),
                        ], 500);
                    }
                }

                // Sende Claim an Salesforce
                try {
                    $this->sendClaimToSalesforce($claim, $printer, $dealer, $customer, $print_head_replacements);
                } catch (Exception $e) {
                    return response()->json([
                        'error' => $e->getMessage(),
                    ], 500);
                }
            }
        } else if ($claim->final_check_status === "3") { // warranty not granted
            // Mail an Engineer
            $engineer->notify(new UserFinalCheckDeclinedNotification($claim));
        }

        return response()->json([
            'success' => true,
        ], 201);
    }


    /**
     * Send customer to Salesforce
     * @param $customer
     * @return JsonResponse|void
     */
    public function sendCustomerToSalesforce($customer)
    {
        Forrest::authenticate();

        if (config('hive.realm') === 'bma') {
            $RecordTypeId = '01210000000kQb2'; // GT Customer
            $OwnerId = '005100000098pAz'; // Cheung, Elinor
        } else {
            $RecordTypeId = '01210000000kQbC'; // GT Buyer
            $OwnerId = '005100000098PKs'; // André
        }

        $dealer = Dealer::where('id', $customer['dealer_id'])->firstOrFail();

        $response = Forrest::sobjects('Account', [
            'method' => 'post',
            'body' => [
                'RecordTypeId' => $RecordTypeId,
                'OwnerId' => $OwnerId,
                'CurrencyIsoCode' => 'EUR',
                'Name' => $customer['name'],
                'NameLocal' => $customer['name'],
                'Address__c' => $customer['street'] . " " . $customer['street_no'] . " " . $customer['zip_code'] . " " . $customer['city'],
                'BillingPostalCode__c' => $customer['zip_code'],
                'Country__c' => $customer['country'],
//                'ParentCustomer__c' => $dealer->salesforce_id
//                'ParentDealer__c' => $dealer->salesforce_id
                'parentId' => $dealer->salesforce_id,
                'PrintMedia__c' => 'Tシャツ'
            ]
        ]);

        if (!empty($response["exception"])) {

            $existingCustomer = Forrest::query("SELECT Id FROM Account WHERE Name = '" . $customer['name'] . "' LIMIT 1");
            try {
                $customer->salesforce_id = $existingCustomer['records'][0]['Id'];
                $customer->save();
            } catch (Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                ], 500);
            }

            return response()->json([
                'error' => "There was an error creating the account at Salesforce (" . (preg_match("/DUPLICATES_DETECTED/", $response["exceptionMessage"]) ? 'account already exists' : 'technical') . ")." . $response["exceptionMessage"],
            ], 500);
        }

        if (!empty($response["errors"])) {
            return response()->json([
                'error' => "There was an error creating the account at Salesforce",
            ], 500);
        }

        if (!empty($response["success"]) && empty($response["errors"])) {
            try {
                $customer->salesforce_id = $response["id"];
                $customer->save();
            } catch (Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                ], 500);
            }
        }
    }

    /**
     * Send claim to Salesforce
     * @param $claim
     * @param $printer
     * @param $dealer
     * @param $customer
     * @param $print_head_replacements
     * @return JsonResponse
     */
    public
    function sendClaimToSalesforce($claim, $printer, $dealer, $customer, $print_head_replacements)
    {
        $return["partialError"] = $return["partialErrorDetails"] = "";
        $errorcode = $claim->err_code != '' ? $claim->err_code : null;

        $printer_model = '';
        if ($printer->printer_type == "gtx") {
            $printer_model = 'a091000003TJ7ENAA1';
        }
        if ($printer->printer_type == "gtxpro") {
            $printer_model = 'a091000005VVMVUAA5';
        }
        if ($printer->printer_type == "gtxprobulk") {
            $printer_model = 'a091000005VVMXG';
        }
        if ($printer->printer_type == "gtx600") {
            $printer_model = 'a091000006KSQgVAAX';
        }
        if ($printer->printer_type == "gt-3") {
            $print_heads = PrintHead::where(['printer_serial_no' => $claim->printer_serial_no, 'installed' => 1])->find();
            if (count($print_heads) == 4) {
                $printer_model = 'a091000002Fp7JmAAJ'; //GT-341
            }
            if (count($print_heads) == 6) {
                $printer_model = 'a091000002Fp7JnAAJ'; //GT-361
            }
            if (count($print_heads) == 8) {
                $printer_model = 'a091000002Fp7JoAAJ'; //GT-381
            }
        }
        if ($printer->printer_type == "gt341") {
            $printer_model = 'a091000002Fp7JmAAJ'; //GT-341
        }
        if ($printer->printer_type == "gt361") {
            $printer_model = 'a091000002Fp7JnAAJ'; //GT-361
        }
        if ($printer->printer_type == "gt381") {
            $printer_model = 'a091000002Fp7JoAAJ'; //GT-381
        }

        $memo = "Printer Type: " . strtoupper($printer->printer_type) . "\n";
        $memo .= config('hive.realm') === 'bma' ? "BMA: " . $claim->fault_report_no . "\n" : "BIMH: " . $claim->fault_report_no . "\n";
        $memo .= "Printer SN: " . $claim->printer_serial_no . "\n";
        $memo .= "Short Explanation: " . $claim->information . "\n";

        if (!empty($print_head_replacements)) {
            foreach ($print_head_replacements as $print_head) {
                $memo .= "PH SN: " . $print_head->original_serial_no . " (" . $print_head->color . ")" . "\n";
            }
        }

        $part_replacements = PrinterPartReplacement::where('claim_id', $claim->id)->get();
        if (!empty($part_replacements)) {
            foreach ($part_replacements as $part_replacement) {
                $memo .= "Part SN: " . $part_replacement->number . "\n";

            }
        }

        // Type of Problem
        $top = 'メカ系;ヘッド'; // Head+Mechanical
        if ($claim->replacement_type === 1) {
            $top = 'ヘッド'; // Head
        } else if ($claim->replacement_type === 2) {
            $top = 'メカ系'; // Mechanical System
        }

        $detailed_info = $memo;

//        $lastClaimForPrinter = DB::select(DB::raw('SELECT created_at FROM claims WHERE printer_serial_no = "'.$printer->printer_serial_no.'" ORDER BY created_at DESC LIMIT 1 OFFSET 1'));
        $internalCheckUser = Member::where('id', $claim->internal_check_user_id)->first();
        Forrest::authenticate();

        $body = [
            'RecordTypeId' => '01210000000kPu3AAE',
            'ServiceStatus__c' => '受付',
            'CustomerName__c' => $customer->salesforce_id,
            'AgentName__c' => $dealer->salesforce_id,
            'Acceptfrom__c' => 'ディーラー',
            'Date__c' => date('Y-m-d', strtotime($claim->dispatch_confirmed_date)),
            'Aceeptmemo__c' => $memo,
            'PreventiveMaintenanceQuantity__c' => 1,
            'RepairQuantity__c' => 1,
            'Content__c' => $memo,
            'Productmm__c' => $printer_model,
            'Dealerengineersname_handwriting__c' => 'xxx',
            'DealerengineersTELno_handwriting__c' => 'xxx',
            'LargeClassification__c' => $top, // Claim: Head: ヘッド, Mechanical System: メカ系, Head+Mechanical: メカ系;ヘッド
            'MiddleClassification__c' => $claim->problem_type,
            'MachineFaultInDetails__c' => $detailed_info,
            'ErrCode1__c' => $errorcode,
            'Photo__c' => 'Yes',
            'logdata__c' => 'Yes',
            'SolutionGT__c' => '部品送付',
            'Planning__c' => 'checked parts send new parts foc',
            'StockConfirmation__c' => true,
            'Forward_recall_defective_parts__c' => 'BIL',
            'PartsSupplier__c' => 'BIL',
            'PartsArrangeRequest__c' => true,
            'CompleteDescription__c' => true,
            'Request_approval_for_Supportplan__c' => true,
            'Request_approval_for_Closed__c' => true,
            'Recovery__c' => true,
            'Description__c' => true,
            'Sine__c' => true,
            'Confirmationofrepair__c' => true,
            'Request_approval_for_Claim_in__c' => true,
            'notification_for_Personincharge__c' => true,
            'Frequency__c' => 'マシンストップ',
            'Others__c' => 'no',
            'UsageInformationWorkingHours__c' => 'その他',
        ];

        if (config('hive.realm') === 'bma') {
            $body['OwnerId'] = "005100000098pAz"; // Cheung, Elinor
            $body['Representative__c'] = $internalCheckUser ? $internalCheckUser->salesforce_id : "005100000098pAz"; // Cheung, Elinor
        } else {
            $body['OwnerId'] = "005100000098PKs"; // André
            $body['Representative__c'] = $internalCheckUser ? $internalCheckUser->salesforce_id : "005100000098PKs"; // André
        }

        // Send notifications
        if (config('app.env') === 'production') {
            if (config('hive.realm') === 'bma') {
                $body['Approver__c'] = '00510000003QYZ9'; // Leung, Chi Wai
                $body['PartsPersonnel__c'] = '005100000098pAz'; // Cheung, Elinor
                $body['Approver_For_FOC_Parts__c'] = '00510000001jA2Q'; //Akinori NARUTO
            } else {
                $body['Approver__c'] = '00510000006VITk';
                $body['PartsPersonnel__c'] = '00510000006mgiHAAQ';
                $body['Approver_For_FOC_Parts__c'] = '00510000001jA2Q'; // Akinori NARUTO
            }
        }

        $response = Forrest::sobjects('Service__c', [
            'method' => 'post',
            'body' => $body
        ]);

        if (!empty($response["exception"])) {
            return response()->json([
                'error' => "There was an error creating the claim at Salesforce",
            ], 500);
        }

        if (!empty($response["success"]) && empty($response["errors"])) {

            $sf_claim_id = $response["id"];

            $salesforceClaim = Forrest::sobjects('Service__c/' . $sf_claim_id, [
                    'method' => 'get'
                ]
            );

            // save Salesforce Values
            Claim::where('id', $claim->id)->update([
                'salesforce_id' => $salesforceClaim['Id'],
                'r_value' => $salesforceClaim['Name'],
            ]);

            $response_detail = Forrest::sobjects('ServiceDetail__c', [
                    'method' => 'post',
                    'body' => [
                        'Service__c' => $sf_claim_id,
                        'INSTALLATIONDATE__c' => substr($printer->installation_date, 0, 10),
                        'Name' => $printer->printer_serial_no,
                        'TotalNumberOfSets__c' => $claim->number_of_prints
                    ]
                ]
            );

            if (!empty($response_detail["success"]) && empty($response_detail["errors"])) {
                $sf_detail_id = $response_detail["id"];

                // Upload Logfile
                $logfile = ClaimFile::where(['claim_id' => $claim->id, 'extension' => 'log'])->first();
                $file = $logfile->getStorageUrl();
                $filename = $logfile->name;

                $tmp_file = tmpfile();
                $tmp_location = stream_get_meta_data($tmp_file)['uri'];
                $zip = new \ZipArchive();
                $zip->open($tmp_location, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
                $zip->addFromString($filename, file_get_contents($file));
                $zip->close();

                $fcontent = file_get_contents($tmp_location);

                $response_cv = Forrest::sobjects('ContentVersion', [
                        'method' => 'post',
                        'body' => [
                            'VersionData' => base64_encode($fcontent),
                            'Origin' => 'H',
                            'ContentLocation' => 'S',
                            'FirstPublishLocationId' => (config('hive.realm') === 'bma') ? '005100000098pAz' : '005100000098PKs', // Cheung, Elinor || André
                            'PathOnClient' => 'log.zip',
                            'Description' => 'Logfile',
                        ]
                    ]
                );

                $file_id = $response_cv["id"];
                $response_cv = Forrest::query("SELECT ContentDocumentId FROM ContentVersion WHERE Id = '" . $file_id . "'");

                $cd_id = $response_cv["records"][0]["ContentDocumentId"];
                $response_cdl = Forrest::sobjects('ContentDocumentLink', [
                        'method' => 'post',
                        'body' => [
                            'ContentDocumentId' => $cd_id,
                            'LinkedEntityId' => $sf_claim_id,
                            'ShareType' => 'V'
                        ]
                    ]
                );

                // Create REPAIRHEAD and Parts Replacement if necessary
                if (!empty($print_head_replacements)) {
                    foreach ($print_head_replacements as $phr) {

                        $colors = '';
                        $broken_head_gtx = null;
                        if ($printer->printer_type == "gtx" || $printer->printer_type == "gtxpro" || $printer->printer_type == "gtxprobulk" || $printer->printer_type == "gtx600") {
                            if ($phr->color == "color") {
                                $colors = 'カラー(GTX)';
                                $broken_head_gtx = 'シアン;マゼンタ;イエロー;ブラック';
                            }
                            if ($phr->color == "white") {
                                $colors = 'ホワイト(GTX)';
                                $broken_head_gtx = 'ホワイト1;ホワイト2;ホワイト3;ホワイト4';
                            }
                        } else if ($printer->printer_type == "gt-3" || $printer->printer_type == "gt341" || $printer->printer_type == "gt361" || $printer->printer_type == "gt381") {
                            if ($phr->color == "color") {
                                if ($phr->ph_position == "c")
                                    $colors = 'シアン(GT3)';
                                if ($phr->ph_position == "m")
                                    $colors = 'マゼンタ(GT3)';
                                if ($phr->ph_position == "y")
                                    $colors = 'イエロー(GT3)';
                                if ($phr->ph_position == "k")
                                    $colors = 'ブラック(GT3)';
                            }
                            if ($phr->color == "white") {
                                if ($phr->ph_position == "w1")
                                    $colors = 'ホワイト1(GT3)';
                                if ($phr->ph_position == "w2")
                                    $colors = 'ホワイト2(GT3)';
                                if ($phr->ph_position == "w3")
                                    $colors = 'ホワイト3(GT3)';
                                if ($phr->ph_position == "w4")
                                    $colors = 'ホワイト4(GT3)';
                            }
                        }

                        $response_phr = Forrest::sobjects('RepairHead__c', [
                                'method' => 'post',
                                'body' => [
                                    'ServiceDetail__c' => $sf_detail_id,
                                    'Service__c' => $sf_claim_id,
                                    'Name' => $phr->original_serial_no,
                                    'AfterSerialNum__c' => $phr->replaced_serial_no,
                                    'Color__c' => $colors,
                                    'BrokenHead_GTX__c' => $broken_head_gtx,
                                    'ProblemDate__c' => substr($phr->replacement_date, 0, 10),
                                    'NumberOfHeadPrints__c' => $claim->number_of_prints,
                                ]
                            ]
                        );

                        if (!empty($response_phr["exception"])) {
                            $return["partialError"] .= "Could not create GT Repair Head.\n";
                            $return["partialErrorDetails"] .= $response_phr["exceptionMessage"] . print_r([
                                    'ServiceDetail__c' => $sf_detail_id,
                                    'Service__c' => $sf_claim_id,
                                    'Name' => $phr->original_serial_no,
                                    'AfterSerialNum__c' => $phr->replaced_serial_no,
                                    'Color__c' => $colors,
                                    'BrokenHead_GTX__c' => $broken_head_gtx,
                                    'ProblemDate__c' => substr($phr->replacement_date, 0, 10)
                                ], true);
                        }

                        // PartsCord__c depends on printer model
//                            $partsCord__c = '';
//                            if ($printer->printer_type == "gtx" || $printer->printer_type == "gtxpro" || $printer->printer_type == "gtxprobulk") {
//                                $partsCord__c = 'a041000002QAZVcAAP';
//                            } else if ($printer->printer_type == "gt-3" || $printer->printer_type == "gt341" || $printer->printer_type == "gt361" || $printer->printer_type == "gt381") {
//                                $partsCord__c = 'a041000002VRoAMAA1';
//                            }

                        $printerPartCord = PrinterPartCord::where([
                            'printer_type' => $printer->printer_type,
                            'color' => $phr->color
                        ])->first();
                        $partsCord__c = $printerPartCord->salesforce_id;

                        if ($partsCord__c !== "") {
                            $response_parts = Forrest::sobjects('ServiceRepairParts__c', [
                                    'method' => 'post',
                                    'body' => [
                                        'DetailOfService__c' => $sf_detail_id,
                                        "Service__c" => $sf_claim_id,
                                        'Quantity__c' => 1,
                                        'PartsCord__c' => $partsCord__c // Print head
                                    ]
                                ]
                            );

                            if (!empty($response_parts["exception"])) {
                                $return["partialError"] .= 'Could not create Repair Part: ' . $response_parts["exceptionMessage"] . "\n";
                                $return["partialErrorDetails"] .= $response_parts["exceptionMessage"] . print_r([
                                        'DetailOfService__c' => $sf_detail_id,
                                        "Service__c" => $sf_claim_id,
                                        'Quantity__c' => 1,
                                        'PartsCord__c' => $partsCord__c // Print head
                                    ], true);
                            }
                        }
                    }
                }

                // Other Parts replacements
                if (!empty($part_replacements)) {
                    foreach ($part_replacements as $rp) {
                        $response_part_sn = Forrest::query("SELECT Id FROM Parts__c WHERE UniqueName__c = '" . $rp->number . "'");
                        if (!empty($response_part_sn["records"])) {
                            $part_sn_id = $response_part_sn["records"][0]["Id"];
                            $response_parts = Forrest::sobjects('ServiceRepairParts__c', [
                                    'method' => 'post',
                                    'body' => [
                                        'DetailOfService__c' => $sf_detail_id,
                                        "Service__c" => $sf_claim_id,
                                        'Quantity__c' => $rp->quantity,
                                        'PartsCord__c' => $part_sn_id
                                    ]
                                ]
                            );
                        }
                        if (empty($response_part_sn["records"])) {
//                            $return["partialError"] .= "Could not find Part No. " . $rp->number . "\nService Repair Part not created.\n";
                            $response_parts = Forrest::sobjects('ServiceRepairParts__c', [
                                    'method' => 'post',
                                    'body' => [
                                        'DetailOfService__c' => $sf_detail_id,
                                        "Service__c" => $sf_claim_id,
                                        'Quantity__c' => $rp->quantity,
                                        'PartsCordHandwriting__c' => $rp->number,
                                        'PartsNameHandwriting__c' => $rp->name,
                                    ]
                                ]
                            );
                        }
                    }
                }

                // Upload Images
                $claim_images = ClaimFile::whereRaw('claim_id = ? AND extension != "log"', [$claim->id])->get();
                if (!empty($claim_images)) {
                    foreach ($claim_images as $claim_image) {
                        $file = $claim_image->getStorageUrl();
                        $filename = $claim_image->name;
                        $fcontent = file_get_contents($file);
                        $client_filename = $filename;

                        $response_cv = Forrest::sobjects('ContentVersion', [
                                'method' => 'post',
                                'body' => [
                                    "VersionData" => base64_encode($fcontent),
                                    'Origin' => 'H',
                                    'ContentLocation' => 'S',
                                    'FirstPublishLocationId' => (config('hive.realm') === 'bma') ? '005100000098pAz' : '005100000098PKs', // Cheung, Elinor || André
                                    'PathOnClient' => $client_filename,
                                    'Description' => 'Image',
                                ]
                            ]
                        );

                        $file_id = $response_cv["id"];
                        $response_cv = Forrest::query("SELECT ContentDocumentId FROM ContentVersion WHERE Id = '" . $file_id . "'");

                        $cd_id = $response_cv["records"][0]["ContentDocumentId"];
                        $response_cdl = Forrest::sobjects('ContentDocumentLink', [
                                'method' => 'post',
                                'body' => [
                                    'ContentDocumentId' => $cd_id,
                                    'LinkedEntityId' => $sf_claim_id,
                                    'ShareType' => 'V'
                                ]
                            ]
                        );
                    }
                }

                return response()->json([
                    'success' => "true",
                    "partialError" => $return["partialError"],
                    "partialErrorDetails" => $return["partialErrorDetails"]
                ]);
            }
            if (!empty($response_detail["errors"])) {
                return response()->json([
                    'error' => "There was an error creating the claim (Service Detail) at Salesforce.",
                ], 500);
            }
        }
        if (!empty($response["errors"])) {
            return response()->json([
                'error' => "There was an error creating the claim (Service) at Salesforce.",
            ], 500);
        }

        exit;
    }

    /**
     * @param $id
     * @return string
     */
    private
    function getFaultReportNo($id): string
    {
        return 'FR.' . date('Y') . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * @param Request $request
     * @return string
     */
    public
    function addComment($id, Request $request)
    {
        $data = json_decode($request->get('data'));

        $validator = Validator::make((array)$data, [
            'comment' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Missing required fields'
            ], 422);
        }

        $claim = Claim::where('id', $id)->firstOrFail();

        $comment = new Comment();
        $comment->claim_id = $claim->id;
        $comment->author_id = auth()->user()->id;
        $comment->content = $data->comment;

        try {
            $comment->save();
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }

//        $zendesk = new ZendeskController();
//        try {
//            $zendesk->addComment($claim->ticket_id, $data->comment);
//        } catch (Exception $e) {
//            return response()->json([
//                'error' => $e->getMessage(),
//            ], 500);
//        }

        return response()->json([
            "Comment added.",
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public
    function addPartsOrderNo($id, Request $request): JsonResponse
    {
        $data = json_decode($request->get('data'));

        $validator = Validator::make((array)$data, [
            'type' => 'required|max:255',
            'number' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Missing required fields'
            ], 422);
        }

        $partsOrderNo = $data->type . ' ' . $data->number;

        $claim = Claim::where('id', $id)->firstOrFail();
        $claim->parts_order_no = $partsOrderNo;

        try {
            $claim->save();
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }

        // send to salesforce
        if ($claim->final_check_status === 1 && $claim->salesforce_id != '') {
            try {
                Forrest::authenticate();
                $response = Forrest::sobjects('Service__c/' . $claim->salesforce_id, [
                        'method' => 'patch',
                        'body' => [
                            'PartsOrderNo__c' => $partsOrderNo,
                            'OrderDate__c' => date('Y-m-d'),
                        ]
                    ]
                );
            } catch (Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                ], 500);
            }
        }

        return response()->json([
            "Parts Order No added.",
        ]);
    }

    /**
     * Upload files for claim
     */
    public
    function uploadFiles($id, Request $request)
    {
        $claim = Claim::where('id', $id)->first();

        if ($claim && $request->file('files')) {
            foreach ($request->file('files') as $file) {
                $path = Storage::disk('s3')->putFileAs($file->getFilename(), $file, Str::random(40) . "." . $file->getClientOriginalExtension());
                $claimFile = new ClaimFile();
                $claimFile->claim_id = $claim->id;
                $claimFile->path = $path;
                $claimFile->name = $file->getClientOriginalExtension() === "log" ? $claim->printer_serial_no . '_' . $file->getClientOriginalName() : $file->getClientOriginalName();
                $claimFile->size = $file->getSize();
                $claimFile->extension = $file->getClientOriginalExtension();

                try {
                    $claimFile->save();
                } catch (Exception $e) {
                    return response()->json([
                        'error' => $e->getMessage(),
                    ], 500);
                }
            }
        }

        return response()->json([
            'claim' => $claim
        ]);
    }


    /**
     * Check status
     * @return JsonResponse
     */
    public function checkStatus(): JsonResponse
    {
        $claims = Claim::where('status', '1')->where('created_at', '<', Carbon::now()->subDays(14))->get();

        foreach ($claims as $claim) {
            $claim->engineer()->first()->notify(new UserClaimStatusNotification($claim));
        }

        return response()->json([
            'count' => count($claims)
        ]);
    }
}
