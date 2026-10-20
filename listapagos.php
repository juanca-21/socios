<?php

ini_set('display_errors', 1);
include('includes/config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$response = array('success' => false, 'message' => '');

if (isset($_GET['id'])) {
    $memberId = $_GET['id'];

    echo($memberId);

    $fetchPagosQuery = "SELECT * FROM renew WHERE member_id = $memberId";
    $fetchPagosResult = $conn->query($fetchPagosQuery);

    if ($fetchPagosResult->num_rows > 0) {
        //$pagosDetails = $fetchPagosResult->fetch_assoc();
    } else {
        header("Location: list_pagos.php");
        exit();
    }
}

include('includes/header.php');
?>
<?php
function getTotalRevenueWithCurrency()
{
    global $conn;
    global $memberId;

    $currencyQuery = "SELECT currency FROM settings LIMIT 1";
    $currencyResult = $conn->query($currencyQuery);

    if ($currencyResult->num_rows > 0) {
        $currencyRow = $currencyResult->fetch_assoc();
        $currencySymbol = $currencyRow['currency'];
    } else {
        $currencySymbol = '$'; // Default currency symbol (you can change this as needed)
    }

    $totalRevenueQuery = "SELECT SUM(total_amount) AS totalRevenue FROM renew WHERE member_id = $memberId";
    $totalRevenueResult = $conn->query($totalRevenueQuery);

    if ($totalRevenueResult->num_rows > 0) {
        $totalRevenueRow = $totalRevenueResult->fetch_assoc();
        $totalRevenue = $totalRevenueRow['totalRevenue'];
    } else {
        $totalRevenue = 0;
    }

    return $currencySymbol . number_format($totalRevenue, 2);
}
?>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
  <?php include('includes/nav.php');?>

 <?php include('includes/sidebar.php');?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    
  <?php include('includes/pagetitle.php');?>
  			<?php
  				$membershipnombreQuery = "SELECT fullname FROM members WHERE id = $memberId";
                $membershipnombreResult = $conn->query($membershipnombreQuery);
                $membershipnombreRow = $membershipnombreResult->fetch_assoc();
                $membershipnombreName = ($membershipnombreRow) ? $membershipnombreRow['fullname'] : 'Unknown';
           	?>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Info boxes -->
        <div class="row">
        
        <div class="col-12">

        <div class="card">
    <div class="card-header">
        <h3 class="card-title">Pagos Realizados por   <?php echo($membershipnombreName)?></h3>
        <h1><?php echo("        monto pagado:  " . getTotalRevenueWithCurrency());?></h1>
    </div>
    <!-- Visit codeastro.com for more projects -->
    <!-- /.card-header -->
    <div class="card-body">
    <table id="example1" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Mes</th>
                <th>Forma de pago</th>
                <th>Monto</th>
                <th>Comprobante</th>
                <th>Acciones</th>
                
            </tr>
        </thead>
        <tbody>
        	<tr>
        	<td><?php echo($memberId)?></td> 
        	<tr>

            <?php

            $counter = 0;
            while ($row = $fetchPagosResult->fetch_assoc()) {
                $añomes = $row['renew_date'];
                //echo $añomes;
               $ff = date("d-M-Y", strtotime($añomes)); 
                //$fetchPagosResult->fetch_assoc()
                echo "<tr>";
                //echo "<td>{$row['renew_date']}</td>";
                echo "<td> $ff </td>";
                $f_pago = date('M-Y',strtotime($row['mes']));
                //echo "<td>{$row['mes']}</td>";
                echo "<td>$f_pago</td>";
                echo "<td>{$row['modo_de_pago']}</td>";
                echo "<td>{$row['total_amount']}</td>";
                echo "<td>{$row['comprobante']}</td>";
                echo "<td>";
                echo "<a href='imprimo_pago.php?id={$row['id']}' class='btn btn-primary' > <i class='fa fa-circle' ></i> Imprimir</a>";
                echo "</td>";
                echo "</tr>";

                
            }
			

            $counter++;
            
            ?>
            
            
        </tbody>
    </table>
</div>

    <!-- /.card-body -->
</div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

        </div>
        <!-- /.row -->

        
      </div><!--/. container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <!-- Visit codeastro.com for more projects -->
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <strong> &copy; <?php echo date('Y');?> codeastro.com</a> -</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Developed By</b> <a href="https://codeastro.com/">CodeAstro</a>
    </div>
  </footer>
</div>
<!-- ./wrapper -->

<?php include('includes/footer.php');?>
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true,
      "autoWidth": false,
    });
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>

<script>
    function deleteMember(id) {
        if (confirm("Are you sure you want to delete this member?")) {
            window.location.href = 'delete_members.php?id=' + id;
        }
    }
</script>

</body>
</html>