<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
ini_set('display_errors', 1);
include('includes/config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
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
       
//sumo tres meses
        $fecha_db=$memberDetails['expiry_date'] ;
        $fecha = new DateTime($fecha_db);
        $fecha->modify("+ 12 months");

        //echo $fecha->format('Y-m-d'); // Resultado: 2024-08-15
        $expiryDate = $fecha;
        //echo $expiryDate->format('M-Y');

    } 

    
//busco ultimo recibo y le sumo 1
    $sql = "SELECT MAX(comprobante) AS ultimo_comprobante FROM renew";
    $ult_comp = $conn->query($sql);
    if ($row = $ult_comp->fetch_assoc()) {
    //echo "El ultimo comprpbante es: " . $row['ultimo_comprobante'];
    $recibo = $row['ultimo_comprobante'] + 1;
    
                                            }

    //fin ultimo recibo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $membershipTypeId = $_POST['membershipType'];
    $renewDuration = $_POST['extend'];
    echo $renewDuration;
    //agrego calculo de meses
    $fecha_db=$memberDetails['expiry_date'] ;
        $fecha = new DateTime($fecha_db);
        $fecha->modify("+ $renewDuration months");
        $expiryDate = $fecha->format('Y-m-d');
        //$ultimo_mes = $_POST['mes'] ."/" . $_POST['año'];
        $ultimo_mes = $fecha->format('M-Y');
        //echo $ultimo_mes;


    $updateMemberQuery = "UPDATE members SET membership_type = $membershipTypeId, expiry_date = '$expiryDate', ultimo_mes = '$ultimo_mes' WHERE id = $memberId";
    $updateMemberResult = $conn->query($updateMemberQuery);

    $totalAmount = $_POST['totalAmount'];
    //$renewDate = date('Y-m-d');
    $renewDate = $_POST['dop'];
    $mes=$_POST['mes'] ?? '';
    $año=$_POST['año'];
    //$recibo=$_POST['Comprobante'];
    $memberid=$_POST['member_id'];
    $mododepago=$_POST['modo_de_pago'];

    
    
    //actualizar members
    
    $insertRenewQuery = "INSERT INTO renew (member_id, total_amount, renew_date,mes,año,comprobante,modo_de_pago) VALUES ($memberId, $totalAmount, '$renewDate','$mes' ,'$año','$recibo','$mododepago')";

    $insertRenewResult = $conn->query($insertRenewQuery);




    if ($updateMemberResult && $insertRenewResult) {
        $response['success'] = true;
        $response['message'] = 'Membership renewed successfully.';
        
        

    } else {
        $response['message'] = 'Error updating membership or renewing: ' . $conn->error;
    }
    
    
}
?>

<?php include('includes/header.php');?>

<style>
    @media print {

        /* Ocultar botones, menús o barras laterales */
    .btn, .nav, #footer-web, .no-print {
        display: none !important;
    }

        /*form {
            display: none;
        }*/

        .print-button {
            display: none;
                      }
        .button {
            display: none;
                }
        input, textarea {
            display: none;
    /*border: none;
    background: transparent;
    padding: 0;
    appearance: none;*/
                        }
    .card-title {
        display: none;
    }
    }


        
