<?php
session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

$user = $_SESSION['user'];

$gproces=$_GET['id'];
$ggrup=$_GET['id2'];
$gbd_data=$_GET['id3'];

date_default_timezone_set("Europe/Madrid");
$gdata=date('d-m-Y',strtotime("$gbd_data"));

include 'config/configuracio.php';

function generaCodiCistella($lenght){
	//generació codi cistella personalitzat
	$string=array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","r","s","t","u","v","w","x","y","z","1","2","3","4","5","6","7","8","9","0");
	$codi="";
	for($i=1; $i<=$lenght; $i++){
		$codi.=$string[rand(0,34)];
	}
	return $codi;
}

function updateCistella($codi, $numero, $numfact){
	// update report0, check0, data2, numfact
	$data2= date("Y-m-d");
	$taula7 = "UPDATE comanda SET report0='".$codi."', check0='1', data2='$data2', check1='1',
	check2='1', numfact='".$numfact."' WHERE numero='".$numero."'";
	$result7 = mysqli_query($conn,$taula7);
	if (!$result7) {die('Invalid query7: ' . mysqli_error($conn));}
}


function laCuenta($numero){
	$selectCuenta = "SELECT SUM(cistella*preu*(1+iva)*(1-descompte)) FROM comanda_linia WHERE numero='".$numero."'";
	$resultCuenta = mysqli_query($conn,$selectCuenta);
	if (!$resultCuenta) {die('Invalid query: ' . mysqli_error($conn));}
	$cuenta = mysqli_fetch_row($resultCuenta);
	return $cuenta;
}

$taula = "SELECT numero, usuari, check0
FROM comanda
WHERE proces='$gproces' AND grup='$ggrup' AND data='$gbd_data'
ORDER BY numero";

$result = mysqli_query($conn,$taula);
if (!$result) {die('Invalid query: ' . mysqli_error($conn));}

while (list($numero,$familia,$check0)=mysqli_fetch_row($result))
{
	//hace la cuenta y la pone en negativo
	$cuenta = laCuenta($numero);
	$cuenta = -$cuenta[0];

	//check0 = 0 se registra por primera vez
	if ($check0==0)
	{
		//generació codi cistella personalitzat
	$lenght = 12;
	$codi = generaCodiCistella($lenght);

	// trobem la darrera factura de l'any vigent
	// i creem el numero de factura següent

	$currentyear= date("Y");
	$taulanf2 = "SELECT numfact
	FROM comanda
	WHERE YEAR(data2)=".$currentyear."
	ORDER BY numfact DESC
	LIMIT 1";
	$resultnf2 = mysqli_query($conn,$taulanf2);
	if (!$resultnf2) {die('Invalid query: ' . mysqli_error($conn));}
	list($lastnumfact)=mysqli_fetch_row($resultnf2);

	if ($lastnumfact!="")
	{
	$numfact=$lastnumfact+1;
		}
	else {
	$numfact=1;
		}

	// update report0, check0, data2, num factura
		updateCistella($codi, $numero, $numfact);

	//
	////////////////////////////////
	//enviament correu electrònic///
	////////////////////////////////

	$taula5 = "SELECT email1, email2 FROM usuaris WHERE nom='".$familia."'";
	$result5 = mysqli_query($conn,$taula5);
	if (!$result5) {die('Invalid query5: ' . mysqli_error($conn));}
	list($email1,$email2)=mysqli_fetch_row($result5);

	//A qui va dirigit
	$to  = $email1.", ".$email2;

	// Tema
	$subject = "Kidekoop: Eskaera ".$numero." balioztatuta";

	//Trobem el directori base http a partir d'on construim l'enllaç amb la factura
	$path_parts = pathinfo($_SERVER['HTTP_REFERER']);
	$dirbase=$path_parts['dirname'];

	// Missatge
	$message = '
	La vostra cistella corresponent a la comanda '.$numero.' està servida \n
	Podeu veure la factura al següent enllaç '.$dirbase.'/factura.php?id='.$numero.'&id2='.$codi.'&id3=0 \n
	La factura ja ha generat el cobrament al moneder, en cas que hi hagi qualsevol problema, envia una incidència.
	';

	// Capçaleres
	$headers   = array();
	$headers[] = 'From: cistellaires';
	$headers[] = 'Reply-To: '.$email_economia; ///$email_economia es troba definit a l'arxiu de configuració///
	$headers[] = "X-Mailer: PHP/".phpversion();

	// Enviant correu
	mail($to, $subject, $message, implode("\r\n", $headers));

	//escribe en la tabla incidencia, que pero no tiene nada a que ver con las incidencias
	//está relacionado con los correos
	$data_c= date("Y-m-d-H-i-s");
	$taula6 = "INSERT INTO incidencia VALUES('cistellaires', '$familia', '$subject', '$text', '$data_c', '0')";
	$result6 = mysqli_query($conn,$taula6);
	if (!$result6) {die('Invalid query6: ' . mysqli_error($conn));}
	}
	else
	{
	//check0 = 1 significa que la cistella había sido ya registrada
	//si es 0 se registra por primera vez

	//busca el último record cargado al moneder de la familia para esta factura o comanda
	//busca la fecha más reciente en el moneder
	$selectCobroReciente = "SELECT max(sessio) as fechaMax FROM moneder WHERE familia='$familia' AND concepte LIKE '%".$numero."'";
	$resultCobroReciente = mysqli_query($conn,$selectCobroReciente);
	if (!$resultCobroReciente) {die('Invalid query: ' . mysqli_error($conn));}
	$TrobaDataCobro = mysqli_fetch_row($resultCobroReciente);
	$fecha_maxima = $TrobaDataCobro[0];

	//busca el cobro con la fecha más reciente
	$selectBuscaCobro = "SELECT valor FROM moneder WHERE familia='$familia' AND concepte LIKE 'Factura num.%".$numero."' AND sessio = '$fecha_maxima'";
	$resultBuscaCobro = mysqli_query($conn,$selectBuscaCobro);
	if (!$resultBuscaCobro) {die('Invalid query: ' . mysqli_error($conn));}
	$TrobaCobro = mysqli_fetch_row($resultBuscaCobro);
	$cobro = $TrobaCobro[0];


		//si no es negativo pasa algo raro y tiene que avisar
		//de momento no hace nada, se pueden añadir consecuencias
		//esto es un parche, habría que añadir a la tabla monedero una causal de movimiento
		//por ejemplo utilizando códigos 0 = cobro, 1 = devolución, 2 = noseke
		//si es negativo lo transforma en positivo para compensar el moneder
		if ($cobro>=0){}
		else {$cobro = -$cobro;}
		//anula el cobro anterior antes de actualizar el moneder con la cuenta real
		$session = date ("Y-m-d H:i:s");
		$selectAnulaCobro = "INSERT INTO moneder(sessio, user, data, familia, concepte, valor) VALUES ('".$session."','".$user."','".date('Y-m-d')."','".$familia."','Anulacio Factura num. ".$numero."','".$cobro."')";
		$resultAnulaCobro = mysqli_query($conn,$selectAnulaCobro);
		if (!$resultAnulaCobro) {die('Invalid query: ' . mysqli_error($conn));}
	}

	//descuenta el valor de la cestella del moneder de la familia
	$session4 = date ("Y-m-d H:i:s");
	$selectMoneder2 = "INSERT INTO moneder(sessio, user, data, familia, concepte, valor) VALUES ('".$session4."','".$user."','".date('Y-m-d')."','".$familia."','Factura num. ".$numero."','".$cuenta."')";
	$resultMoneder2 = mysqli_query($conn,$selectMoneder2);
	if (!$resultMoneder2) {die('Invalid query: ' . mysqli_error($conn));}
}

