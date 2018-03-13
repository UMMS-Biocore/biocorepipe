<?php
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$ownerID = isset($_SESSION['ownerID']) ? $_SESSION['ownerID'] : "";
if ($ownerID != ''){$login = 1;} 
else { $login = 0;}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>UMass Med Biocore Pipeline Builder</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!--   app’s client ID prodcued in the Google Developers Console-->
    <meta name="google-signin-client_id" content="1051324819082-6mjdouf9dhmhv9ov5vvdkdknqrb8tont.apps.googleusercontent.com">
    <!--    google icon-->
    <link rel="icon" type="image/png" href="https://www.w3.org/2000/svg">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
    <!-- Morris chart -->
    <link rel="stylesheet" href="bower_components/morris.js/morris.css">
    <!-- jvectormap -->
    <link rel="stylesheet" href="bower_components/jvectormap/jquery-jvectormap.css">
    <!-- Date Picker -->
    <link rel="stylesheet" href="bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="bower_components/bootstrap-daterangepicker/daterangepicker.css">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
    <!-- selectize style -->
    <link rel="stylesheet" href="css/selectize.bootstrap3.css">
    <!-- feedback modal style -->
    <link rel="stylesheet" href="css/feedback.css">
    <!--    bigger fonts-->
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <link href="//cdn.datatables.net/tabletools/2.2.3/css/dataTables.tableTools.css" rel="stylesheet" type="text/css" />
    <link href="//cdn.datatables.net/select/1.2.4/css/select.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="//cdn.datatables.net/plug-ins/725b2a2115b/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="//editor.datatables.net/examples/resources/bootstrap/editor.bootstrap.css" rel="stylesheet" type="text/css" />
    <!--    dataTables.checkboxes-->
    <link type="text/css" href="css/dataTables.checkboxes.css" rel="stylesheet" />
    <!--    pagination-->
    <link type="text/css" href="css/pagination.css" rel="stylesheet" />
    <!-- jQuery 3 -->
    <script src="bower_components/jquery/dist/jquery.min.js"></script>

    <style>
        /* Ace Editor scroll problem fix */

        .ace_text-input {
            position: absolute!important
        }

        /*glyphicon-stack    */

        .glyphicon-stack {
            position: relative;
        }

        .glyphicon-stack-2x {
            position: absolute;
            left: 14px;
            top: -5px;
            font-size: 10px;
            text-align: center;
        }

        /*Pipeline Name Dynamic Input Box */

        .width-dynamic {
            padding: 5px;
            font-size: 20px;
            font-family: Sans-serif;
            white-space: pre;
        }

        .box-dynamic:hover {
            border: 1px solid lightgrey;
        }

        .box-dynamic {
            border: 1px solid transparent;
        }

        /*Combobox Menu*/

        .selectize-control .option .title {
            display: block;
        }

        .selectize-control .option .url {
            font-size: 12px;
            display: block;
            color: #a0a0a0;
        }

        .selectize-dropdown {
            width: 350px !important;
        }

        /*    D3 tooltip*/

        div.tooltip-svg {
            position: absolute;
            text-align: left;
            padding: 2px;
            font: 14px sans-serif;
            background: lightsteelblue;
            border: 0px;
            border-radius: 8px;
            pointer-events: none;
            font-color: black;
        }

        /*    NavBar process details*/

        .nav-tabs {
            background-color: #F9F9F9 !important;
            color: #428bca;
            font-weight: 600;

        }

        .nav-tabs>li>a {
            border: medium none;
        }

        .nav-tabs>li>a:hover {
            border: medium none;
            border-radius: 0;
            color: #0570c1;
        }

        .active a {
            color: #428bca !important;
        }

        /*        table links should appear blue*/

        #projecttable a,
        #runtable a,
        #allpipelinestable a {
            color: #0570c1;
        }

        #projecttable a:hover,
        #runtable a:hover,
        #allpipelinestable a:hover {
            color: #428bca !important;
            text-decoration: underline;
        }

        /*        public pipelines page*/

        .boxheader {
            font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            padding: 1% 0;
            border-bottom: 2px solid #eee;
            height: 60px !important;
        }

        .widget-user-header {
            height: 100px !important;
        }

        .box-body {
            font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            padding: 20px;
            padding-top: 5px;
        }

        .movebox {
            min-width: 100%;
            min-height: 100%;
            margin-bottom: 10px;
            border: 2px solid #dee2e8;
            position: relative;
            display: inline-block;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease-in-out;
        }

        .movebox::after {
            position: absolute;
            z-index: -1;
            opacity: 0;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: opacity 0.3s ease-in-out;
        }

        /* Scale up the box */

        .movebox:hover {
            box-shadow: 0 0 20px rgba(33, 33, 33, .2);
        }

        /* Fade in the pseudo-element with the bigger shadow */

        .movebox:hover::after {
            opacity: 1;
        }

        /* In order to fix textarea width*/
        textarea {
            resize: vertical;
        }
