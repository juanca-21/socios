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



    $reciboId = $_GET['id'];

    $fetchMemberQuery = "SELECT m.fullname, m.membership_number, m.fecha_ult_pago, m.id, r.Id,   r.total_amount, r.renew_date, r.mes, r.anio, r.modo_de_pago, r.comprobante                     FROM renew r 
                    JOIN members m ON r.member_id = m.id
                    WHERE r.Id = $reciboId ";

    $fetchMemberResult = $conn->query($fetchMemberQuery);

    if ($fetchMemberResult->num_rows > 0) {
        $memberDetails = $fetchMemberResult->fetch_assoc();
       
//sumo tres meses
        //$fecha_db=$memberDetails['expiry_date'] ;
        //$fecha = new DateTime($fecha_db);
        //$fecha->modify("+ 12 months");

        //echo $fecha->format('Y-m-d'); // Resultado: 2024-08-15
        //$expiryDate = $fecha;
        //echo $expiryDate->format('M-Y');

    } 

 /*   
//busco ultimo recibo y le sumo 1
    $sql = "SELECT MAX(comprobante) AS ultimo_comprobante FROM renew";
    $ult_comp = $conn->query($sql);
    if ($row = $ult_comp->fetch_assoc()) {
    //echo "El ultimo comprpbante es: " . $row['ultimo_comprobante'];
    $recibo = $row['ultimo_comprobante'] + 1;

}
    
 */                                           

    //fin ultimo recibo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /*$membershipTypeId = $_POST['membershipType'];
    $renewDuration = $_POST['extend'];
    echo $renewDuration;*/
    //agrego calculo de meses
        $fecha_db=$memberDetails['anio'] ;
        $fecha = new DateTime($fecha_db);
        //$fecha->modify("+ $renewDuration months");
        
        //$ultimo_mes = $_POST['mes'] ."/" . $_POST['año'];
        $ultimo_mes = $fecha->format('M-Y');
        //echo $ultimo_mes;
    	$hoy=new DateTime ('today');
        $ultimo_pago=$hoy->format('Y-m-d');
        $memberId=$memberDetails['id'];
        $expiryDate = $fecha->format('Y-m-d');
        $ultimo_mes=$fecha->format('M-Y');



    $updateMemberQuery = "UPDATE members SET expiry_date = '$expiryDate' ,ultimo_mes = '$ultimo_mes' WHERE id = $memberId";

    $updateMemberResult = $conn->query($updateMemberQuery);

    $totalAmount = $_POST['totalAmount'];
    //$renewDate = date('Y-m-d');
    $renewDate = $_POST['dop'];
    $mes=$_POST['mes'] ?? '';
    $anio=$_POST['anio'] ?? '';
    //$recibo=$_POST['Comprobante'];
    $memberid=$_POST['member_id'];
    $mododepago=$_POST['modo_de_pago'];

/*    
    
    //actualizar members
    
    $insertRenewQuery = "INSERT INTO renew (member_id, total_amount, renew_date,mes,anio,comprobante,modo_de_pago) VALUES ($memberId, $totalAmount, '$renewDate','$mes' ,'$anio','$recibo','$mododepago')";

    $insertRenewResult = $conn->query($insertRenewQuery);

    */
    $insertRenewQuery = "UPDATE renew set modo_de_pago = '$mododepago' WHERE Id = $reciboId ";

    $insertRenewResult = $conn->query($insertRenewQuery);

//&& $insertRenewResult


    if ($updateMemberResult && $insertRenewResult) {
        $response['success'] = true;
        $response['message'] = 'Membership renewed successfully.';
        
        

    } else {
        $response['message'] = 'Error updating membership or renewing: ' . $conn->error;
        echo "$memberId $expiryDate";
    }
    
    
}
?>


<?php include('includes/header.php');?>

