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
    $fullname = mb_strtoupper($_POST['fullname']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $contactNumber = $_POST['contactNumber'];
    $email = $_POST['email'];
    $address = mb_strtoupper($_POST['address']);
    $country = mb_strtoupper($_POST['country']);
    //$postcode = $_POST['postcode'];
    //$occupation = $_POST['occupation'];
    $membership_Type = $_POST['membershipType'];
    $dni=$_POST['dni'];
    $osocial=mb_strtoupper($_POST['osocial']);
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

    $updateQuery = "UPDATE members SET fullname='$fullname', dob='$dob', gender='$gender', contact_number='$contactNumber', email='$email', address='$address', country='$country',membership_type='$membership_Type'
                    ,dni='$dni',o_social='$osocial',AfiliadoNro='$afiliado ',expiry_date='$expirydate',ultimo_mes='$ultimomes' $photoUpdate
                    WHERE id = $memberId";

    if ($conn->query($updateQuery) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'Member updated successfully!';
        //sleep(5);
        header("location:manage_members.php");
        

        //header("Location: manage_members.php");
        exit();
    } else {
        $response['message'] = 'Error: ' . $conn->error;
    }
}
?>


<?php include('includes/header.php');?>


<style>

/* Estilo para ocultar el título en la web, pero mostrarlo en papel */
.titulo-impresion {
    display: none;
}

.solo-impresion {
    display: none;
}

@media print {

    body {
        font-size: 20pt !important; /* El tamaño ideal para lectura en papel se mide en puntos (pt) */
        font-weight: bold !important;
        color: #000000 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

/* Ajustamos los párrafos y listas si es necesario */
    p, li, td, span {
        font-size: 20pt !important;
        font-weight: bold !important;
    }


    /* Ocultar elementos de la interfaz de AdminLTE */
    .main-sidebar, .main-header, .main-footer, .btn, .alert, .card-header {
        display: none !important;
    }

    .solo-impresion {
        display: block;
        text-align: center;
        font-size: 72px;
        margin-bottom: 20px;
    }
    
    /*.content-wrapper {
        margin-left: 0 !important;
        padding: 0 !important;
    }*/

    input, select, textarea {
        background-color: transparent !important;
        background-image: none !important;
        color: #000000 !important; /* Asegura que el texto sea negro y legible */
        border: 1px solid #cccccc !important; /* Deja un borde sutil para saber dónde está el campo */
        font-size: 25pt !important;
        /*font-weight: bold !important;*/
    }

    /* Mostrar título de la ficha */
    .titulo-impresion {
        display: block !important;
        text-align: center;
        font-weight: bold;
        font-size: 24px;
        margin-bottom: 20px;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
    }

    /* Ajustar inputs para que parezcan texto plano */
    .form-control {
        border: none !important;
        padding: 0 !important;
    }
    
    label {
        font-weight: bold;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    /*COMIENZA CODIGO INCRUSTADO*/


    /* 1. Resetear la página y eliminar márgenes del navegador/kiosco */
    @page {
        margin: 0;
        size: auto;
    }

    html, body {
        margin: 0;
        padding: 0;
        background: #fff !important;
        -webkit-print-color-adjust: exact; /* Mantiene colores de AdminLTE si hacen falta */
        print-color-adjust: exact;
        zoom: 80%;
    }

    /* 2. Matar el min-height inline de 1020px y limpiar contenedores */
    .content-wrapper {
        min-height: auto !important;
        height: auto !important;
        padding: 0 !important;
        margin: 0 !important;
        margin-left: 0 !important;
        
    }

    /* 3. Ocultar lo que no sirve en el papel (Botones, Navegación, Breadcrumbs) */
    .breadcrumb,
    .card-footer,
    #btnEnviar,
    a[href="dashboard.php"] {
        display: none !important;
    }

    /* 4. Forzar que el formulario use todo el ancho sin desbordar */
    .container-fluid, .row, .col-md-12, .card {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
        border: none !important;
    }

    /* 5. Ajustar márgenes internos para que quede prolijo en la hoja */
    .content {
        padding: 15mm !important; /* Margen físico seguro para la hoja */
    }

    /* Evitar que Bootstrap rompa las columnas en la impresión */
    .col-sm-6, .col-sm-3, .col-md-12 {
        float: left !important;
        position: relative !important;
    }

    /* 4. Prohibir saltos de página fantasmas */
    section, .content, form, .card, .row {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        page-break-after: avoid !important;
        break-after: avoid !important;
    }

    /* 2. Ocultar absolutamente todo lo periférico (Footer de AdminLTE incluido) */
    .breadcrumb,
    .card-footer,
    #btnEnviar,
    a[href="dashboard.php"],
    footer,
    .main-footer,
    .main-header,
    .main-sidebar {
        display: none !important;
        height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
    }


    /*FINALIZA CODIGO INCRUSTADO*/


}
</style>


<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <?php include('includes/nav.php'); ?>

    <?php include('includes/sidebar.php'); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <?php include('includes/pagetitle.php'); ?>
        <h1 class="solo-impresion">Ficha del Socio</h1>
        

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
                            <form method="post" action="" enctype="multipart/form-data" >
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
                                        <div class="col-sm-">
                                            <label for="dni">DNI</label>
                                            <input type="text" class="form-control" id="dni" name="dni"
                                                   placeholder="Ingrese DNI" value="<?php echo $memberDetails['dni'] ?: '-'; ?>" required>
                                        </div>
                                        <!--agrego columna o social-->

                                        <div class="col-sm-3">
                                            <label for="osocial">O. Social</label>
                                            <input type="text" class="form-control" id="osocial" name="osocial"
                                                   placeholder="Ingrese O. Social" value="<?php echo $memberDetails['o_social'] ?: '-'; ?> " >
                                        </div>
                                        <!-- fin agrego columna o social-->
                                        <!-- agrego tipo socio-->

                                        <div class="col-sm-3">
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

                                    <!--fin agrego tipo de socio-->

                                        <div class="col-sm-3">
                                            <label for="afiliado">Nro Afiliado</label>
                                            <input type="text" class="form-control" id="afiliado" name="afiliado"
                                                   placeholder="Ingrese Afiliado" value="<?php echo $memberDetails['AfiliadoNro'] ?: '-'; ?>"  >
                                        </div>

                                        <div class="col-sm-3">
                                            <label for="expiryDate">Al dia Hasta</label>
                                            <input type="date" class="form-control" id="expiryDate" name="expiryDate"
                                                   placeholder="Ingrese Afiliado" value="<?php echo $memberDetails['expiry_date'] ?: '-'; ?>" required >
                                        </div>

                                        <div class="col-sm-3">
                                            <label for="ultmes">ultimo pago</label>
                                            <input type="text" class="form-control" id="ultmes" name="ultmes"
                                                   placeholder="Ingrese Afiliado" value="<?php echo $memberDetails['ultimo_mes'] ?: '-'; ?>"  >
                                        </div>

                                        <div class="col-sm-3">
                                            <label for="socnum">Socio Número </label>
                                            <input type="text" class="form-control" id="socnum" name="socnum"
                                                   placeholder="Ingrese Afiliado" value="<?php echo $memberDetails['membership_number'] ?: '-'; ?>" disabled >
                                        </div>




                                    </div>
                                <!--
                                    
                                <div class="row mt-3">
                                    <div class="col-sm-6">
                                        <label for="photo">Member Photo</label>
                                        <input type="file" class="form-control" id="photo" name="photo">
                                        <small class="text-muted">Leave it blank if you don't want to change the photo.</small>
                                    </div>
                                </div>
                                -->


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
