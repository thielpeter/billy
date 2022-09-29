<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover"/>
    <title>StickyMobile BootStrap</title>
    <link rel="stylesheet" type="text/css" href="styles/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="styles/style.css">
    <link
        href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900,900i|Source+Sans+Pro:300,300i,400,400i,600,600i,700,700i,900,900i&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="fonts/css/fontawesome-all.min.css">
    <link rel="manifest" href="_manifest.json" data-pwa-version="set_in_manifest_and_pwa_js">
    <link rel="apple-touch-icon" sizes="180x180" href="app/icons/icon-192x192.png">
</head>

<body class="theme-light" data-highlight="highlight-red" data-gradient="body-default">

<div id="preloader">
    <div class="spinner-border color-highlight" role="status"></div>
</div>

<div id="page">

    <div class="header header-fixed header-logo-center">
        <a href="index.html" class="header-title">Sticky Mobile</a>
        <a href="#" data-back-button class="header-icon header-icon-1"><i class="fas fa-arrow-left"></i></a>
        <a href="#" data-toggle-theme class="header-icon header-icon-4"><i class="fas fa-lightbulb"></i></a>
    </div>

    <div id="footer-bar" class="footer-bar-1">
        <a href="index.html"><i class="fa fa-home"></i><span>Home</span></a>
        <a href="index-components.html"><i class="fa fa-star"></i><span>Features</span></a>
        <a href="index-pages.html" class="active-nav"><i class="fa fa-heart"></i><span>Pages</span></a>
        <a href="index-search.html"><i class="fa fa-search"></i><span>Search</span></a>
        <a href="#" data-menu="menu-settings"><i class="fa fa-cog"></i><span>Settings</span></a>
    </div>

    <div class="page-content header-clear-medium">

        <div class="card card-style">
            <div class="content">
                <div class="d-flex">
                    <div class="align-self-center">
                        <img src="images/preload-logo.png" class="me-4" width="50">
                    </div>
                    <div class="w-100 align-self-center">
                        <h1 class="mb-n2">Enabled</h1>
                        <p class="mb-0 font-11 opacity-60">name@domain.com</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="row mb-n2">
                <div class="col-4 pe-2">
                    <div class="card card-style mx-0 mb-3">
                        <div class="p-3">
                            <h4 class="font-700 text-uppercase font-12 opacity-50 mt-n2">Pending</h4>
                            <h1 class="font-700 font-34 color-red-dark  mb-0">29</h1>
                            <i class="fa fa-arrow-right float-end mt-n3 opacity-20"></i>
                        </div>
                    </div>
                </div>
                <div class="col-4 ps-2 pe-2">
                    <div class="card card-style mx-0 mb-3">
                        <div class="p-3">
                            <h4 class="font-700 text-uppercase font-12 opacity-50 mt-n2">Assigned</h4>
                            <h1 class="font-700 font-34 color-blue-dark mb-0">15</h1>
                            <i class="fa fa-arrow-right float-end mt-n3 opacity-20"></i>
                        </div>
                    </div>
                </div>
                <div class="col-4 ps-2">
                    <div class="card card-style mx-0 mb-3">
                        <div class="p-3">
                            <h4 class="font-700 text-uppercase font-12 opacity-50 mt-n2">Compete</h4>
                            <h1 class="font-700 font-34 color-green-dark mb-0">17</h1>
                            <i class="fa fa-arrow-right float-end mt-n3 opacity-20"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="row mb-n2">
                <div class="col-4 ps-2">
                    <div class="card card-style gradient-blue shadow-bg shadow-bg-l">
                        <div class="content">
                            <a href="#" data-menu="menu-dates">
                                <h4 class="color-white">Track Time</h4>
                                <p class="color-white">
                                    This box is a gradient. Any color can be composed to a gradient from the color packs included.
                                </p>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-4 ps-2">
                    <div class="card card-style gradient-blue shadow-bg shadow-bg-l">
                        <div class="content">
                            <a href="#" data-menu="menu-dates">
                                <h4 class="color-white">Add Project</h4>
                                <p class="color-white">
                                    This box is a gradient. Any color can be composed to a gradient from the color packs included.
                                </p>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-4 ps-2">
                    <div class="card card-style gradient-blue shadow-bg shadow-bg-l">
                        <div class="content">
                            <a href="#" data-menu="menu-dates">
                                <h4 class="color-white">Add Customer</h4>
                                <p class="color-white">
                                    This box is a gradient. Any color can be composed to a gradient from the color packs included.
                                </p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Dates-->
        <div id="menu-dates" class="menu menu-box-bottom menu-box-detached">
            <div class="menu-title"><h1>Track it </h1>
                <p class="color-theme opacity-40">Track it</p><a href="#" class="close-menu"><i class="fa fa-times"></i></a>
            </div>
            <div class="divider divider-margins mb-4"></div>
            <div class="content mt-2">
                <div class="input-style input-style-2">
                    <span class="input-style-1-active">Day</span>
                    <em><i class="fa fa-angle-down"></i></em>
                    <input type="date">
                </div>
                <div class="input-style input-style-2">
                    <span class="input-style-1-active">Time</span>
                    <em><i class="fa fa-angle-down"></i></em>
                    <input type="time">
                </div>
                <div class="input-style input-style-2">
                    <span class="input-style-1-active">Customer</span>
                    <em><i class="fa fa-angle-down"></i></em>
                    <select>
                        <option value="a" selected>Low</option>
                        <option value="b">Medium</option>
                        <option value="c">High</option>
                        <option value="d">Critical</option>
                    </select>
                </div>
                <div class="input-style input-style-2">
                    <span class="input-style-1-active">Project</span>
                    <em><i class="fa fa-angle-down"></i></em>
                    <select>
                        <option value="1">Project 1</option>
                        <option value="2" selected>Project 2</option>
                        <option value="3">Project 3</option>
                    </select>
                </div>
                <div class="input-style input-style-2">
                    <span class="input-style-1-active">Description</span>
                    <textarea></textarea>
                </div>

                <a href="#" data-menu="menu-manage"
                   class="btn btn-full btn-m rounded-sm bg-highlight shadow-xl text-uppercase font-900 mt-3 mb-3">Save</a>
            </div>
        </div>

        <div class="card card-style">
            <div class="content">
                <div class="d-flex mb-4">
                    <div class="align-self-center">
                        <span class="icon icon-xxl rounded-m me-3"><img src="images/pictures/18s.jpg" width="60"
                                                                        class="rounded-m"></span>
                    </div>
                    <div class="align-self-center w-100">
                        <h4>Project Invite <strong
                                class="badge bg-highlight color-white text-uppercase float-end font-10 mt-1">Pending</strong>
                        </h4>
                        <p class="mb-0 opacity-60 line-height-s font-12">
                            You've been invited to collaborate on <strong class="color-highlight">StickyMobile </strong>
                            by <strong class="color-theme">Administrator</strong>.
                        </p>
                    </div>
                </div>
                <div class="divider mb-2 mt-n2"></div>
                <div class="row mb-n2 text-center">
                    <div class="col-4">
                        <a href="#" class="color-green-dark text-uppercase font-800 font-11 opacity-90">Join</a>
                    </div>
                    <div class="col-4">
                        <a href="#" class="color-red-dark text-uppercase font-800 font-11 opacity-90">Reject</a>
                    </div>
                    <div class="col-4">
                        <a href="#" class="color-yellow-dark text-uppercase font-800 font-11 opacity-90">Later</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-style">
            <div class="content mb-0">
                <div class="row mb-2 mt-n2">
                    <div class="col-6 text-start">
                        <h4 class="font-700 text-uppercase font-12 opacity-50">You Projects</h4>
                    </div>
                    <div class="col-6 text-end">
                        <a href="#" class="font-12">View All</a>
                    </div>
                </div>
                <div class="divider mb-3"></div>

                <a href="#" class="d-flex mb-4">
                    <div class="align-self-center">
                        <span class="icon icon-l bg-blue-dark rounded-m color-white me-3"><i
                                class="fa fa-cog"></i></span>
                    </div>
                    <div class="align-self-center w-100">
                        <h5>Website Launch</h5>
                        <div class="progress mt-2 mb-1" style="height:3px;">
                            <div class="progress-bar border-0 bg-blue-dark text-start ps-2"
                                 role="progressbar" style="width: 40%"
                                 aria-valuenow="10" aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-6 text-start">
                                <p class="mb-n1 font-12 opacity-60">Developing</p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="mb-n1 font-12 opacity-60">2/6</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="#" class="d-flex mb-4">
                    <div class="align-self-center">
                        <span class="icon icon-l bg-green-dark rounded-m color-white me-3"><i
                                class="fa fa-power-off"></i></span>
                    </div>
                    <div class="align-self-center w-100">
                        <h5>Application Update</h5>
                        <div class="progress mt-2 mb-1" style="height:3px;">
                            <div class="progress-bar border-0 bg-green-dark text-start ps-2"
                                 role="progressbar" style="width: 100%"
                                 aria-valuenow="10" aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-6 text-start">
                                <p class="mb-n1 font-12 opacity-60">Complete</p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="mb-n1 font-12 opacity-60">10/10</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="#" class="d-flex mb-4">
                    <div class="align-self-center">
                        <span class="icon icon-l bg-red-dark rounded-m color-white me-3"><i
                                class="fa fa-server"></i></span>
                    </div>
                    <div class="align-self-center w-100">
                        <h5>Server Data Transfer</h5>
                        <div class="progress mt-2 mb-1" style="height:3px;">
                            <div class="progress-bar border-0 bg-red-dark text-start ps-2"
                                 role="progressbar" style="width: 20%"
                                 aria-valuenow="10" aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-6 text-start">
                                <p class="mb-n1 font-12 opacity-60">Canceled</p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="mb-n1 font-12 opacity-60">3/5</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="#" class="d-flex mb-4">
                    <div class="align-self-center">
                        <span class="icon icon-l bg-yellow-dark rounded-m color-white me-3"><i
                                class="fa fa-sync"></i></span>
                    </div>
                    <div class="align-self-center w-100">
                        <h5>Project Assignment</h5>
                        <div class="progress mt-2 mb-1" style="height:3px;">
                            <div class="progress-bar border-0 bg-yellow-dark text-start ps-2"
                                 role="progressbar" style="width: 70%"
                                 aria-valuenow="10" aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-6 text-start">
                                <p class="mb-n1 font-12 opacity-60">On hold</p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="mb-n1 font-12 opacity-60">16/23</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="card card-style">
            <div class="content mb-0">
                <div class="row mb-2 mt-n2">
                    <div class="col-6 text-start">
                        <h4 class="font-700 text-uppercase font-12 opacity-50">Notifications</h4>
                    </div>
                    <div class="col-6 text-end">
                        <a href="#" class="font-12">View All</a>
                    </div>
                </div>
                <div class="divider mb-3"></div>

                <a href="#" class="item">
                    <div class="d-flex mb-4">
                        <div class="pe-3">
                            <span class="icon icon-xs bg-blue-dark rounded-sm"><i class="fa fa-plus"></i></span>
                        </div>
                        <div class="align-self-center w-100">
                            <p class="line-height-s font-12 font-400">Your account has been added to <strong
                                    class="font-800">Web Design</strong> by <strong class="font-800">Admin</strong>
                                <span class="badge bg-blue-dark color-white ms-2">UPDATE</span>
                            </p>
                        </div>
                        <div class="align-self-center flex-grow-1">
                            <p class="ps-3 font-10 line-height-xs text-center opacity-40">15 sec</p>
                        </div>
                    </div>
                </a>

                <a href="#" class="item">
                    <div class="d-flex mb-4">
                        <div class="pe-3">
                            <span class="icon icon-xs bg-green-dark rounded-sm"><i class="fa fa-check"></i></span>
                        </div>
                        <div class="align-self-center w-100">
                            <p class="line-height-s font-12 font-400"><strong class="font-800">AppKit</strong> Mobile
                                update has been completed. Good job!
                                <span class="badge bg-green-dark color-white ms-2">COMPLETE</span>
                            </p>
                        </div>
                        <div class="align-self-center flex-grow-1">
                            <p class="ps-3 font-10 line-height-xs text-center opacity-40">10 min</p>
                        </div>
                    </div>
                </a>

                <a href="#" class="item">
                    <div class="d-flex mb-4">
                        <div class="pe-3">
                            <span class="icon icon-xs bg-red-dark rounded-sm"><i class="fa fa-times"></i></span>
                        </div>
                        <div class="align-self-center w-100">
                            <p class="line-height-s font-12 font-400">Mockups Rejected. Event <strong class="font-800">Emergency
                                    Meeting</strong> created by <strong class="font-800">Admin</strong>.
                                <span class="badge bg-red-dark color-white ms-2">URGENT</span>
                            </p>
                        </div>
                        <div class="align-self-center flex-grow-1">
                            <p class="ps-3 font-10 line-height-xs text-center opacity-40">10 hrs</p>
                        </div>
                    </div>
                </a>

            </div>
        </div>

        <div class="card card-style">
            <div class="content mb-0">
                <div class="row mb-2 mt-n2">
                    <div class="col-6 text-start">
                        <h4 class="font-700 text-uppercase font-12 opacity-50">INBOX</h4>
                    </div>
                    <div class="col-6 text-end">
                        <a href="#" class="font-12">View All</a>
                    </div>
                </div>
                <div class="divider mb-3"></div>

                <a href="#" class="d-flex mb-4">
                    <div class="align-self-center">
                        <img src="images/pictures/faces/1s.png" width="45" class="rounded-xl border border-s me-3">
                    </div>
                    <div class="align-self-center w-100">
                        <h5>John Doe <span class="badge ms-1 bg-highlight color-white border-0 font-10"
                                           style="transform:translateY(-2px);">ADMIN</span></h5>
                        <p class="font-500 opacity-70 mt-n2">Ping me when you get this, we need...</p>
                    </div>
                </a>
                <a href="#" class="d-flex mb-4">
                    <div class="align-self-center">
                        <img src="images/pictures/faces/4s.png" width="45" class="rounded-xl border border-s me-3">
                    </div>
                    <div class="align-self-center w-100">
                        <h5>Jack Son <span class="badge ms-1 bg-blue-dark color-white border-0 font-10"
                                           style="transform:translateY(-2px);">CLIENT</span></h5>
                        <p class="font-500 opacity-70 mt-n2">Hello, have time for a chat?</p>
                    </div>
                </a>
                <a href="#" class="d-flex mb-4">
                    <div class="align-self-center">
                        <img src="images/pictures/faces/2s.png" width="45" class="rounded-xl border border-s me-3">
                    </div>
                    <div class="align-self-center w-100">
                        <h5>Joe Markus <span class="badge ms-1 bg-green-dark color-white border-0 font-10"
                                             style="transform:translateY(-2px);">DESIGN</span></h5>
                        <p class="font-500 opacity-70 mt-n2">PSD's are ready. Wanna see them?</p>
                    </div>
                </a>

            </div>
        </div>


        <div class="footer card card-style">
            <a href="#" class="footer-title"><span class="color-highlight">StickyMobile</span></a>
            <p class="footer-text"><span>Made with <i class="fa fa-heart color-highlight font-16 ps-2 pe-2"></i> by Enabled</span><br><br>Powered
                by the best Mobile Website Developer on Envato Market. Elite Quality. Elite Products.</p>
            <div class="text-center mb-3">
                <a href="#" class="icon icon-xs rounded-sm shadow-l me-1 bg-facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="icon icon-xs rounded-sm shadow-l me-1 bg-twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" class="icon icon-xs rounded-sm shadow-l me-1 bg-phone"><i class="fa fa-phone"></i></a>
                <a href="#" data-menu="menu-share" class="icon icon-xs rounded-sm me-1 shadow-l bg-red-dark"><i
                        class="fa fa-share-alt"></i></a>
                <a href="#" class="back-to-top icon icon-xs rounded-sm shadow-l bg-dark-light"><i
                        class="fa fa-angle-up"></i></a>
            </div>
            <p class="footer-copyright">Copyright &copy; Enabled <span id="copyright-year">2017</span>. All Rights
                Reserved.</p>
            <p class="footer-links"><a href="#" class="color-highlight">Privacy Policy</a> | <a href="#"
                                                                                                class="color-highlight">Terms
                    and Conditions</a> | <a href="#" class="back-to-top color-highlight"> Back to Top</a></p>
            <div class="clear"></div>
        </div>

    </div>
    <!-- End of Page Content-->
    <!-- All Menus, Action Sheets, Modals, Notifications, Toasts, Snackbars get Placed outside the <div class="page-content"> -->


    <div id="menu-settings" class="menu menu-box-bottom menu-box-detached">
        <div class="menu-title mt-0 pt-0"><h1>Settings</h1>
            <p class="color-highlight">Flexible and Easy to Use</p><a href="#" class="close-menu"><i
                    class="fa fa-times"></i></a></div>
        <div class="divider divider-margins mb-n2"></div>
        <div class="content">
            <div class="list-group list-custom-small">
                <a href="#" data-toggle-theme data-trigger-switch="switch-dark-mode" class="pb-2 ms-n1">
                    <i class="fa font-12 fa-moon rounded-s bg-highlight color-white me-3"></i>
                    <span>Dark Mode</span>
                    <div class="custom-control scale-switch ios-switch">
                        <input data-toggle-theme type="checkbox" class="ios-input" id="switch-dark-mode">
                        <label class="custom-control-label" for="switch-dark-mode"></label>
                    </div>
                    <i class="fa fa-angle-right"></i>
                </a>
            </div>
            <div class="list-group list-custom-large">
                <a data-menu="menu-highlights" href="#">
                    <i class="fa font-14 fa-tint bg-green-dark rounded-s"></i>
                    <span>Page Highlight</span>
                    <strong>16 Colors Highlights Included</strong>
                    <span class="badge bg-highlight color-white">HOT</span>
                    <i class="fa fa-angle-right"></i>
                </a>
                <a data-menu="menu-backgrounds" href="#" class="border-0">
                    <i class="fa font-14 fa-cog bg-blue-dark rounded-s"></i>
                    <span>Background Color</span>
                    <strong>10 Page Gradients Included</strong>
                    <span class="badge bg-highlight color-white">NEW</span>
                    <i class="fa fa-angle-right"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- Menu Settings Highlights-->
    <div id="menu-highlights" class="menu menu-box-bottom menu-box-detached">
        <div class="menu-title"><h1>Highlights</h1>
            <p class="color-highlight">Any Element can have a Highlight Color</p><a href="#" class="close-menu"><i
                    class="fa fa-times"></i></a></div>
        <div class="divider divider-margins mb-n2"></div>
        <div class="content">
            <div class="highlight-changer">
                <a href="#" data-change-highlight="blue"><i class="fa fa-circle color-blue-dark"></i><span
                        class="color-blue-light">Default</span></a>
                <a href="#" data-change-highlight="red"><i class="fa fa-circle color-red-dark"></i><span
                        class="color-red-light">Red</span></a>
                <a href="#" data-change-highlight="orange"><i class="fa fa-circle color-orange-dark"></i><span
                        class="color-orange-light">Orange</span></a>
                <a href="#" data-change-highlight="pink2"><i class="fa fa-circle color-pink2-dark"></i><span
                        class="color-pink-dark">Pink</span></a>
                <a href="#" data-change-highlight="magenta"><i class="fa fa-circle color-magenta-dark"></i><span
                        class="color-magenta-light">Purple</span></a>
                <a href="#" data-change-highlight="aqua"><i class="fa fa-circle color-aqua-dark"></i><span
                        class="color-aqua-light">Aqua</span></a>
                <a href="#" data-change-highlight="teal"><i class="fa fa-circle color-teal-dark"></i><span
                        class="color-teal-light">Teal</span></a>
                <a href="#" data-change-highlight="mint"><i class="fa fa-circle color-mint-dark"></i><span
                        class="color-mint-light">Mint</span></a>
                <a href="#" data-change-highlight="green"><i class="fa fa-circle color-green-light"></i><span
                        class="color-green-light">Green</span></a>
                <a href="#" data-change-highlight="grass"><i class="fa fa-circle color-green-dark"></i><span
                        class="color-green-dark">Grass</span></a>
                <a href="#" data-change-highlight="sunny"><i class="fa fa-circle color-yellow-light"></i><span
                        class="color-yellow-light">Sunny</span></a>
                <a href="#" data-change-highlight="yellow"><i class="fa fa-circle color-yellow-dark"></i><span
                        class="color-yellow-light">Goldish</span></a>
                <a href="#" data-change-highlight="brown"><i class="fa fa-circle color-brown-dark"></i><span
                        class="color-brown-light">Wood</span></a>
                <a href="#" data-change-highlight="night"><i class="fa fa-circle color-dark-dark"></i><span
                        class="color-dark-light">Night</span></a>
                <a href="#" data-change-highlight="dark"><i class="fa fa-circle color-dark-light"></i><span
                        class="color-dark-light">Dark</span></a>
                <div class="clearfix"></div>
            </div>
            <a href="#" data-menu="menu-settings"
               class="mb-3 btn btn-full btn-m rounded-sm bg-highlight shadow-xl text-uppercase font-900 mt-4">Back to
                Settings</a>
        </div>
    </div>
    <!-- Menu Settings Backgrounds-->
    <div id="menu-backgrounds" class="menu menu-box-bottom menu-box-detached">
        <div class="menu-title"><h1>Backgrounds</h1>
            <p class="color-highlight">Change Page Color Behind Content Boxes</p><a href="#" class="close-menu"><i
                    class="fa fa-times"></i></a></div>
        <div class="divider divider-margins mb-n2"></div>
        <div class="content">
            <div class="background-changer">
                <a href="#" data-change-background="default"><i class="bg-theme"></i><span class="color-dark-dark">Default</span></a>
                <a href="#" data-change-background="plum"><i class="body-plum"></i><span
                        class="color-plum-dark">Plum</span></a>
                <a href="#" data-change-background="magenta"><i class="body-magenta"></i><span class="color-dark-dark">Magenta</span></a>
                <a href="#" data-change-background="dark"><i class="body-dark"></i><span
                        class="color-dark-dark">Dark</span></a>
                <a href="#" data-change-background="violet"><i class="body-violet"></i><span class="color-violet-dark">Violet</span></a>
                <a href="#" data-change-background="red"><i class="body-red"></i><span class="color-red-dark">Red</span></a>
                <a href="#" data-change-background="green"><i class="body-green"></i><span class="color-green-dark">Green</span></a>
                <a href="#" data-change-background="sky"><i class="body-sky"></i><span class="color-sky-dark">Sky</span></a>
                <a href="#" data-change-background="orange"><i class="body-orange"></i><span class="color-orange-dark">Orange</span></a>
                <a href="#" data-change-background="yellow"><i class="body-yellow"></i><span class="color-yellow-dark">Yellow</span></a>
                <div class="clearfix"></div>
            </div>
            <a href="#" data-menu="menu-settings"
               class="mb-3 btn btn-full btn-m rounded-sm bg-highlight shadow-xl text-uppercase font-900 mt-4">Back to
                Settings</a>
        </div>
    </div>
    <!-- Menu Share -->
    <div id="menu-share" class="menu menu-box-bottom menu-box-detached">
        <div class="menu-title mt-n1"><h1>Share the Love</h1>
            <p class="color-highlight">Just Tap the Social Icon. We'll add the Link</p><a href="#" class="close-menu"><i
                    class="fa fa-times"></i></a></div>
        <div class="content mb-0">
            <div class="divider mb-0"></div>
            <div class="list-group list-custom-small list-icon-0">
                <a href="auto_generated" class="shareToFacebook external-link">
                    <i class="font-18 fab fa-facebook-square color-facebook"></i>
                    <span class="font-13">Facebook</span>
                    <i class="fa fa-angle-right"></i>
                </a>
                <a href="auto_generated" class="shareToTwitter external-link">
                    <i class="font-18 fab fa-twitter-square color-twitter"></i>
                    <span class="font-13">Twitter</span>
                    <i class="fa fa-angle-right"></i>
                </a>
                <a href="auto_generated" class="shareToLinkedIn external-link">
                    <i class="font-18 fab fa-linkedin color-linkedin"></i>
                    <span class="font-13">LinkedIn</span>
                    <i class="fa fa-angle-right"></i>
                </a>
                <a href="auto_generated" class="shareToWhatsApp external-link">
                    <i class="font-18 fab fa-whatsapp-square color-whatsapp"></i>
                    <span class="font-13">WhatsApp</span>
                    <i class="fa fa-angle-right"></i>
                </a>
                <a href="auto_generated" class="shareToMail external-link border-0">
                    <i class="font-18 fa fa-envelope-square color-mail"></i>
                    <span class="font-13">Email</span>
                    <i class="fa fa-angle-right"></i>
                </a>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript" src="scripts/bootstrap.min.js"></script>
<script type="text/javascript" src="scripts/custom.js"></script>
</body>
<?php /**PATH /var/www/html/billy/backend/resources/views/welcome.blade.php ENDPATH**/ ?>