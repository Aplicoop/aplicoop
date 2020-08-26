<?php
session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];
    date_default_timezone_set('Europe/Madrid');
    $session = date("Y-m-d H:i:s", $_SESSION['timeinitse']);
    $data = date("Y-m-d", $_SESSION['timeinitse']);

    $pyear = $_GET['year'];
    $pmes = $_GET['month'];

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
        $concepto = "Kuota ".$pmes.'/'.$pyear;
        $notas = "hecho desde economia kidekoop";
        $query_check = "SELECT familia FROM moneder WHERE concepte = '$concepto'";
        $result = mysqli_query($conn,$query_check);
        if (mysqli_num_rows($result)==0) {
        	echo "<p>Aplicar la cuota del mes en los monederos para el més " . $pmes . "/" . $pyear ."</p>";

	        //Kuotak
	        echo "<p>Kuotas del més : ". $pmes . "/" . $pyear . "</p>";
	        $query = "SELECT nom,IF(year(usuaris.fechaalta)=" . $pyear . " AND month((usuaris.fechaalta))=" . $pmes . ",'20.00',usuaris.kuota) FROM `usuaris` WHERE tipus2='actiu' AND kuota != 0";
	        $result = mysqli_query($conn,$query);
			if (!$result) {
	            die('Invalid query: ' . mysqli_error($conn));
	            }
	        while (list($socio,$kuota) = mysqli_fetch_row($result)) {
	        	echo $socio . " " . $kuota . "<br>";
	        	$query2 = "INSERT INTO moneder
	 			VALUES ('" . $session . "','" . $user . "','" . $data . "','" . $socio . "','" . $concepto . "','" . -$kuota . "','" . $notas . "')";
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
