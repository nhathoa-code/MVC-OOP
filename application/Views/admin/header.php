<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Nhật Hòa</title>
    <link rel="icon" href="<?php echo url("client_assets/images/v.svg") ?>" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="<?php echo url("admin_assets/vendor/jquery/jquery.min.js") ?>"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <?php if(current_route() === "admin"): ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <link href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="<?php echo url("admin_assets/css/month-picker.min.css"); ?>"> 
        <link rel="stylesheet" href="<?php echo url("admin_assets/css/year-picker.css"); ?>"> 
        <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
        <script src="<?php echo url("admin_assets/js/month-picker.min.js") ?>"></script>
        <script src="<?php echo url("admin_assets/js/year-picker.js") ?>"></script>
    <?php endif; ?>
    <link href="<?php echo url("admin_assets/css/sb-admin-2.min.css") ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo url("admin_assets/css/style.css") ?>">
    <?php if(strpos(current_route(),"coupon") !== false): ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css">
    <?php endif; ?>
    <?php if(current_route() === "admin/product/add" || strpos(current_route(),"admin/product/edit") !== false || strpos(current_route(),"inventory/add") !== false || strpos(current_route(),"admin/pos") !== false): ?>
        <script src="<?php echo url("client_assets/js/notifIt.js") ?>"></script>
        <link rel="stylesheet" href="<?php echo url("client_assets/css/notifIt.css"); ?>">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <?php endif; ?>
    <script src="<?php echo url("client_assets/js/notifIt.js") ?>"></script>
    <link rel="stylesheet" href="<?php echo url("client_assets/css/notifIt.css"); ?>">    
    <script>
        const csrf_token = "<?php echo csrf_token(); ?>";
    </script>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include_once APP_PATH . "/application/Views/admin/sidebar.php"; ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav
                  style="background-color: #3c434a;position:sticky;top:0px;z-index:10" class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow py-0"
                >
                  <!-- Topbar Navbar -->
                  <ul style="justify-content:space-between;align-items:center" class="navbar-nav w-100 ml-auto py-1">
                    <?php
                          use NhatHoa\Framework\Facades\Auth;
                          $current_user = Auth::user("admin");
                    ?>
                    <li>
                      <div style="color:#fff;" class="small">
                          <?php echo $current_user->role; ?>
                      </div>
                    </li>
                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                      <a
                        class="nav-link dropdown-toggle"
                        href="#"
                        id="userDropdown"
                        role="button"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                      >
                        <span style="color:#fff" class="mr-2 d-none d-lg-inline small"
                          >Welcome, <?php echo $current_user->name ?></span
                        >
                        <img
                          class="img-profile rounded-circle"
                          src="<?php echo url("admin_assets/img/avatar.png") ?>"
                        />
                      </a>
                      <!-- Dropdown - User Information -->
                      <div
                        class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                        aria-labelledby="userDropdown"
                      >
                        <a
                          class="dropdown-item"
                          href="<?php echo url("admin/logout"); ?>"
                        >
                          <i
                            class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"
                          ></i>
                          Logout
                        </a>
                      </div>
                    </li>
                  </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">