<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <?php
                
                if (strpos($_SERVER['REQUEST_URI'], 'add_members.php') !== false) {
                    $pageTitle = 'Agregar Socio';
                } elseif (strpos($_SERVER['REQUEST_URI'], 'view_type.php') !== false) {
                    $pageTitle = 'Manage Membership Types';
                }  elseif (strpos($_SERVER['REQUEST_URI'], 'renew.php') !== false) {
                    $pageTitle = 'Pago de Socios';
                } elseif (strpos($_SERVER['REQUEST_URI'], 'edit_member.php') !== false) {
                  $pageTitle = 'Edit Members';
                } elseif (strpos($_SERVER['REQUEST_URI'], 'edit_type.php') !== false) {
                  $pageTitle = 'Edit Membership Type';
                } elseif (strpos($_SERVER['REQUEST_URI'], 'list_renewal.php') !== false) {
                  $pageTitle = 'Renewal';
                } elseif (strpos($_SERVER['REQUEST_URI'], 'manage_members.php') !== false) {
                  $pageTitle = 'Manage Members';
                } elseif (strpos($_SERVER['REQUEST_URI'], 'memberProfile.php') !== false) {
                  $pageTitle = 'Member Profile';
                } elseif (strpos($_SERVER['REQUEST_URI'], 'print_membership_card.php') !== false) {
                  $pageTitle = 'Print Membership';
                } elseif (strpos($_SERVER['REQUEST_URI'], 'revenue_report.php') !== false) {
                  $pageTitle = 'Reporte de Pagos';
                } elseif (strpos($_SERVER['REQUEST_URI'], 'report.php') !== false) {
                  $pageTitle = 'Reporte de Socios';
                } elseif (strpos($_SERVER['REQUEST_URI'], 'settings.php') !== false) {
                  $pageTitle = 'Settings';
                } elseif (strpos($_SERVER['REQUEST_URI'], 'add_type.php') !== false) {
                  $pageTitle = 'Add Membership Type';
                } elseif (strpos($_SERVER['REQUEST_URI'], 'dashboard.php') !== false) {
                  $pageTitle = 'Dashboard';
                }
                elseif (strpos($_SERVER['REQUEST_URI'], 'pago.php') !== false) {
                  $pageTitle = 'Pago de Cuotas';
                }
                elseif (strpos($_SERVER['REQUEST_URI'], 'listapagos.php') !== false) {
                  $pageTitle = 'listado de pagos';
                }

                elseif (strpos($_SERVER['REQUEST_URI'], 'list_pagos.php') !== false) {
                  $pageTitle = 'listar pagos';
                }
                elseif (strpos($_SERVER['REQUEST_URI'], 'edit_member-2.php') !== false) {
                  $pageTitle = 'editar solo pagos';
                }
                elseif (strpos($_SERVER['REQUEST_URI'], '-report.php') !== false) {
                  $pageTitle = 'Reporte de Morosos';
                }

                echo '<h1 class="m-0 text-dark">' . $pageTitle . '</h1>';
                ?>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active"><?php echo $pageTitle; ?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
