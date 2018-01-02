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
    <link rel="icon" type="image/png" href="http://www.w3.org/2000/svg">
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
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <link href="//cdn.datatables.net/tabletools/2.2.3/css/dataTables.tableTools.css" rel="stylesheet" type="text/css" />
    <link href="//cdn.datatables.net/plug-ins/725b2a2115b/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="//editor.datatables.net/examples/resources/bootstrap/editor.bootstrap.css" rel="stylesheet" type="text/css" />
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
                <span class="logo-lg"><b>UMassMed</b>Biocore</span>
            </a>
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
                    session_start(); 
                    $ownerID = isset($_SESSION['ownerID']) ? $_SESSION['ownerID'] : "";
                    echo '$user: '.$ownerID;
                if ($ownerID != ''){
                    $np = 1;
                    include("php/sidebarmenu.php");
                    
                }
                    include("php/funcs.php");

                ?>

        </aside>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Biocore Pipeline Generation
                    <small><?php print getTitle($np); ?></small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href=""><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href=""></a>NGS Pipeline</li>
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
                        <div class="box-body table-responsive" style=" overflow-y:scroll;">
                            
                            <?php print getPage($np); 
                            
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

        <script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>

        <?php print getJS($np); ?>
</body>

</html>