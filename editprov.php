<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];

$nompre = $_GET['id'];
$supernompre=strtoupper($nompre);
$elim = $_GET['id2'];

$pnom=$_POST['nom'];
$pactiu=$_POST['actiu'];
$pnomcomplert=$_POST['nomcomplert'];
$pcontacte=$_POST['contacte'];
$padress=$_POST['adress'];
$ptelf1=$_POST['telf1'];
$ptelf2=$_POST['telf2'];
$pfax=$_POST['fax'];
$pweb=$_POST['web'];
$pemail1=$_POST['email1'];
$pemail2=$_POST['email2'];
$pnotes=$_POST['notes'];

$psupernom=strtoupper($pnom);

if ($elim!="") {$link="";}

include 'config/configuracio.php';



?>

<html lang="es">
	<head>
		<?php include 'head.php'; ?>
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>editar proveidora ::: la coope</title>
	</head>

<body>
	<?php include 'menu.php'; ?>
<div class="pagina" style="margin-top: 10px;">
<div class="contenidor_1" style="border: 1px solid #990000;">

<p class='path'>
><a href='admint.php'>administració</a>
 >><a href='proveidores.php'>editar, crear, eliminar proveïdores</a>
>>><a href='editprov.php?id=<?php echo $nompre; ?>'>editar proveïdora <?php echo $nompre; ?></a>
</p>
<p class="h1" style="background: #990000; text-align: left; padding-left: 20px;">
Editar proveïdora <?php echo $nompre; ?></p>