<style>


    

    @media print {


        .alert .alert-success .alert-dismissible {

            display: none;
        }
        /* Ocultar botones, menús o barras laterales */
    .btn, .nav, #footer-web, header, footer .no-print {
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
                            <div class="alert alert-success alert-dismissible" id='alerta'>
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h5><i class="icon fas fa-check"></i> Success</h5>
                                <?php echo $response['message'];
                                //sleep(10);
                                echo "$memberId $expiryDate $mododepago";
                                ?> 
                            </div>
                        <?php elseif (!empty($response['message'])): ?>
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h5><i class="icon fas fa-ban"></i> Error</h5>
                                <?php echo $response['message']; echo "$memberId $expiryDate $mododepago";?>
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
                        <h2 style="text-align: center;">Recibo Nro: <?php echo $memberDetails['comprobante'] ?>;</h2>
                        <!--<p>This is the content that will appear twice on one sheet.</p> -->
                           <!-- /.card-header -->
                            <!-- Visit codeastro.com for more projects -->
                            <!-- form start -->
                            <form method="post" action="" name ="pago" id="miFormulario">
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
                                        <input type="text" class="form-control" id="-" name="-" value="-" disabled>       

                                        </div>
                                    
                                       
                                    </div>

                                    <div class="row mt-3">
                                    <div class="col-sm-6">
                                        <label for="totalAmount">Total Amount</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="currencySymbol"><?php echo getCurrencySymbol(); ?></span>
                                            </div>
                                            <input type="text" class="form-control" id="total_Amount" name="total_Amount" placeholder="Total Amount" value="<?php echo $memberDetails['total_amount']; ?>"  readonly>
                                        </div>
                                    </div>

                                    <!--agrego otra columna-->
                                    
                                    <div class="col-sm-6">
                                        <label for="ultimopago">Pago Anterior</label>
                                        
                                    <input type="text" class="form-control" id="ultimo_pago" name="ultimo_pago"
                                       value="<?php echo date('M-Y',strtotime($memberDetails['anio'] ));?>"   readonly >                                  
                                    </div> 
                                    
                                    
                                <!--fin agrego otra columna-->

                                <!-- agrego otra fila-->
                                <div class="row mt-3">
                                    <div class="col-sm-6">
                                        <label for="mes">Pago actual</label>
                                            <input type="text" class="form-control" id="mes" name="mes" value = "<?php echo date('M-Y',strtotime($memberDetails['mes'] ));?>" >

<!--//value = "<?php //echo ($memberDetails['mes'] ?? '--');?>" >-->
                                            
                                    </div>

                                    
                                    
                                    <div class="col-sm-6">
                                        <label for="anio">año</label>
                                        <input type="text" class="form-control" id="anio" name="anio" placeholder="ingrese año" disabled>
                                                                                   
                                    </div> 
                                    <!--añado forma de pago-->

                                    <div class="col-sm-6">
                                        <label for="modo_de_pago">modo de pago</label>


                                        <select class="form-control" id="modo_de_pago" name="modo_de_pago" required placeholder="ingrese modo" value="efectivo">
                                        <option value="efectivo">efectivo</option>
                                        <option value="transferencia">transferencia</option>
                                        <option value="anulado">anulado</option>



                                        </select>

                                                                                   
                                    </div> 

<!-- fin agrego campo comprobante -->
                                    <div class="col-sm-3">
                                        <label for="Comprobante">Comprobante</label>
                                        <input type="text" class="form-control" id="Comprobante" name="Comprobante" required placeholder="ingrese numero de Comprobante" value="<?php echo $memberDetails['comprobante']; ?>" >
                                                                                   
                                    </div> 
                                    <!--agrego fecha pago-->

                                    <div class="col-sm-3">
                                            <label for="dop">fecha de pago</label>
                                            <input type="date" class="form-control" id="dop" name="dop" value="<?php echo $memberDetails['fecha_ult_pago'];?>">
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

                                    <button type="submit" class="btn btn-primary" >Anular recibo</button>
                                    
                                    <!-- agrego-->

                                    <a href='revenue_report.php' class='btn btn-primary' > <i class='fa fa-circle' ></i> Volver</a>
<!--agrego boton print-->
<!-- ANULO IMPRIMIR RECIBO-->
                                   <button type="button" class="btn btn-primary print-button" onclick="prepareAndPrint()"><i class="fas fa-print"></i> Reimprimir Recibo
                                    </button>
<!---->

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
            $('#anio').val(year);

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

  const elemento = document.getElementById('alerta');
  if (elemento) {
    elemento.remove(); // Elimina el div del DOM
  };

  const container = document.getElementById('print-area');
  const originalContent = container.querySelector('.content-to-copy');
  
  // Clone the content
  const clone = originalContent.cloneNode(true);
  clone.classList.add('is-clone');
  container.appendChild(clone);

    //correccion
    
    // 2. CORRECCIÓN: Sincronizar valores de formularios
  // Buscamos todos los elementos que pueden tener valores cambiantes
  const selectoresOriginales = originalContent.querySelectorAll('select, input, textarea');
  const selectoresClonados = clone.querySelectorAll('select, input, textarea');

  selectoresOriginales.forEach((el, index) => {
    // Copiamos el valor actual al elemento correspondiente en el clon
    selectoresClonados[index].value = el.value;
    
    // Caso especial para Checkboxes y Radio buttons
    if (el.type === 'checkbox' || el.type === 'radio') {
      selectoresClonados[index].checked = el.checked;
    }
  });


    
    
  // Trigger print
  window.print();

  // Clean up: Remove the clone after printing so the web view stays normal
  window.addEventListener('afterprint', () => {
    clone.remove();
  }, { once: true });
}
</script>

<!--fin imprimo dos copias-->
<!---->
<script>
// Seleccionamos el formulario para confirmar
const miFormulario = document.getElementById('miFormulario');

miFormulario.addEventListener('submit', function(e) {
  // 1. Detenemos el envío inmediato
  e.preventDefault();

  // 2. Lanzamos la alerta personalizada
  Swal.fire({
    title: '¿Estás seguro?',
    text: "Revisa que todos los datos sean correctos antes de enviar.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6', // Color azul para confirmar
    cancelButtonColor: '#d33',    // Color rojo para cancelar
    confirmButtonText: 'Sí, enviar datos',
    cancelButtonText: 'No, revisar'
  }).then((result) => {
    // 3. Si el usuario hizo clic en "Sí"
    if (result.isConfirmed) {
      // Enviamos el formulario programáticamente
        //window.print();
        //prepareAndPrint();

        setTimeout(() => {
        miFormulario.submit();
      }, 3000);

        
        setTimeout(() => {
            prepareAndPrint();
        
      }, 500);

      //miFormulario.submit();
      
      
    }
  });
});

</script>

<script>

    // Espera 3000 milisegundos (3 segundos)
setTimeout(function() {
  const elemento = document.querySelector('.alert alert-danger alert-dismissible');
  if (elemento) {
    elemento.remove(); // Elimina el div del DOM
  }
}, 3000);
</script>



</body>
</html>
