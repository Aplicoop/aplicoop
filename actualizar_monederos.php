<?php
session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];
    date_default_timezone_set('Europe/Madrid');
    $session = date("Y-m-d H:i:s", $_SESSION['timeinitse']);
    $data = date("Y-m-d", $_SESSION['timeinitse']);

    $pyear = $_GET['year'];
    $pmes = $_GET['month'];
    if ($pmes == 12) {
        $nextmonth = 1;
        $pnextyear = $pyear +1;
    }
    else{
        $nextmonth = $pmes+1;
        $pnextyear = $pyear;
    }


    $fecha1 = $pnextyear . "-" . $nextmonth . "-01";

    include 'config/configuracio.php';

    $sel = "SELECT tipus FROM usuaris WHERE nom='$user'";
    $query = mysqli_query($conn,$sel) or die ('query failed: ' . mysqli_error($conn));
    list($priv) = mysqli_fetch_row($query);

///sólo entramos si somos "super"////

    if ($priv == 'super') {

        ?>

        <html lang="es">
        <head>
            <?php include 'head.php'; ?>
            <title>Econom&iacute;a - Kidekoop</title>
            <script>var respuesta = confirm('Esta operación no se puede deshacer, ¿Estás seguro que quieres seguir?');
            if (respuesta == false) {
            	alert('Operación cancelada.');
            	window.location = "kidekoop.php";
            }
        </script>
    </head>

    <body>
        <?php include 'menu.php'; ?>
        <div class="page">
            <p>Actualizando Monederos ...</p>
            <?php
        //Check that this is the first time
            $concepto = "Ordainketa (dom.) ".$pmes.'/'.$pyear;
            $query_check = "SELECT familia FROM moneder WHERE concepte = '$concepto'";
            $result = mysqli_query($conn,$query_check);
            if (mysqli_num_rows($result)==0) {
               echo "<p>Acutalizacion de monederos con consumos y cuotas liquidados por domiciliación bancária del més " . $pmes . "/" . $pyear ."</p>";
	        //Ordainketak
               $concepto = "Ordainketa (dom.) ".$pmes.'/'.$pyear;
               $notas = "hecho desde economia kidekoop";
               $query = "SELECT familia, ROUND(SUM(valor),2), IF(year(usuaris.fechaalta)=" . $pyear . " AND month((usuaris.fechaalta))=" . $pmes . ",'20.00',usuaris.kuota), ROUND(IF(year(usuaris.fechaalta)=" . $pyear . " AND month((usuaris.fechaalta))=" . $pmes . ",'20.00',usuaris.kuota) + ABS(SUM(valor)),2) as tot
               FROM `moneder`
               JOIN usuaris ON moneder.familia = usuaris.nom
               WHERE year(data) = " . $pyear . " and month(data) = " . $pmes . " and (concepte LIKE 'Factura%' OR concepte LIKE 'Anulacio%') AND usuaris.domiciliacion=1
               GROUP BY familia";
               $result = mysqli_query($conn,$query);
               if (!$result) {
                   die('Invalid query: ' . mysqli_error($conn));
               }
               while (list($socio,$consumo,$kuota,$total) = mysqli_fetch_row($result)) {
                  // $consumo = sprintf("%01.2f", $consumo);
                  // $total = $consumo + $kuota;
                  echo $socio . " " . $consumo . " + " . $kuota . " = " . $total . "<br>";

                  $query2 = "INSERT INTO moneder
                  VALUES ('" . $session . "','" . $user . "','" . $data . "','" . $socio . "','" . $concepto . "','" . $total . "','" . $notas . "')";
                  mysqli_query($conn,$query2) or die('Error, insert query2 failed');
              }

              $query="SELECT usuaris.nom, IF(year(usuaris.fechaalta)=" . $pyear . " AND month((usuaris.fechaalta))=" . $pmes . ",'20.00',usuaris.kuota)
              FROM usuaris
              WHERE nom NOT IN (
                SELECT DISTINCT us.nom
                FROM usuaris AS us
                JOIN comanda ON us.nom=comanda.usuari
                WHERE year(comanda.data) = " . $pyear . " AND MONTH(comanda.data) = " . $pmes . "
            )  AND usuaris.tipus2 = 'actiu' AND usuaris.domiciliacion = 1 AND usuaris.fechaalta <='" . $fecha1 . "'";
            $result = mysqli_query($conn,$query);
            if (!$result) {
            	die('Invalid query: ' . mysqli_error($conn));
            }

            while (list($socio, $kuota) = mysqli_fetch_row($result)) {
            	echo $socio . " " . $kuota . "<br>";
            	$query2 = "INSERT INTO moneder
               VALUES ('" . $session . "','" . $user . "','" . $data . "','" . $socio . "','" . $concepto . "','" . $kuota . "','" . $notas . "')";
               mysqli_query($conn,$query2) or die('Error, insert query2 failed');
           }
       }
       else {
         echo "<p>Esta operación parece que ha sido realizada. Cancelando ... </p><p><a href='kidekoop.php'>Volver</a>";

     }
     ?>
 </div>
</body>
</html>
<?php
include 'config/disconect.php';
} else {
    header("Location: escriptori2.php");
}
} else {
    header("Location: index.php");
}
?>
