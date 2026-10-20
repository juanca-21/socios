<?php
ini_set('display_errors', 1);
include('includes/config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SESSION['rol'] !== 'admin') {
    header("Location: dashboard.php?error=acceso_denegado");
    exit();
}

$response = array('success' => false, 'message' => '');
$socioId = 0; // Inicializamos la variable para evitar errores de Notice en PHP

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
    $membershipNumber = $row['ultimo_socio'] + 1;
}

$fecha = date('Y-m-d', strtotime('- 1 month'));
$fecha2 = date('M-Y', strtotime('- 1 month'));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = strtoupper($_POST['fullname']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $contactNumber = $_POST['contactNumber'];
    $email = $_POST['email'];
    $address = strtoupper($_POST['address']);
    $country = strtoupper($_POST['country']);
    $membershipType = $_POST['membershipType'];
    $dni = $_POST['dni'];
    $osocial = strtoupper($_POST['osocial']);
    $afiliado = $_POST['afiliado'];
    $NuevoNumero = $_POST['nuevoNro'];
    $membershipNumber = $NuevoNumero;

    // --- NUEVA VERIFICACIÓN DE NÚMERO DE SOCIO DUPLICADO ---
    $checkQuery = "SELECT id FROM members WHERE membership_number = ?";
    $stmtCheck = $conn->prepare($checkQuery);
    $stmtCheck->bind_param("s", $NuevoNumero);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows > 0) {
        $response['success'] = false;
        $response['message'] = "El Número de Socio ó Matrícula ya se encuentra registrado. Por favor, asigne otro.";
        $stmtCheck->close();
    } else {
        $stmtCheck->close();

        if (!empty($_FILES['photo']['name'])) {
            $uploadedPhoto = $_FILES['photo'];
            $uniquePhotoName = generateUniqueFileName($uploadedPhoto['name']);
            move_uploaded_file($uploadedPhoto['tmp_name'], 'uploads/member_photos/' . $uniquePhotoName);
        } else {
            $uniquePhotoName = 'default.jpg';
        }

        // Inserción en la base de datos
        $insertQuery = "INSERT INTO members (fullname, dob, gender, contact_number, email, address, country, 
                        membership_type, membership_number, photo, created_at, dni, o_social, AfiliadoNro, expiry_date, ultimo_mes) 
                        VALUES ('$fullname', '$dob', '$gender', '$contactNumber', '$email', '$address', '$country', 
                                '$membershipType', '$NuevoNumero', '$uniquePhotoName', NOW(), '$dni', '$osocial', '$afiliado', '$fecha', '$fecha2')";

        if ($conn->query($insertQuery) === TRUE) {
            $response['success'] = true;
            
            // Obtenemos el ID exacto que se acaba de insertar de forma segura
            $socioId = $conn->insert_id; 
            $response['message'] = 'Member added successfully! Membership Number: ' . $membershipNumber;
        } else {
            $response['message'] = 'Error: ' . $conn->error;
        }
    }
}
?>


<?php include('includes/header.php');?>

<style>
/* Estilo para ocultar el título en la web, pero mostrarlo en papel */
.titulo-impresion {
    display: none;
}

