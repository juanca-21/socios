<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, 'es_ES.UTF-8');
ini_set('display_errors', 1);
include('includes/config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fromDate = $_POST['fromDate'];
    $toDate = $_POST['toDate'];

    $reportQuery = "SELECT m.fullname, m.membership_number, r.Id,   r.total_amount, r.renew_date, r.modo_de_pago, r.comprobante, r.mes, r.anio, s.currency
                    FROM renew r
                    JOIN members m ON r.member_id = m.id
                    LEFT JOIN settings s ON s.id = 1
                    WHERE r.renew_date BETWEEN '$fromDate' AND '$toDate'";
    $reportResult = $conn->query($reportQuery);
    //var_dump($reportResult);
}

?>

<?php
function getTotalRevenueWithCurrency()
{
    global $conn;
    global $memberId;
    global $fromDate;
    global $toDate;

    $currencyQuery = "SELECT currency FROM settings LIMIT 1";
    $currencyResult = $conn->query($currencyQuery);

    if ($currencyResult->num_rows > 0) {
        $currencyRow = $currencyResult->fetch_assoc();
        $currencySymbol = $currencyRow['currency'];
    } else {
        $currencySymbol = '$'; // Default currency symbol (you can change this as needed)
    }

    $totalRevenueQuery = "SELECT SUM(total_amount) AS totalRevenue FROM renew WHERE renew_date BETWEEN '$fromDate' AND '$toDate' AND modo_de_pago = 'efectivo'";
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


<?php include('includes/header.php');?>
<style>
    @media print {
        form {
            display: none;
        }

        .print-button {
            display: none;
        }
    }
</style>


<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
  <?php include('includes/nav.php');?>
  <?php include('includes/sidebar.php');?>
  
  <div class="content-wrapper">
    <?php include('includes/pagetitle.php');?>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-keyboard"></i> Revenue Report</h3>
                <h1><?php echo getTotalRevenueWithCurrency()?></h1>
                
              </div>
              
              <form method="post" action="">
                <div class="card-body">
                  <div class="form-group">
                    <label for="fromDate">From Date:</label>
                    <input type="date" id="fromDate" name="fromDate" class="form-control" required>
                  </div>

                  <div class="form-group">
                    <label for="toDate">To Date:</label>
                    <input type="date" id="toDate" name="toDate" class="form-control" required>
                  </div>

                  <button type="submit" class="btn btn-success">Generate Report</button>
                </div>
              </form>
              
              <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                  if ($reportResult->num_rows > 0) {
                    echo '<div class="card-body">';
                    echo '<table class="table table-bordered table-striped">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>Member</th>';
                    echo '<th>Membership Number</th>';

                    echo '<th>Total Amount</th>';
                    echo '<th>Modo de pago</th>';
                    echo '<th>comprobante</th>';
                    echo '<th>mes pagado</th>';
                    echo '<th>pago anterior</th>';
                    echo '<th>Date</th>';
                    echo '<th>accion</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    
                    while ($row = $reportResult->fetch_assoc()) {
                      echo '<tr>';
                      echo '<td>' . $row['fullname'] . '</td>';
                      echo '<td>' . $row['membership_number'] . '</td>';
                      echo '<td>' . $row['currency'] .'  '. $row['total_amount'] . '</td>';
                      //
                      echo '<td>' . $row['modo_de_pago'] . '</td>';
                      echo '<td>' . $row['comprobante'] . '</td>';
                      echo '<td>';
                      echo date('M-Y',strtotime($row['mes'] ));
                      echo '</td>';
                      echo '<td>';
                      echo date('M-Y',strtotime($row['anio'] ));
                      echo '</td>';
                      //
                      echo '<td>' . $row['renew_date'] . '</td>';
                      echo '<td>';

                      echo "
                    <a href='edito_pago.php?id={$row['Id']}' class='btn btn-primary' title='Editar recibo'><i class='fas fa-edit'></i>Editar</a>  '
                    </td>";
                    }

                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';

                    echo '<div class="card-footer">';
                    echo '<button type="button" class="btn btn-primary print-button" onclick="printReport()"><i class="fas fa-print"></i> Print Report</button>';
                    echo '</div>';
                  } else {
                    echo '<div class="card-body">';
                    echo '<p>No renew records found within the specified date range.</p>';
                    echo '</div>';
                  }
                }
              ?>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <aside class="control-sidebar control-sidebar-dark">
  </aside>

  <footer class="main-footer">
    <strong> &copy; <?php echo date('Y');?> codeastro.com</a> -</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Developed By</b> <a href="https://codeastro.com/">CodeAstro</a>
    </div>
  </footer>

  
</div>

<?php include('includes/footer.php');?>

<script>
function printReport() {
    window.print();
}
</script>

</body>
</html>
