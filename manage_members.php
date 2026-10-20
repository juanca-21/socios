<?php
include('includes/config.php');

$selectQuery = "SELECT * FROM members ORDER BY created_at DESC";
$result = $conn->query($selectQuery);


if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SESSION['rol'] !== 'admin') {
    // Si no es admin, lo mandamos a una página de "No tienes permiso" o al inicio
    header("Location: dashboard.php?error=acceso_denegado");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $membershipType = $_POST['membershipType'];
    $membershipAmount = $_POST['membershipAmount'];

    $insertQuery = "INSERT INTO membership_types (type, amount) VALUES ('$membershipType', $membershipAmount)";
    
    if ($conn->query($insertQuery) === TRUE) {
        $successMessage = 'Membership type added successfully!';
        // header("Location: dashboard.php");
        // exit();
    } else {
        echo "Error: " . $insertQuery . "<br>" . $conn->error;
    }
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
        <div class="row">
        
        <div class="col-12">

        <div class="card">
    <div class="card-header">
        <h3 class="card-title">Members DataTable</h3>
    </div>
    <!-- Visit codeastro.com for more projects -->
    <!-- /.card-header -->
    <div class="card-body">
    <table id="example1" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Fullname</th>
                <th>Contact</th>
                <th>Dni</th>
                <th>Address</th>
                <th>Type</th>
                <th>Estado</th>
                <th>ultimo pago</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $counter = 1;
            while ($row = $result->fetch_assoc()) {
                
                $expiryDate = strtotime($row['fecha_ult_pago']);
                $currentDate = time();
                $daysDifference = floor(($expiryDate - $currentDate) / (60 * 60 * 24));
                $fechapaga = (strtotime($row['expiry_date']));
                $hoy=date('Y-m');

               //if (strtotime($row['expiry_date']) >= $currentDate) 
                            
               if (date('Y-m', strtotime($row['expiry_date'])) >= date('Y-m', strtotime('now')))
                                {


                    $membershipStatus = 'Al día';
                    $claseCSS='al-dia';
                }

                else {
                    $membershipStatus = ($daysDifference > -30) ? 'Al dia' : 'Atrasado';
                    //$membershipStatus = 'Al día';
                     // code...
                    $claseCSS = ($daysDifference > -30) ? 'al-dia' : 'vencido';

                

                 }

                 //$membershipStatus = ($daysDifference < -30) ? 'Atrasado' : 'Al día';
            


                



                $membershipTypeId = $row['membership_type'];
                $membershipTypeQuery = "SELECT type FROM membership_types WHERE id = $membershipTypeId";
                $membershipTypeResult = $conn->query($membershipTypeQuery);
                $membershipTypeRow = $membershipTypeResult->fetch_assoc();
                $membershipTypeName = ($membershipTypeRow) ? $membershipTypeRow['type'] : 'Unknown';

                echo "<tr>";
                echo "<td>{$row['membership_number']}</td>";
                echo "<td>{$row['fullname']}</td>";
                echo "<td>{$row['contact_number']}</td>";
                echo "<td>{$row['dni']}</td>";
                echo "<td>{$row['address']}</td>";
                echo "<td>{$membershipTypeName}</td>";
                echo "<td class = $claseCSS >{$membershipStatus}</td>";
                echo "<td>{$row['ultimo_mes']}</td>";

                echo "<td>";

                /*if (!empty($row['expiry_date'])) {
                    echo "<a href='memberProfile.php?id={$row['id']}' class='btn btn-info'><i class='fas fa-id-card'></i></a>";
                }*/

                echo "
                    <a href='edit_member.php?id={$row['id']}' class='btn btn-primary' title='Editar información del socio'><i class='fas fa-edit'></i>Editar</a>
                    <a href='listapagos.php?id={$row['id']}' class='btn btn-primary' title='Listar Pagos'><i class='fas fa-list'></i> Pagos</a>
                    <button class='btn btn-danger' onclick='deleteMember({$row['id']})'title='Eliminar Socio'><i class='fas fa-trash'></i>Borrar</button>
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
    function deleteMember(id) {
        if (confirm("Esta seguro de querer eliminar este socio?")) {
            window.location.href = 'delete_members.php?id=' + id;
        }
    }
</script>

</body>
</html>