<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user=strtoupper($_SESSION['user']);

$numcmda=$_GET['id'];


include 'config/configuracio.php';

$query = "DELETE FROM comanda_linia WHERE numero='$numcmda'";
mysql_query($query) or die('Error, insert query failed');

$numrows = mysql_affected_rows();

$query3 = "DELETE FROM comanda WHERE numero='$numcmda'";
mysql_query($query3) or die('Error, insert query failed');



?>

<html>
	<head>
		<?php include 'head.php'; ?>
		<title>aplicoop - eliminar pedido</title>
	</head>
<body>
<?php include 'menu.php'; ?>
<div class="page">
	<div class="container">
		<div class="box">
			<h2 class="box-title">
				La comanda número <?php echo $numcmda; ?>
				ha si borrado completamente
			</h2>
			<p class="alert alert--info">
				<?php echo $numrows; ?> productos borrados
			</p>
		</div>

		<div class="u-text-center">
			<a class="button" href="escriptori2.php" target="cos" title="Volver al escritorio">Volver</a>
		</div>
	</div>
</div>
</body>
</html>
<?php 

include 'config/disconect.php';
} 
else {
header("Location: index.php"); 
}
?>