//generació codi grup cistelles
$lenght = 6;
$codi2 = generaCodiCistella($lenght);

?>

<html lang="es">
	<head>
		<?php include 'head.php'; ?>
		<title>aplicoop - primer check pedido</title>
	</head>


<body>
<?php include 'menu.php'; ?>
<div class="page">
    <div class="container">

		<h1>Generación código de edición</h1>


<?php


$select= "SELECT check1,codi
	FROM cistella_check
	WHERE proces='$gproces' AND grup='$ggrup' AND data='$gbd_data'";
$result = mysqli_query($conn,$select);
if (!$result) {die('Invalid query: ' . mysqli_error($conn));}

if (mysqli_num_rows($result) > 0)
{
	$taula3= "UPDATE cistella_check
	SET check1='1', codi='$codi2'
	WHERE  proces='$gproces' AND grup='$ggrup' AND data='$gbd_data'";
	$result3 = mysqli_query($conn,$taula3);
	if (!$result3) {die('Invalid query3: ' . mysqli_error($conn));}
}

else
{
	$taula2="INSERT INTO cistella_check
	VALUES ('$gproces','$ggrup','$gbd_data','1','$codi2')";
	$result2 = mysqli_query($conn,$taula2);
	if (!$result2) {die('Invalid query2: ' . mysqli_error($conn));}
}

?>
<div class="box">
<p class="alert alert--info" align="center" style="font-size: 14px; padding-top: 50px;" >
  La edición de cestas queda cerrada. A partir de ahora sólo se podrá entrar con el siguiente código:
  </br>
  <span class="u-text-bold" style="font-size: 3rem;"><?php echo $codi2; ?><span>
</p>

<p class="u-text-center">
<button class="button button--animated button--white" name="fact" type="button" onClick="javascript:window.location = 'cistella_check2.php?id=<?php echo $gproces.'&id2='.$ggrup.'&id3='.$gbd_data; ?>';">Facturas</button>
<button class="button button--animated button--white" name="sortir" type="button"  onClick="javascript: history.go(-1);">Atrás <i class="fa fa-undo" aria-hidden="true"></i></button>
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