@media print {
    /* Ocultar elementos de la interfaz de AdminLTE */
    .main-sidebar, .main-header, .main-footer, .btn, .alert, .card-header {
        display: none !important;
    }
    
    input, select, textarea {
        background-color: transparent !important;
        background-image: none !important;
        color: #000000 !important; 
        border: 1px solid #cccccc !important; 
        font-size: 25pt !important;
    }

    /* Mostrar título de la ficha */
    .titulo-impresion {
        display: block !important;
        text-align: center;
        font-weight: bold;
        font-size: 30px;
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

    @page {
        margin: 0;
        size: auto;
    }

    html, body {
        margin: 0;
        padding: 0;
        background: #fff !important;
        -webkit-print-color-adjust: exact; 
        print-color-adjust: exact;
        zoom: 80%;
    }

    .content-wrapper {
        min-height: auto !important;
        height: auto !important;
        padding: 0 !important;
        margin: 0 !important;
        margin-left: 0 !important;
    }

    .breadcrumb,
    .card-footer,
    #btnEnviar,
    a[href="dashboard.php"] {
        display: none !important;
    }

    .container-fluid, .row, .col-md-12, .card {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
        border: none !important;
    }

    .content {
        padding: 15mm !important; 
    }

    .col-sm-6, .col-sm-3, .col-md-12 {
        float: left !important;
        position: relative !important;
    }

    section, .content, form, .card, .row {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        page-break-after: avoid !important;
        break-after: avoid !important;
    }

    footer, .main-footer, .main-header, .main-sidebar {
        display: none !important;
        height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
    }
}
</style>


<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <?php include('includes/nav.php'); ?>
    <?php include('includes/sidebar.php'); ?>

    <div class="content-wrapper">
        <?php include('includes/pagetitle.php'); ?>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">

                        <?php if ($response['success']): ?>
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h5><i class="icon fas fa-check"></i> Success</h5>
                                <?php echo $response['message']; ?>
                                <hr>
                                <!-- Dejamos el botón por si necesitan reimprimir manualmente antes de que cambie de página -->
                                <button type="button" class="btn btn-light" onclick="window.print(); window.location.href='pago.php?id=<?= $socioId ?>';">
                                    <i class="fas fa-print"></i> Imprimir Ficha del Socio
                                </button>
                            </div>
                            <div class="titulo-impresion">
                                FICHA DEL SOCIO
                            </div>
                        <?php elseif (!empty($response['message'])): ?>
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h5><i class="icon fas fa-ban"></i> Error</h5>
                                <?php echo $response['message']; ?>
                            </div>
                        <?php endif; ?>

                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-keyboard"></i> Agregar Socios</h3>
                            </div>
                            
                            <form method="post" action="" enctype="multipart/form-data" id="add_member">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label for="fullname"> Nombre</label>
                                            <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>" placeholder="Ingrese Apellido y Nombre" required >
                                        </div>
                                        <div class="col-sm-3">
                                            <label for="dob">Fecha de Nacimiento</label>
                                            <input type="date" class="form-control" id="dob" name="dob" value="<?php echo isset($_POST['dob']) ? htmlspecialchars($_POST['dob']) : ''; ?>" required>
                                        </div>
                                        <div class="col-sm-3">
                                            <label for="gender">Genero</label>
                                            <select class="form-control" id="gender" name="gender" required>
                                                <?php $selected_gender = isset($_POST['gender']) ? $_POST['gender'] : ''; ?>
                                                <option value="Masculino" <?php echo ($selected_gender === 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                                                <option value="Femenino" <?php echo ($selected_gender === 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
                                                <option value="Otro" <?php echo ($selected_gender === 'Otro') ? 'selected' : ''; ?>>Otro</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-sm-6">
                                            <label for="contactNumber">Teléfono</label>
                                            <input type="tel" class="form-control" id="contactNumber" name="contactNumber" value="<?php echo isset($_POST['contactNumber']) ? htmlspecialchars($_POST['contactNumber']) : ''; ?>" placeholder="Ingrese Teléfono" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="email">Email</label>
                                            <input type="text" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '-'; ?>" placeholder="Ingrese email">
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-sm-6">
                                            <label for="address">Direccion</label>
                                            <input type="text" class="form-control" id="address" name="address" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>" placeholder="Ingrese address" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="country">Barrio</label>
                                            <input type="text" class="form-control" id="country" name="country" value="<?php echo isset($_POST['country']) ? htmlspecialchars($_POST['country']) : '-'; ?>" placeholder="Ingrese Barrio">
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-sm-6">
                                            <label for="dni">DNI</label>
                                            <input type="text" class="form-control" id="dni" name="dni" value="<?php echo isset($_POST['dni']) ? htmlspecialchars($_POST['dni']) : ''; ?>" placeholder="Ingrese DNI" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="osocial">Obra Social</label>
                                            <input type="text" class="form-control" id="osocial" name="osocial" value="<?php echo isset($_POST['osocial']) ? htmlspecialchars($_POST['osocial']) : '-'; ?>" placeholder="Ingrese Obra Social">
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-sm-6">
                                            <label for="afiliado">Nro Afiliado</label>
                                            <input type="text" class="form-control" id="afiliado" name="afiliado" value="<?php echo isset($_POST['afiliado']) ? htmlspecialchars($_POST['afiliado']) : '-'; ?>" placeholder="Ingrese Afiliado">
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="membershipType">Socio clase</label>
                                            <select class="form-control" id="membershipType" name="membershipType" required>
                                                <?php
                                                if ($membershipTypesResult) {
                                                    while ($row = $membershipTypesResult->fetch_assoc()) {
                                                        $currencyQuery = "SELECT currency FROM settings";
                                                        $currencyResult = $conn->query($currencyQuery);
                                                        $currencySymbol = ($currencyResult && $currencyResult->num_rows > 0) ? $currencyResult->fetch_assoc()['currency'] : '$';
                                                        echo "<option value='{$row['id']}'>{$row['type']} - {$currencySymbol}{$row['amount']}</option>";
                                                    }
                                                } else {
                                                    echo "Error: " . $conn->error;
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-sm-6">
                                            <label for="nuevoNro">Socio Numero</label>
                                            <input type="text" class="form-control" id="nuevoNro" name="nuevoNro" value="<?php echo $membershipNumber;?>" >
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary" id="btnEnviar">Guardar</button>
                                    <a href='dashboard.php' class='btn btn-primary'><i class='fa fa-circle'></i> Volver</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <footer class="main-footer">
        <strong> &copy; <?php echo date('Y');?> codeastro.com -</strong> All rights reserved.
    </footer>
</div>

<?php include('includes/footer.php'); ?>

<script>
// Manejo del botón de envío
document.getElementById('add_member').addEventListener('submit', function(e) {
    const boton = document.getElementById('btnEnviar');
    boton.innerText = "Enviando...";
    boton.disabled = true;
});

// FLUJO DE IMPRESIÓN Y REDIRECCIÓN AUTOMÁTICA
// Este bloque PHP se activa SOLO cuando el registro fue exitoso
<?php if ($response['success']): ?>
    window.addEventListener('DOMContentLoaded', () => {
        // Ejecuta la orden de impresión inmediatamente al cargar
        window.print();

        setTimeout(function() {
            window.location.href = 'pago.php?id=<?= $socioId ?>';
        }, 800); // 800 milisegundos de espera son suficientes en Modo Kiosco
        
        // Redirige automáticamente a pago.php llevando el ID del nuevo socio
        //window.location.href = 'pago.php?id=<?= $socioId ?>';
    });
<?php endif; ?>
</script>
</body>
</html>