<?php
if ($elim!="")
{
	$query = "SELECT nom,proveidora FROM productes WHERE proveidora='$nompre'";
	$result=mysqli_query($conn,$query);
	if (!$result) {die("Query to show fields from query failed");}
	$check=mysqli_num_rows($result);
	if ($check != 0)
	{
		echo "<p class='comment'>Les dades de l'empresa o persona proveïdora ".$supernompre." no
		poden borrar-se perquè tenen encara productes associats. Hauries d'eliminar el productes associats
		en primer terme. Si no pots, recorda que pots desactivar aquesta proveïdora.</p>";
		echo "<p class='comment'>Productes associats</p>";
		while (list($nom,$proveidora)=mysqli_fetch_row($result))
		{
			echo "<p class='cos2'>".$nom."-".$proveidora."</p>";
		}
		die;
	}
	else
	{
		$query2="DELETE FROM proveidores WHERE nom='$nompre'";
		mysqli_query($conn,$query2) or die("Error, no s'han pogut borrar les dades");

  		die ("<p class='comment'>S'han eliminat les dades de l'empresa o persona proveïdora ".$supernompre."
   	</p>");
	}
}

if ($pnom != "")
{
		$query2 = "UPDATE proveidores
		SET actiu='".$pactiu."',nomcomplert='".$pnomcomplert."',contacte='".$pcontacte."',
		adress='".$padress."',telf1='".$ptelf1."',telf2='".$ptelf2."',fax='".$pfax."',
		web='".$pweb."',email1='".$pemail1."',email2='".$pemail2."',notes='".$pnotes."'
	   WHERE nom='".$pnom."'";

		mysqli_query($conn,$query2) or die('Error, insert query2 failed');

		echo "<p class='comment'>Les dades de l'empresa o persona proveïdora ".$psupernom."
		s'han guardat satisfactòriament</p>";
		echo "<p class='cos2'>Nom:".$pnom."</p>";
		echo "<p class='cos2'>Actiu:".$pactiu."</p>";
		echo "<p class='cos2'>Nom complert:".$pnomcomplert."</p>";
		echo "<p class='cos2'>Contacte:".$pcontacte."</p>";
		echo "<p class='cos2'>Adreça:".$padress."</p>";
		echo "<p class='cos2'>Telèfon principal:".$ptelf1."</p>";
		echo "<p class='cos2'>Telèfon alternatiu:".$ptelf2."</p>";
		echo "<p class='cos2'>Fax:".$pfax."</p>";
		echo "<p class='cos2'>Web:".$pweb."</p>";
		echo "<p class='cos2'>e-mail principal:".$pemail1."</p>";
		echo "<p class='cos2'>e-mail alternatiu:".$pemail2."</p>";
		echo "<p class='cos2'>Comentaris:".$pnotes."</p>";
}
else
{
	$select= "SELECT * FROM proveidores WHERE nom='$nompre'";
	$query=mysqli_query($conn,$select);
	if (!$query) {    die('Invalid query: ' . mysqli_error($conn));    }

	list($nom,$actiu,$nomcomplert,$contacte,$adress,$telf1,$telf2,$fax,$web,$email1,$email2,$notes)=mysqli_fetch_row($query);

	if ($actiu=='activat'){ $checked1='checked'; $checked2='';}
	else { $checked1=''; $checked2='checked';}
?>

<div class="contenidor_fac" style=" width:500px; border: 1px solid orange; margin-bottom:20px;">
<form action="editprov.php?id=<?php echo $nompre; ?>" method="post" name="nouprov" id="nouprov" >

<table style="padding: 10px;" width="100%" align="center" cellspading="5" cellspacing="5" >
<tr><td class="cos_majus">Nom:(*)</td><td><input align="right" name="nom" id="nom" type="TEXT" maxlength="20" size="20"
value="<?php echo $nom; ?>" readonly>
</td></tr>
<tr><td class="cos_majus">Actiu:</td>
<td>
si<input type="radio" name="actiu" value="activat" id="actiu" <?php echo $checked1; ?> >
no<input type="radio" name="actiu" value="desactivat" id="actiu" <?php echo $checked2; ?>>
</td>
<tr><td class="cos_majus">Nom complert:</td><td><input value="<?php echo $nomcomplert; ?>" align="right" name="nomcomplert" id="nomcomplert" type="TEXT" maxlength="50" size="35"></td></tr>
<tr><td class="cos_majus">Contacte:</td><td><input value="<?php echo $contacte; ?>" align="right" name="contacte" id="contacte" type="TEXT" maxlength="100" size="35"></td></tr>
<tr><td class="cos_majus">Adreça:</td><td><input a value="<?php echo $adress; ?>"lign="right" name="adress" id="adress" type="TEXT" maxlength="255" size="35"></td></tr>
<tr><td class="cos_majus">Telèfon principal:</td><td><input value="<?php echo $telf1; ?>" align="right" name="telf1" id="telf1" type="TEXT" maxlength="9" size="9"></td></tr>
<tr><td class="cos_majus">Telèfon alternatiu:</td><td><input value="<?php echo $telf2; ?>" align="right" name="telf2" id="telf2" type="TEXT" maxlength="9" size="9"></td></tr>
<tr><td class="cos_majus">Fax:</td><td><input value="<?php echo $fax; ?>" align="right" name="fax" id="fax" type="TEXT" maxlength="9" size="9"></td></tr>
<tr><td class="cos_majus">Pàgina web:</td><td><input value="<?php echo $web; ?>" align="right" name="web" id="web" type="TEXT" maxlength="100" size="35"></td></tr>
<tr><td class="cos_majus">e-mail principal:</td><td><input value="<?php echo $email1; ?>" align="right" name="email1" id="email1" type="TEXT" maxlength="50" size="35"></td></tr>
<tr><td class="cos_majus">e-mail alternatiu:</td><td><input value="<?php echo $email2; ?>" align="right" name="email2" id="email2" type="TEXT" maxlength="50" size="35"></td></tr>
<tr><td class="cos_majus">Comentaris:</td><td><input value="<?php echo $notes; ?>" align="right" name="notes" id="notes" type="TEXT" maxlength="255" size="35"></td></tr>
</table>

<p class="linia_button2" style="background: #990000; text-align: center; vertical-align: middle;">
<input class="button2" name="acceptar" type="submit" id="acceptar" value="Acceptar">
<input class="button2" name="eliminar" type="button" id="eliminar" value="Eliminar"
     onClick="var answer = confirm ('Estas segur de borrar aquesta proveïdora!!')
				if (answer)
					{window.location='editprov.php?id=<?php echo $nompre; ?>&id2=elim'}">
</p>
</div></div></div>
</body>
</html>


<?php
}
include 'config/disconect.php';

}
else {
header("Location: index.php");
}
?>
