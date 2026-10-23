//modificaddo

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

function generateUniqueFileName($originalName) {
    $timestamp = time();
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $uniqueName = $timestamp . '_' . uniqid() . '.' . $extension;
    return $uniqueName;
}



$sql = "SELECT MAX(membership_number) AS ultimo_socio FROM members";
    $ult_socio = $conn->query($sql);
    if ($row = $ult_socio->fetch_assoc()) {
    echo "El ultimo socio es: " . $row['ultimo_socio'];
    $membershipNumber = $row['ultimo_socio'] + 1;
    }




if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $contactNumber = $_POST['contactNumber'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $country = $_POST['country'];
    $postcode = $_POST['postcode'];
    //$occupation = $_POST['occupation'];
    $membershipType = $_POST['membershipType'];
    $dni=$_POST['dni'];
    $osocial=$_POST['osocial'];
    $afiliado=$_POST['afiliado'];


    //$membershipNumber = 'CA-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
    //busco ultimo socio y le sumo 1
    $sql = "SELECT MAX(membership_number) AS ultimo_socio FROM members";
    $ult_socio = $conn->query($sql);
    if ($row = $ult_socio->fetch_assoc()) {
    echo "El ultimo socio es: " . $row['ultimo_socio'];
    $membershipNumber = $row['ultimo_socio'] + 1;
    
                                            }

    //fin ultimo socio



    if (!empty($_FILES['photo']['name'])) {
        $uploadedPhoto = $_FILES['photo'];
        $uniquePhotoName = generateUniqueFileName($uploadedPhoto['name']);

        move_uploaded_file($uploadedPhoto['tmp_name'], 'uploads/member_photos/' . $uniquePhotoName);
        } else {
            $uniquePhotoName = 'default.jpg';
        }

    $insertQuery = "INSERT INTO members (fullname, dob, gender, contact_number, email, address, country, postcode, 
                    membership_type, membership_number, photo, created_at,dni,o_social,AfiliadoNro) 
                    VALUES ('$fullname', '$dob', '$gender', '$contactNumber', '$email', '$address', '$country', '$postcode', 
                            '$membershipType', '$membershipNumber', '$uniquePhotoName', NOW(),dni='$dni',o_social='$osocial',AfiliadoNro='$afiliado ')";

    if ($conn->query($insertQuery) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'Member added successfully! Membership Number: ' . $membershipNumber;
    } else {
        $response['message'] = 'Error: ' . $conn->error;
    }
}
// Visit codeastro.com for more projects
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
                        <!-- Visit codeastro.com for more projects -->
                        <!-- general form elements -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-keyboard"></i> Agregar Socios</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form method="post" action="" enctype="multipart/form-data">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label for="fullname"> Nombre</label>
                                            <input type="text" class="form-control" id="fullname" name="fullname"
                                                   placeholder="Ingrese Apellido y Nombre" required>
                                        </div>
                                        <div class="col-sm-3">
                                            <label for="dob">Fecha de Nacimiento</label>
                                            <input type="date" class="form-control" id="dob" name="dob" required>
                                        </div>
                                        <div class="col-sm-3">
                                            <label for="gender">Genero</label>
                                            <select class="form-control" id="gender" name="gender" required>
                                                <option value="Masculino">Masculino</option>
                                                <option value="Femenino">Femenino</option>
                                                <option value="Otro">Otro</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="row mt-3">
                                        <div class="col-sm-6">
                                            <label for="contactNumber">Teléfono</label>
                                            <input type="tel" class="form-control" id="contactNumber"
                                                   name="contactNumber" placeholder="Ingrese Teléfono" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="email">Email</label>
                                            <input type="text" class="form-control" id="email" name="email"
                                                   placeholder="Ingrese email" value="-" >
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-sm-6">
                                            <label for="address">Direccion</label>
                                            <input type="text" class="form-control" id="address" name="address"
                                                   placeholder="Ingrese address" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="country">Barrio</label>
                                            <input type="text" class="form-control" id="country" name="country"
                                                   placeholder="Ingrese Barrio" value="-"">
                                        </div>
                                    </div>
                                    <!-- Visit codeastro.com for more projects -->

                                    <!-- Agrego fila con dni osocial y afiliado -->
                                    <div class="row mt-3">
                                        <div class="col-sm-6">
                                            <label for="dni">DNI</label>
                                            <input type="text" class="form-control" id="dni" name="dni"
                                                   placeholder="Ingrese DNI"  required>
                                        </div>

                                        <!--agrego columna o social-->

                                        <div class="col-sm-6">
                                            <label for="osocial">Obra Social</label>
                                            <input type="text" class="form-control" id="osocial" name="osocial"
                                                   placeholder="Ingrese Obra Social"  value="-">
                                        </div>
                                    </div>
                                        <!-- fin agrego columna o social-->
                            <div class="row mt-3">
                                    

                                        <div class="col-sm-6">
                                            <label for="afiliado">Nro Afiliado</label>
                                            <input type="text" class="form-control" id="afiliado" name="afiliado"
                                                   placeholder="Ingrese Afiliado"  value="-">
                                        </div>

                                    <div class="col-sm-6">
                                        <label for="membershipType">Socio clase</label>
                                        <!-- Replace with a dynamic select box populated from the database -->
                                        <select class="form-control" id="membershipType" name="membershipType" required>
                                            <?php
                                            if ($membershipTypesResult) {
                                                while ($row = $membershipTypesResult->fetch_assoc()) {
                                                    $currencyQuery = "SELECT currency FROM settings";
                                                    $currencyResult = $conn->query($currencyQuery);

                                                    if ($currencyResult->num_rows > 0) {
                                                        $currencyRow = $currencyResult->fetch_assoc();
                                                        $currencySymbol = $currencyRow['currency'];
                                                    } else {
                                                        $currencySymbol = '$';
                                                    }

                                                    echo "<option value='{$row['id']}'>{$row['type']} - {$currencySymbol}{$row['amount']}</option>";
                                                }
                                            } else {
                                                echo "Error: " . $conn->error;
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                    
                            </div>





                                    

                                <div class="row mt-3">
                                    
                                    <div class="col-sm-6">
                                        <label for="photo">Member Photo</label>
                                        <input type="file" class="form-control-file" id="photo" name="photo">
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="nuevoNro">Socio Numero</label>
                                        <input type="text" class="form-control-file" id="nuevoNro" name="nuevoNro" value="<?php echo $membershipNumber ;?> " >
                                    </div>

                                </div>

                        </div>
                        <!-- /.card-body -->

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.card -->

                    </div>
                    <!--/.col (left) -->

                </div>
                <!-- /.row -->
                <!-- Visit codeastro.com for more projects -->

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
    <!-- Visit codeastro.com for more projects -->
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

<?php include('includes/footer.php'); ?>
</body>
</html>
