<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' OR ($_GET['id'] != "" AND strlen($_GET['id2']) == 12 AND ($_GET['id3'] == "0" OR $_GET['id3'] == "1")))
{
	$user = $_SESSION['user'];
	$superuser=strtoupper($_SESSION['user']);
	$numcmda=$_GET['id'];
	$codi=$_GET['id2'];
	$val=$_GET['id3'];

	include 'config/configuracio.php';

	date_default_timezone_set('Europe/Madrid');
   $session= date ("Y-m-d H:i:s", $_SESSION['timeinitse']);

?>

<html lang="es">
	<head>
		<?php include 'head.php'; ?>
		<title>aplicoop - factura</title>
	</head>

<body>
<?php include 'menu.php'; ?>
<div class="page">
	<div class="container">
		<div class="box">

			<div class="hidden-print">

<?php
	if ($_SESSION['image_is_logged_in'] != 'true')
	{
		$sel2="SELECT numero, report0
		FROM comanda
		WHERE numero='$numcmda' AND report0='$codi'";
		$query2=mysqli_query($conn,$sel2) or die('query2:'.mysqli_error($conn));
		if (mysqli_num_rows($query2)!=1)
		{
			die ('<p class="error" style="font-size: 14px;">
			Entra a l\'aplicatiu per veure la factura '.$numcmda.' o valida-la des del correu
			de l\'escriptori</p>');
		}
	}

	if ($val=='1')
	{
		$sel3="UPDATE comanda SET check1='1' WHERE numero='$numcmda'";
		$query3=mysqli_query($conn,$sel3) or die('query3:'.mysqli_error($conn));

		$sel2a="SELECT numfact, data2 FROM comanda WHERE numero='$numcmda'";
		$query2a=mysqli_query($conn,$sel2a) or die('query2a:'.mysqli_error($conn));
		list($numfact2, $data2)=mysqli_fetch_row($sel2a);

		$yearfact=strtotime($data2);
		$yearfact=date('Y',$yearfact);
		$numfact2=$numfact2."/".$yearfact;

		echo '<p class="error" style="font-size: 14px;">La factura '.$numfact2.' ha estat validada
		satisfactòriament</p>';
	}
	else
	{

		$sel="SELECT c.usuari, u.nomf, u.adressf, u.niff, c.proces, c.data, c.check0, c.report0, c.data2, c.check1, c.numfact, c.notes
		FROM comanda AS c, usuaris AS u
		WHERE c.numero='$numcmda' AND c.usuari=u.nom";
		$query=mysqli_query($conn,$sel) or die('query:'.mysqli_error($conn));
		list($familia, $nomf, $adressf, $niff, $proces, $bd_data, $check0, $acatreport0,
		$bd_data2, $check1, $numfact, $notes) = mysqli_fetch_row($query);

		/// creem numero de factura
		$factyear=strtotime($bd_data2);
		$factyear=date('Y',$factyear);
		$numfact=$numfact."/".$factyear;

		list($anyrc, $mesrc, $mdiarc) = explode("-", $bd_data);
		$ver_data=$mdiarc." - ".$mesrc." - ".$anyrc;
		list($anyrc2, $mesrc2, $mdiarc2) = explode("-", $bd_data2);
		$ver_data2=$mdiarc2." - ".$mesrc2." - ".$anyrc2;
		$yearfact=$anyrc2;

		if ($val=='2')
		{
			echo '<p class="error" style="font-size: 14px;">La factura '.$numfact.'
			ha estat validada satisfactòriament</p>';
		}

?>

		<div class="u-text-right  u-mb-2">
			<button class="button button--animated" name="imprimir" onclick="window.print();">Imprimir <i class="fa fa-print" aria-hidden="true"></i></button>

	<?php
			if ($_SESSION['image_is_logged_in'] == 'true')
			{
				echo'<button class="button button--animated" onClick="javascript: history.go(-1);">Volver <i class="fa fa-undo" aria-hidden="true"></i></button>';
			}
			//$logo_factura està definida a l'arxiu de configuració
	?>

		</div>
	</div>

			<div class="row">
				<div class="col-md-4 u-text-center u-mb-1">
					<img id="fig" class="img--responsive" style="height:85px;" src="<?php echo $logo_factura; ?>">
					<p>Kidekoop Elkartea
					CIF: G95749321<br>
					C/ Urazurrutia 26
					48003 BILBAO<p>
				</div>
				<div class="col-md-8 u-text-right u-mb-1">
					<span style="color: grey;">Factura nº: </span><span class="u-text-semibold"><?php echo $numfact; ?></span><br/>
					<span style="color: grey;">Fecha: </span><span class="u-text-semibold"><?php echo $ver_data2; ?></span><br/>
					<span style="color: grey;">Familia: </span><span class="u-text-semibold"><?php echo $nomf; ?></span><br/>
					<span style="color: grey;">Dirección: </span><span class="u-text-semibold"><?php echo $adressf; ?></span><br/>
					<span style="color: grey;">NIF: </span><span class="u-text-semibold"><?php echo $niff; ?></span><br/>


				</div>
			</div>


			<div class="cf u-mt-2 table-responsive u-width-100">
		<table width="100%"  class="table table-striped">
			<thead>
			<tr style="font-size:18px;">
				<td width="50%" align="left" class="u-text-semibold">Producto</u></td>
				<td width="10%" align="center" class="u-text-semibold">Cant.</td>
				<td width="10%" align="center" class="u-text-semibold">Unidad</td>
				<td width="10%" align="center" class="u-text-semibold">PVP</td>
				<td width="10%" align="center" class="u-text-semibold">Descuento</td>
				<td width="10%" align="center" class="u-text-semibold">IVA</td>
				<td width="10%" align="right" class="u-text-semibold">Total</td>
			</tr>
			</thead>
			<tbody>
<?php
		$sel5="SELECT cl.ref, prod.nom, prod.proveidora, prod.unitat, cl.cistella, cl.preu, cl.descompte, cl.iva
		FROM comanda_linia AS cl, productes AS prod
		WHERE cl.numero='$numcmda' AND cl.ref=prod.ref
		ORDER BY prod.categoria, prod.proveidora, prod.nom";
		$result5=mysqli_query($conn,$sel5) or die(mysqli_error($conn));

		$total=0; $total_import_brut=0; $totaliva=0;
		while (list ($ref, $nomprod, $nomprod2, $unitat, $cistella, $preu, $descompte, $iva)=
		mysqli_fetch_row($result5))
		{
			/// agafem la primera lletra de la unitat ///
			$unitat1=substr($unitat,0,1);

			//calculem import brut, iva línia, subtotal linia,
			///i totals import brut, iva i factura
			$importbrut=$cistella*$preu*(1-$descompte);
			$total_import_brut=$total_import_brut+$importbrut;
			$subtotal=$cistella*$preu*(1-$descompte)*(1+$iva);
			$subtotal=sprintf("%01.2f", $subtotal);
			$total=$total+$subtotal;
			$iva_linia=$cistella*$preu*(1-$descompte)*$iva;
			$iva_linia=sprintf("%01.2f", $iva_linia);
			$totaliva=$totaliva+$iva_linia;

			//iva i descompte si =0 apreixen en blanc//
			$v_descompte=$descompte*100;
			$v_iva=$iva*100;
			if ($iva==0){$v_iva="";} else {$v_iva=$v_iva." %";}
			if ($descompte==0){$v_descompte="";} else {$v_descompte=$v_descompte." %";}


?>

				<tr class="cos">
				<td><?php echo $ref.' - '.$nomprod; ?></td>
				<td align="center"><?php echo ($cistella + 0); ?></td>
				<td align="center"><?php echo $unitat; ?></td>
				<td align="center"><?php echo $preu; ?>&#8364;</td>
				<td align="center"><?php echo $v_descompte; ?></td>
				<td align="center"><?php echo $v_iva; ?></td>
				<td align="right"><?php echo $subtotal; ?>&#8364;</td>
				</tr>

<?php
		}
		$total=sprintf("%01.2f", $total);
		$totaliva=sprintf("%01.2f", $totaliva);
		$total_import_brut=sprintf("%01.2f", $total_import_brut);

		if ($val=='2')
		{
			$sel3="UPDATE comanda SET check2='1' WHERE numero='$numcmda'";
			$query3=mysqli_query($conn,$sel3) or die('query3: '.mysqli_error($conn));

			$sel4="INSERT INTO moneder VALUES ('".$session."','".$user."','".date('Y-m-d')."',
			'".$familia."','Factura num. ".$numcmda."','-".$total."','')";
			$query4=mysqli_query($conn,$sel4) or die('query4: '.mysqli_error($conn));

			$sel5="UPDATE usuaris SET moneder=moneder-".$total." WHERE nom='".$familia."'";
			$query5=mysqli_query($conn,$sel5) or die('query5: '.mysqli_error($conn));

		}
?></tbody>
		</table>
		<table width="100%" align="center">
		<tr class="cos_majus" valign="baseline">
				<td width="33%" class="u-text-semibold" align="center">Imp. Bruto</td>
				<td width="33%" class="u-text-semibold" align="center">IVA</td>
				<td width="33%" class="u-text-semibold" align="center">TOTAL</td>
		</tr>
		<tr class="cos12" style="font-weight: bold;">
				<td align="center"><?php echo $total_import_brut; ?>&#8364;</td>
				<td align="center"><?php echo $totaliva; ?>&#8364;</td>
				<td align="center"><?php echo $total	; ?>&#8364;</td>
		</tr>
	</table>
	</div>
	<p class="alert alert--info" >
		Tus datos proceden de un fichero del que es propietaria y responsable esta entidad, ante la cual pueden ejercitar los derechos de acceso, modificación, cancelación y oposición reconocidos por la LO 15/1999, de 13 de septiembre, de protección de datos de carácter personal.
	</p>

<?php
	}
	echo"</div></div></div></body></html>";
	include 'config/disconect.php';
}
else
{
	header("Location: index.php");
}
?>