</style>

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
                                <?php echo $response['message'];
                                //sleep(10);
                                ?>
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
                                <h3 class="card-title"><i class="fas fa-keyboard"></i> Formulario de pagos</h3>

                            </div>

                            <!--print area-->
                <div id="print-area">
                    <div class="content-to-copy">
                        <h2 style="text-align: center;">Recibo Nro: <?php echo $recibo ?></h2>
                        <!--<p>This is the content that will appear twice on one sheet.</p> -->
                           <!-- /.card-header -->
                            <!-- Visit codeastro.com for more projects -->
                            <!-- form start -->
                            <form method="post" action="">
                            <input type="hidden" name="member_id" value="<?php echo $id; ?>">
                                <div class="card-body">
                                    <div class="row">
                                    <div class="col-sm-6">
                                    <label for="fullname">Nombre</label>
                                    <input type="text" class="form-control" id="fullname" name="fullname"
                                        placeholder="Enter full name" required value="<?php echo $memberDetails['fullname']; ?>" disabled>
                                </div>
                                        <div class="col-sm-6">
                                            <label for="dob">Socio Numero</label>
                                            <input type="text" class="form-control" id="mm" name="mm" value="<?php echo $memberDetails['membership_number']; ?>" disabled>
                                        </div>
                                        
                                    </div>


                                    <div class="row mt-3">
                                    <div class="col-sm-6">
                                    <label for="membershipType">Tipo de Socio</label>
                                    <select class="form-control" id="membershipType" name="membershipType" required>
                                        <?php
                                        if ($membershipTypesResult) {
                                            while ($row = $membershipTypesResult->fetch_assoc()) {
                                                echo "<option value='{$row['id']}'>{$row['type']} - {$row['amount']}</option>";
                                            }
                                        } else {
                                            echo "Error: " . $conn->error;
                                        }
                                        ?>
                                    </select>
                                </div>
                                        <div class="col-sm-6">
                                        <label for="extend">Meses a Pagar</label>
                                            <select class="form-control" id="extend" name="extend" required>
                                                <option value="1">un mes</option>
                                                <option value="2">dos meses</option>
                                                <option value="3">tres meses</option>
                                                <option value="4">cuatro meses</option>
                                                <option value="5">cinco meses</option>
                                                <option value="6">seis meses</option>
                                                <option value="7">siete meses</option>
                                                <option value="8">ocho meses</option>
                                                <option value="9">nueve meses</option>
                                                <option value="10">diez meses</option>
                                                <option value="11">once meses</option>
                                                <option value="12">un año</option>
                                            </select>
                                        </div>
                                    
                                       
                                    </div>

                                    <div class="row mt-3">
                                    <div class="col-sm-6">
                                        <label for="totalAmount">Total Amount</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="currencySymbol"><?php echo getCurrencySymbol(); ?></span>
                                            </div>
                                            <input type="text" class="form-control" id="totalAmount" name="totalAmount" placeholder="Total Amount" readonly>
                                        </div>
                                    </div>

                                    <!--agrego otra columna-->
                                    
                                    <div class="col-sm-6">
                                        <label for="ultimopago">ultimo mes pagado</label>
                                        
                                    <input type="text" class="form-control" id="ultimopago" name="ultimopago"
                                          disabled >                                  
                                    </div> 
                                    
                                    
                                <!--fin agrego otra columna-->

                                <!-- agrego otra fila-->
                                <div class="row mt-3">
                                    <div class="col-sm-6">
                                        <label for="mes">mes a pagar</label>
                                            <input type="text" class="form-control" id="mes" name="mes" disabled>

                                            
                                    </div>

                                    
                                    
                                    <div class="col-sm-6">
                                        <label for="año">año</label>
                                        <input type="text" class="form-control" id="año" name="año" placeholder="ingrese año" disabled>
                                                                                   
                                    </div> 
                                    <!--añado forma de pago-->

                                    <div class="col-sm-6">
                                        <label for="modo_de_pago">modo de pago</label>


                                        <select class="form-control" id="modo_de_pago" name="modo_de_pago" required placeholder="ingrese año" value="efectivo">
                                        <option value="efectivo">efectivo</option>
                                        <option value="transferencia">transferencia</option>


                                        </select>

                                                                                   
                                    </div> 

<!-- fin agrego campo comprobante -->
                                    <div class="col-sm-3">
                                        <label for="Comprobante">Comprobante</label>
                                        <input type="text" class="form-control" id="Comprobante" name="Comprobante" required placeholder="ingrese numero de Comprobante" value="<?php echo "$recibo"; ?>" >
                                                                                   
                                    </div> 
                                    <!--agrego fecha pago-->

                                    <div class="col-sm-3">
                                            <label for="dop">fecha de pago</label>
                                            <input type="date" class="form-control" id="dop" name="dop" value="<?php echo date('Y-m-d');?>">
                                    <!--agrego fecha pago fin--> 


                                        </div>


                                    </div> <!--ojo-->

                    </div>
                                    <p></p>
                                    <br>
                                    
                </div> <!--fin area print-->
 

                                    <!-- fin agrego otra fila-->




                                </div>

                                <?php
                                function getCurrencySymbol()
                                {
                                    global $conn;

                                    $currencyQuery = "SELECT currency FROM settings";
                                    $currencyResult = $conn->query($currencyQuery);

                                    if ($currencyResult->num_rows > 0) {
                                        $currencyRow = $currencyResult->fetch_assoc();
                                        return $currencyRow['currency'];
                                    } else {
                                        return '$';
                                    }
                                }
                                ?>
                                </div>
                                <!-- /.card-body -->

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary" onclick="prepareAndPrint()">Guardar</button>
                                    <!-- agrego-->

                                    <a href='list_pagos.php' class='btn btn-primary' > <i class='fa fa-circle' ></i> Volver</a>