/*        Make center the pagination numbers*/
        .paginationjs {
            display: flex;
            justify-content: center;
        }

    </style>

</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

        <header class="main-header">
            <!-- Logo -->
            <a href="index.php" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><b>U</b>Bio</span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg" style="font-size:17px;"><b>Biocore</b> DolphinNext<b><sub> BETA</sub></b></span>
            </a>

            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top">
                <div class="collapse navbar-collapse pull-left">
                    <ul class="nav navbar-nav">
                        <li><a href="index.php?np=2">Projects </a></li>
                        <li><a href="index.php?np=1">Pipelines </a></li>
                        <!-- <li><a href="#"><i class="fa fa-bell-o"></i></a></li>-->
                        <?php
                        include("php/funcs.php");
                        $np = isset($_REQUEST["np"]) ? $_REQUEST["np"] : "";
                        $id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : "";
                        ?>
                    </ul>
                </div>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li id="manageAmz" style="display:none">
                            <a href="#amzModal" data-toggle="modal">Amazon
                            <!--              <i class="fa fa-amazon"></i>-->
                            <small id="amzAmount" style="display:none" class="label pull-right bg-green"></small>
                            </a>
                        </li>
                        <?php
                        if ($login == 1){
                            echo '<li><a href="index.php?np=4">Profiles </a></li>';
                        }
                        ?>
                        <li><a href="http://dolphinnext.readthedocs.io/" target="_blank"><i class="fa fa-mortar-board"></i></a></li>
                        <li> <a><b style="color:#7c1842;"> BETA VERSION </b> </a></li>


                    </ul>
                </div>
            </nav>
        </header>



        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel" style="padding-bottom:5px;">
                    <div id="googleSignIn" class="g-signin2" data-longtitle="true" data-onsuccess="Google_signIn" data-theme="dark" data-width="200"></div>
                    <div id="userAvatar" style="display:none" class="pull-left image">
                        <img id="userAvatarImg" src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                    </div>
                    <div id="userInfo" style="display:none" class="info">
                        <p id="userName">Alper Kucukural</p>
                        <span style="font-size:11px;"><i class="fa fa-circle text-success"></i> Online</span>
                        <a style="padding-left:5px; font-size:11px; float:right;" href="#" onclick="signOut();">Sign out</a>
                    </div>
                </div>

                <!-- search form -->
                <form action="#" method="get" class="sidebar-form" autocomplete="off">
                    <div class="input-group">
                        <input type="text" id="tags" name="q" class="form-control" placeholder="Search..." />
                        <span class="input-group-btn">
				<button type='button' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
			</span>
                    </div>
                </form>
                <!-- /.search form -->
                <!-- sidebar menu: : style can be found in sidebar.less -->
                <?php
                    print getSidebarMenu($np, $login);
                ?>

        </aside>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Biocore
                    <?php print getTitle($np); ?> Generation

                </h1>
                <ol class="breadcrumb">
                    <li><a href=""><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href=""></a>Biocore</li>
                    <li class="active">
                        <?php print getTitle($np); ?>
                    </li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="box">

                        <!--/.box-header -->
                        <div class="box-body table-responsive" style="min-height:90vh; overflow-y:scroll;">

                            <?php print getPage($np, $login, $id); 
                            
                            ?>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
            </aside>
            <!-- /.control-sidebar -->
            <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
            <div class="control-sidebar-bg"></div>
        </div>
        <!-- ./wrapper -->
        <!--        feedback modal-->
        <div id="feedback">
            <div id="feedback-form" style='display:none;' class="col-xs-4 col-md-4 panel panel-default">
                <form method="POST" action="/feedback" class="form panel-body" role="form">
                    <div class="form-group">
                        <input class="form-control" name="email" autofocus placeholder="Your e-mail" type="email" />
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" name="message" required placeholder="Please write your feedback here..." rows="5"></textarea>
                    </div>
                    <button class="btn btn-primary pull-right" type="submit">Send</button>
                </form>
            </div>
            <div id="feedback-tab">Feedback</div>
        </div>
        <!--        feedback modal ends-->

        <!-- Add Amazon Modal Starts-->
        <div id="amzModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Amazon Management Console</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal">
                            <div class="panel panel-default">
                                <div>
                                    </br>
                                    <table id="amzTable" class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Profile Name</th>
                                                <th scope="col">Details</th>
                                                <th style="width:250px;" scope="col">Status</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add Amazon Modal Ends-->

        <!-- Add Amazon Node Modal Starts-->
        <div id="addAmzNodeModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Configuration</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal">
                            <div class="form-group" style="display:none">
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="profileID" name="id">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="numNodes" class="col-sm-3 control-label">Nodes</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="numNodes" required name="nodes" placeholder="Enter the number of nodes">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Use Autoscale</label>
                                <div class="col-sm-9">
                                    <input type="checkbox" id="autoscale_check" name="autoscale_check" data-toggle="collapse" data-target="#autoscaleDiv">
                                </div>
                            </div>
                            <div id="autoscaleDiv" class="collapse">
                                <div class="form-group row">
                                    <label for="autoscale_maxIns" class="col-sm-3 control-label">Maximum instances <span><a data-toggle="tooltip" data-placement="bottom" title="Maximum number of instances on the cluster"><i class='glyphicon glyphicon-info-sign'></i></a></span></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="autoscale_maxIns" name="autoscale_maxIns" placeholder="Enter the number of maximum instances">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="activateAmz">Activate Cluster</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add Amazon Node Modal Ends-->
        
 



        <!--Google Platform Library on your web pages that integrate Google Sign-In-->
        <script src="https://apis.google.com/js/platform.js" async defer></script>
        <!-- jQuery 3 -->
        <script src="bower_components/jquery/dist/jquery.min.js"></script>
        <!-- jQuery UI 1.11.4 -->
        <script src="bower_components/jquery-ui/jquery-ui.min.js"></script>
        <!-- Selectize 0.12.4.  -->
        <script src="dist/selectize/selectize.js"></script>
        <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
        <script>
            $.widget.bridge('uibutton', $.ui.button);

        </script>
        <!-- Bootstrap 3.3.7 -->
        <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <!-- Morris.js charts -->
        <script src="bower_components/raphael/raphael.min.js"></script>

        <!-- Sparkline -->
        <script src="bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
        <!-- jvectormap -->
        <script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
        <script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
        <!-- jQuery Knob Chart -->
        <script src="bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
        <!-- daterangepicker -->
        <script src="bower_components/moment/min/moment.min.js"></script>
        <script src="bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
        <!-- datepicker -->
        <script src="bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <!-- Bootstrap WYSIHTML5 -->
        <script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
        <!-- Slimscroll -->
        <script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <!-- FastClick -->
        <script src="bower_components/fastclick/lib/fastclick.js"></script>
        <!-- AdminLTE App -->
        <script src="dist/js/adminlte.min.js"></script>

        <!-- AdminLTE for demo purposes -->
        <script src="dist/js/demo.js"></script>
        <!-- pagination 2.1.2 -->
        <script src="dist/js/pagination.min.js"></script>

        <script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>

        <?php print getJS($np, $login, $id); ?>


</body>

</html>
