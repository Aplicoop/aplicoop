<?php

session_start();

if ($_SESSION['image_is_logged_in'])
{

$user = $_SESSION['user'];
$_SESSION['codi_cistella']='off';

$gproces=$_GET['id'];
$ggrup=$_GET['id2'];
$gbd_data=$_GET['id3'];

$gdata=date('d-m-Y',strtotime('$gbd_data'));

?>

<html lang="es">
	<head>
		<?php include 'head.php'; ?>
		<title>aplicoop - generación factura</title>
	</head>

<body>
<?php include 'menu.php'; ?>
<div class="page">
<div class="container">

<h1>Generación factura</h1>
<div class="box">
<p class='alert alert--info'>
	Las facturas de cada familia se han generado correctamente. Puede verlas o imprimirlas clicando encima.
</p>

<div >
<div class="table-responsive">
<table class="table table-striped table-bordered">

<?php

echo "<tr class='cos_majus'><td width='55%' class='u-text-semibold'>Família (número pedido)</td>";
echo "<td width='15%' class='u-text-semibold  u-text-center'>Prods. pedidos</td>";
echo "<td width='15%' class='u-text-semibold  u-text-center'>Prods. servidos</td>";
echo "<td width='15%' class='u-text-semibold  u-text-center'>Total a pagar</td>";
echo "</tr>";

include 'config/configuracio.php';

$taula = "SELECT numero, usuari, check0
FROM comanda
WHERE proces='$gproces' AND grup='$ggrup' AND data='$gbd_data'
ORDER BY numero";

$result = mysqli_query($conn,$taula);
if (!$result) {die('Invalid query: ' . mysqli_error($conn));}

while (list($numero,$familia,$check0)=mysqli_fetch_row($result))
{
	$taula2 = "SELECT SUM(quantitat), SUM(cistella), SUM(cistella*preu*(1-descompte)*(1+iva))
	FROM comanda_linia
	WHERE numero='$numero'
	GROUP BY numero";

	$result2 = mysqli_query($conn,$taula2);
	if (!$result2) {die('Invalid query2: ' . mysqli_error($conn));}

	list($totcom,$totcist,$totpreu)=mysqli_fetch_row($result2);
	$totcom=sprintf("%01.2f",$totcom);
	$totcist=sprintf("%01.2f",$totcist);
	$totpreu=sprintf("%01.2f",$totpreu);

?>

<tr class='cos'>
<td align="left"><a class='link link--visitable'  href='factura.php?id=<?php echo $numero; ?>'>
<?php echo $familia; ?> (<?php echo $numero; ?>)</a></td>

<?php
	echo "<td align='center'>".$totcom."</td>";
	echo "<td align='center'>".$totcist."</td>";
	echo "<td align='center'>".$totpreu."€</td>";
	echo "</tr>";
}
?>

</table>
</div>
</div>

<p class="u-text-center">
<button class="button button--animated button--white" name="sortir" type="button"  onClick="javascript:window.location = 'admint.php';">Finalizar <i class="fa fa-check" aria-hidden="true"></i></button>
<button class="button button--animated button--white"  name="sortir" type="button" value="INCIDÈNCIES"
	onClick="javascript:window.location = 'cistella_incidencia.php?id=<?php echo $gproces.'&id2='.$ggrup.'&id3='.$gbd_data; ?>';">Incidencias</button>
</p>
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
