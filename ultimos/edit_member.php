<?php
ini_set('display_errors', 1);
include('includes/config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SESSION['rol'] !== 'admin') {
    // Si no es admin, lo mandamos a una página de "No tienes permiso" o al inicio
    header("Location: dashboard.php?error=acceso_denegado");
    exit();
}

$response = array('success' => false, 'message' => '');

$membershipTypesQuery = "SELECT id, type, amount FROM membership_types";
$membershipTypesResult = $conn->query($membershipTypesQuery);

if (!isset($_GET['id'])) {
    header("Location: manage_members.php");
        exit();
    }
    




    $memberId = $_GET['id'];

    $fetchMemberQuery = "SELECT * FROM members WHERE id = $memberId";
    $fetchMemberResult = $conn->query($fetchMemberQuery);

    if ($fetchMemberResult->num_rows > 0) {
        $memberDetails = $fetchMemberResult->fetch_assoc();
    } 
    

function generateUniqueFileName($filename)
{
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $basename = pathinfo($filename, PATHINFO_FILENAME);
    $uniqueName = $basename . '_' . time() . '.' . $ext;
    return $uniqueName;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $contactNumber = $_POST['contactNumber'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $country = $_POST['country'];
    //$postcode = $_POST['postcode'];
    //$occupation = $_POST['occupation'];
    $dni=$_POST['dni'];
    $osocial=$_POST['osocial'];
    $afiliado=$_POST['afiliado'];
    $expirydate=$_POST['expiryDate'] ?: '-';
    $ultimomes=$_POST['ultmes'];

    $photoUpdate = "";
    $uploadedPhoto = $_FILES['photo'];

    if (!empty($uploadedPhoto['name'])) {
        $uniquePhotoName = generateUniqueFileName($uploadedPhoto['name']);
        move_uploaded_file($uploadedPhoto['tmp_name'], 'uploads/member_photos/' . $uniquePhotoName);
        $photoUpdate = ", photo='$uniquePhotoName'";
    }

    $updateQuery = "UPDATE members SET fullname='$fullname', dob='$dob', gender='$gender', contact_number='$contactNumber', email='$email', address='$address', country='$country'
                    ,dni='$dni',o_social='$osocial',AfiliadoNro='$afiliado ',expiry_date='$expirydate',ultimo_mes='$ultimomes' $photoUpdate
                    WHERE id = $memberId";

    if ($conn->query($updateQuery) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'Member updated successfully!';
        
        //header("Location: manage_members.php");
        //exit();
    } else {
        $response['message'] = 'Error: ' . $conn->error;
    }
}
?>


<?php include('includes/header.php');?>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <?php include('includes/nav.php'); ?>

    <?php include('includes/sidebar.php'); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <?php include('includes/pagetitle.php'); ?>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Info boxes -->
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">

                        <?php if ($response['success']): ?>
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h5><i class="icon fas fa-check"></i> Success</h5>
                                <?php echo $response['message']; ?>
                            </div>
                        <?php elseif (!empty($response['message'])): ?>
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h5><i class="icon fas fa-ban"></i> Error</h5>
                                <?php echo $response['message']; ?>
                            </div>
                        <?php endif; ?>

                        <!-- general form elements -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-keyboard"></i> Editar Socio</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form method="post" action="" enctype="multipart/form-data">
                            <input type="hidden" name="member_id" value="<?php echo $id; ?>">
                                <div class="card-body">
                                    <div class="row">
                                    <div class="col-sm-6">
                                    <label for="fullname">Nombre</label>
                                    <input type="text" class="form-control" id="fullname" name="fullname"
                                        placeholder="Ingrese Nombre" required value="<?php echo $memberDetails['fullname']; ?>">
                                </div>
                                        <div class="col-sm-3">
                                            <label for="dob">fecha de nacimiento</label>
                                            <input type="date" class="form-control" id="dob" name="dob" value="<?php echo $memberDetails['dob']; ?>" required>
                                        </div>
                                         
                                        <div class="col-sm-3">
                                        <label for="gender">Genero</label>
                                        <select class="form-control" id="gender" name="gender" required >
                                            <option value="Masculino" <?php echo ($memberDetails['gender'] == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                                            <option value="Femenino" <?php echo ($memberDetails['gender'] == 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
                                            <option value="Otro" <?php echo ($memberDetails['gender'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                                        </select>
                                    </div>

                                    </div>


                                    <div class="row mt-3">
                                        <div class="col-sm-6">
                                            <label for="contactNumber">Teléfono</label>
                                            <input type="tel" class="form-control" id="contactNumber"
                                                   name="contactNumber" placeholder="Ingrese Teléfono" value="<?php echo $memberDetails['contact_number']; ?>" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="email">Email</label>
                                            <input type="text" class="form-control" id="email" name="email"
                                                   placeholder="Ingrese email" value="<?php echo $memberDetails['email'] ?: '-'; ?>" >
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-sm-6">
                                            <label for="address">Direccion</label>
                                            <input type="text" class="form-control" id="address" name="address"
                                                   placeholder="Ingrese domicilio" value="<?php echo $memberDetails['address'] ?: '-'; ?>" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="country">Barrio</label>
                                            <input type="text" class="form-control" id="country" name="country"
                                                   placeholder="Ingrese Barrio" value="<?php echo $memberDetails['country'] ?: '-'; ?>" >
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-sm-6">
                                            <label for="dni">DNI</label>
                                            <input type="text" class="form-control" id="dni" name="dni"
                                                   placeholder="Ingrese DNI" value="<?php echo $memberDetails['dni'] ?: '-'; ?>" required>
                                        </div>
                                        <!--agrego columna o social-->

                                        <div class="col-sm-6">
                                            <label for="osocial">O. Social</label>
                                            <input type="text" class="form-control" id="osocial" name="osocial"
                                                   placeholder="Ingrese O. Social" value="<?php echo $memberDetails['o_social'] ?: '-'; ?> " >
                                        </div>
                                        <!-- fin agrego columna o social-->

                                        <div class="col-sm-3">
                                            <label for="afiliado">Nro Afiliado</label>
                                            <input type="text" class="form-control" id="afiliado" name="afiliado"
                                                   placeholder="Ingrese Afiliado" value="<?php echo $memberDetails['AfiliadoNro'] ?: '-'; ?>"  >
                                        </div>

                                        <div class="col-sm-3">
                                            <label for="expiryDate">ultimo pago</label>
                                            <input type="date" class="form-control" id="expiryDate" name="expiryDate"
                                                   placeholder="Ingrese Afiliado" value="<?php echo $memberDetails['expiry_date'] ?: '-'; ?>" required >
                                        </div>

                                        <div class="col-sm-3">
                                            <label for="ultmes">ultimo pago</label>
                                            <input type="text" class="form-control" id="ultmes" name="ultmes"
                                                   placeholder="Ingrese Afiliado" value="<?php echo $memberDetails['ultimo_mes'] ?: '-'; ?>"  >
                                        </div>




                                    </div>
                                    
                                    <div class="row mt-3">
                                    <div class="col-sm-6">
                                        <label for="photo">Member Photo</label>
                                        <input type="file" class="form-control" id="photo" name="photo">
                                        <small class="text-muted">Leave it blank if you don't want to change the photo.</small>
                                    </div>
                                </div>

                                </div>
                                <!-- /.card-body -->

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Grabar</button>

                                    <a href='manage_members.php' class='btn btn-primary' > <i></i> Volver</a>
                                </div>

                            </form>
                        </div>
                        <!-- /.card -->

                    </div>
                    <!--/.col (left) -->

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
<script>
document.getElementById('expiryDate').addEventListener('change', function() {
    const fechaValor = this.value; // Obtiene el valor en formato YYYY-MM-DD
    
    if (fechaValor) {
        // Creamos el objeto fecha (añadiendo hora para evitar desfases de zona horaria)
        const fecha = new Date(fechaValor + 'T00:00:00');
        
        // Opciones para formatear el mes en español
        const opciones = { month: 'short', year: 'numeric' };
        let fechaFormateada = fecha.toLocaleDateString('es-ES', opciones);
        
        // Capitalizar la primera letra (opcional)
        fechaFormateada = fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1);
        
        // Asignar al segundo campo
        document.getElementById('ultmes').value = fechaFormateada;
    }
});

</script>

<?php include('includes/footer.php'); ?>
</body>
</html>