<!--agrego boton print-->
                                    <button type="button" class="btn btn-primary print-button" onclick="prepareAndPrint()"><i class="fas fa-print"></i> Imprimir Recibo
                                    </button>
<!--fin agrego boton print-->
                                    <!--fin-->


                                    <!--<button type="submit" formaction="list_pagos.php" class="btn-primary">Otro Pago</button>-->

                                    
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
    <!-- Visit codeastro.com for more projects -->
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

<?php include('includes/footer.php'); ?>

<script>
    $(document).ready(function () {
        function updateTotalAmount() {
            var membershipTypeAmount = parseFloat($('#membershipType option:selected').text().split('-').pop());

            var renewDuration = parseFloat($('#extend').val());

            var totalAmount = membershipTypeAmount * renewDuration;

            $('#totalAmount').val(totalAmount.toFixed(2));
            //

            
            // --- Cálculo de la Fecha (Nueva lógica) ---
        var fechaSesion = "<?php echo $fecha_db ?? '';?>";
        //var fechaBase = new Date($('#ultimoMes').val()); // Supongamos que tienes la fecha en un input hidden
        var fechaBase = new Date(fechaSesion + 'T00:00:00'); // Supongamos que tienes la fecha en un input hidden
        
        if (!isNaN(fechaBase.getTime())) {
            fechaBase.setDate(10);
            fechaBase.setMonth(fechaBase.getMonth() + renewDuration);
            ///
// Configuramos el mes como 'short'
        
        const opciones = { month: 'long', year: 'numeric' };
        let fechaFormateada = fechaBase.toLocaleDateString('es-ES', opciones);
        // Quitar el punto final si el navegador lo añade (ej: "mar." -> "mar")
        fechaFormateada = fechaFormateada.replace('.', '');
        
        // Capitalizar la primera letra
        fechaFormateada = fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1);




            ///
            // Formatear fecha a YYYY-MM-DD
            var day = ("0" + fechaBase.getDate()).slice(-2);
            var month = ("0" + (fechaBase.getMonth() + 1)).slice(-2);
            var year = fechaBase.getFullYear();
            
            //$('#año').val(year + "-" + month + "-" + day);
            $('#año').val(year);

            //$('#mes').val(month);

            document.getElementById('mes').value = fechaFormateada;


            console.log("La fecha en sesión es: " + fechaSesion);
            console.log("La fecha en sesión es: " + renewDuration);
            //$('#campo_formulario').val(fechaSesion);
        

            //
        }


//endif


        }

        $('#membershipType, #extend').change(updateTotalAmount);

        updateTotalAmount();








        
    });
</script>

<script>
            // format fecha ultimo pago

        //document.getElementById('ultimoMes').addEventListener('change', function() 
        $(document).ready(function () 
        {

            //const fechaBase = new Date($('#ultimoMes').val()); // Supongamos que tienes la fecha en un input hidden
    //const fechaBase = this.value; // Obtiene el valor en formato YYYY-MM-DD
            var fechaSesion = "<?php echo $fecha_db ?? '';?>";
            var fechaBase = new Date(fechaSesion + 'T00:00:00');
    
    if (fechaBase) {
        // Creamos el objeto fecha (añadiendo hora para evitar desfases de zona horaria)
        //const fecha1 = new Date(fechaBase + 'T00:00:00');
        
        // Opciones para formatear el mes en español
        const opciones1 = { month: 'long', year: 'numeric' };
        let fechaFormateada1 = fechaBase.toLocaleDateString('es-ES', opciones1);
        
        // Capitalizar la primera letra (opcional)
        fechaFormateada1 = fechaFormateada1.charAt(0).toUpperCase() + fechaFormateada1.slice(1);
        console.log("La fechabase es: " + fechaBase);
        console.log("La fecha1 es: " + fechaBase);
        // Asignar al segundo campo
        document.getElementById('ultimopago').value = fechaFormateada1;
    }
});


</script>
<script>
function printReport() {
    window.print();
}
</script>
<!--imprimo dos copias-->
<script>
function prepareAndPrint() {
  const container = document.getElementById('print-area');
  const originalContent = container.querySelector('.content-to-copy');
  
  // Clone the content
  const clone = originalContent.cloneNode(true);
  clone.classList.add('is-clone');
  container.appendChild(clone);

  // Trigger print
  window.print();

  // Clean up: Remove the clone after printing so the web view stays normal
  window.addEventListener('afterprint', () => {
    clone.remove();
  }, { once: true });
}
</script>
<!--fin imprimo dos copias-->







</body>
</html>
