<?php
include('includes/config.php');

$selectQuery = "SELECT * FROM members WHERE membership_type =1";
$result = $conn->query($selectQuery);

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}


?>

<?php include('includes/header.php');?>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
  <?php include('includes/nav.php');?>

 <?php include('includes/sidebar.php');?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    
  <?php include('includes/pagetitle.php');?>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Info boxes -->
        <!-- Visit codeastro.com for more projects -->
        <div class="row">
        
        <div class="col-12">

        <div class="card">
    <div class="card-header">
        <h3 class="card-title">Tabla de Socios</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
    <table id="example1" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Socio Nro</th>
                <th>Fullname</th>
                <th>Contact</th>
                <th>Dni</th>
                <th>Type</th>
                <th>fecha ult pago</th>
                <th>mes pagado</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $counter = 1;
            while ($row = $result->fetch_assoc()) {
                $expiryDate = strtotime($row['expiry_date']);
                $currentDate = time();
                $daysDifference = floor(($expiryDate - $currentDate) / (60 * 60 * 24));

                $membershipStatus = ($daysDifference < 0) ? 'Expired' : 'Active';
                if ($membershipStatus === 'Expired') {
                  $badgeClass = 'badge-danger';
                } elseif ($membershipStatus === 'Active') {
                    $badgeClass = 'badge-success';
                } else {
                    $badgeClass = 'badge-secondary';
                }
//extrae valores asociados a tabla membership_type

                $membershipTypeId = $row['membership_type'];
                $membershipTypeQuery = "SELECT type FROM membership_types WHERE id = $membershipTypeId";
                $membershipTypeResult = $conn->query($membershipTypeQuery);
                $membershipTypeRow = $membershipTypeResult->fetch_assoc();
                $membershipTypeName = ($membershipTypeRow) ? $membershipTypeRow['type'] : 'Unknown';

                $expiryDate = strtotime($row['expiry_date']);
                $daysRemaining = floor(($expiryDate - $currentDate) / (60 * 60 * 24));

                echo "<tr>";
                echo "<td>{$row['membership_number']}</td>";
                echo "<td>{$row['fullname']}</td>";
                echo "<td>{$row['contact_number']}</td>";
                echo "<td>{$row['dni']}</td>";
                echo "<td>{$membershipTypeName}</td>";

                if ($row['fecha_ult_pago'] === NULL) {
                  echo "<td>NONE</td>";
              } else {
                  $expiryDate = new DateTime($row['fecha_ult_pago']);
                  $currentDate = new DateTime();
              
                  $daysRemaining = $currentDate->diff($expiryDate)->days;

              
                  echo "<td>{$row['fecha_ult_pago']}<br><small>{$daysRemaining} days remaining</small></td>";
              } 

              //echo "<td><span class='badge $badgeClass'>$membershipStatus</span></td>";
              echo "<td>{$row['ultimo_mes']}</td>";


                echo "<td>
                <a href='pago.php?id={$row['id']}' class='btn btn-success'>pago</a>
                <a href='listapagos.php?id={$row['id']}' class='btn btn-success'>listar</a>
                    </td>";
                echo "</tr>";

                $counter++;

            }
            ?>
        </tbody>
    </table>
</div>

    <!-- /.card-body -->
</div>
<!-- Visit codeastro.com for more projects -->
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
   var table = $("#example1").DataTable({
      "responsive": true,
      "autoWidth": false,
    });

    $('div.dataTables_filter input', table.table().container()).focus();
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
  
//var table = $('#example1').DataTable();
 
//$('div.dataTables_filter input', table.table().container()).focus();


</script>

</body>
</html>