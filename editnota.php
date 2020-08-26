<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];

$numnota = $_GET['id'];
$del=$_GET['id2'];

$num=$_POST['num'];
$nom=$_POST['nom'];
$supernom=strtoupper($nom);
$text=$_POST['text'];
$tipus=$_POST['tipus'];
$dia=$_POST['dia'];
$mes=$_POST['mes'];
$any=$_POST['any'];

if ($any<100){
$any=$any+2000;
}

$data=$any."-".$mes."-".$dia;
$data2=$dia."-".$mes."-".$any;

include 'config/configuracio.php';



if ($numnota!="")
{
	if ($del!="")
	{
		$title="";
		$link="";
		$title2="Borrar nota";
	}
	else
	{
		$link=">>><a href='editnota.php?id=".$numnota."'>editar nota ".$numnota."</a>";
		$title2="Editar nota";
		$button='<p class="linia_button2" style="background: #a74fd7; text-align: center; vertical-align: middle;">
		<input class="button2" name="acceptar" type="button" id="acceptar" value="Guardar" onClick="validar2(this.form);">
		<input class="button2" name="eliminar" type="button" id="eliminar" value="Eliminar"
     onClick="var answer = confirm (\'Estas segur de borrar aquest producte!!\')
				if (answer)
					{window.location=\'editnota.php?id='.$numnota.'&id2=del \'}"></p>';
	}
}
else
{
	$link=">>><a href='editnota.php'>crear nota ".$numnota."</a>";
	$title2="Crear nota";
	$button='
	<p class="linia_button2" style="background: #a74fd7; text-align: center; vertical-align: middle;
	height:20px; padding:4px 0px;">
	<input class="button2" name="acceptar" type="button" id="acceptar" value="Acceptar" onClick="validar2(this.form);">
	</p>';
}

?>

<html lang="es">
	<head>
		<?php include 'head.php'; ?>
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>crear/editar nota ::: la coope</title>

<script language="javascript" type="text/javascript">

function validar2(form) {

var totbe=1;

if ((editnota.dia.value == "") || (isNaN(editnota.dia.value)) ||
(editnota.dia.value>=32) || (editnota.dia.value==0)) {
     alert ('DIA ha de ser un valor numèric entre 1 i 31');
	  editnota.dia.focus();
     totbe=0;
}

if ((editnota.mes.value == "") || (isNaN(editnota.mes.value)) ||
(editnota.mes.value>=13) || (editnota.mes.value==0)) {
     alert ('MES ha de ser un valor numèric entre 1 i 12');
	  editnota.mes.focus();
     totbe=0;
}

if ((editnota.any.value == "") || (isNaN(editnota.any.value))) {
     alert ('ANY ha de ser un valor numèric entre 0 i 99');
	  editnota.any.focus();
     totbe=0;
}


if (totbe==1) {
form.submit();
	}

}

</script>


</head>
<body>
<?php include 'menu.php'; ?>
<div class="pagina" style="margin-top: 10px;">
<div class="contenidor_1" style="border: 1px solid #a74fd7;">
<p class='path'>
><a href='admint.php'>administració</a>
>><a href='notes.php'>introduir notes a l'escriptori</a>
 <?php echo $link; ?>
</p>
<p class="h1" style="background: #a74fd7; text-align: left; padding-left: 20px;">
<?php echo $title2.' '.$numnota; ?>
</p>

<?php
if ($nom!="")
{
	if (!$num)
	{
		$taula= "SELECT nom FROM notescrip WHERE nom='$nom'";
		$result=mysqli_query($conn,$taula);
		if (!$result) { die('Invalid query taula: ' . mysqli_error($conn));}

		$check=mysqli_num_rows($result);
		if ($check != 0)
		{
		echo "<p class='error' style='font-size: 14px;'>El NOM ".$supernom." ja existeix.</p>
		<p class='error' style='font-size: 14px;'>Hauries d'usar-ne un altre o editar el que ja existeix:</p>
		<p class='error' style='font-size: 14px;'><a href='notes.php'>Torna enrera</a></p>";
		}
		else
		{
		$query3="INSERT INTO notescrip (nom,text,tipus,caducitat)
		VALUES ('$nom', '$text', '$tipus', '$data')";
		mysqli_query($conn,$query3) or die('Error, la inserció de dades query3 no ha estat possible');

		echo "<p class='error' style='font-size: 14px;'>La nota anomenada ".$supernom."
		s'ha guardat satisfactòriament</p>";
		}
	}
	else
	{
		$query2="UPDATE notescrip
		SET nom='$nom', text='$text', tipus='$tipus', caducitat='$data'
		WHERE numero='$num'";
		mysqli_query($conn,$query2) or die("Error, no s'han pogut modificar les dades");

		echo "<p class='error' style='font-size: 14px;'>Les dades de la nota ".$num." - ".$nom."
		s'han guardat satisfactòriament</p>";

		echo '<table width="70%" align="center" class="cos2" style="margin-bottom: 20px;">
		<tr><td>Numero:</td><td>'.$num.'</td></tr>
		<tr><td>Nom:</td><td>'.$nom.'</td></tr>
		<tr><td>Text:</td><td>'.$text.'</td></tr>
		<tr><td>Taules:</td><td>'.$tipus.'</td></tr>
		<tr><td>Caducitat:</td><td>'.$data2.'</td></tr>
		</table>';
	}
}
else
{
	if ($numnota!="")
	{
		if ($del!="")
		{
			$query2="DELETE FROM notescrip WHERE numero='$numnota'";
			mysqli_query($conn,$query2) or die("Error, no s'han pogut borrar les dades");
			die ("<p class='error' style='font-size: 14px;'>
			La nota numero ".$numnota." s'ha eliminat satisfactòriament
			</p>");
		}
		else
		{
		$select= "SELECT * FROM notescrip WHERE numero='$numnota'";
		$query=mysqli_query($conn,$select);
		if (!$query) {die('Invalid query: ' . mysqli_error($conn));}

		list($num,$nom,$text,$tipus,$caduc)=mysqli_fetch_row($query);
		list($any, $mes, $dia) = explode("-", $caduc);
		$caduc2=$dia."-".$mes.'-'.$any;
		}
	}
	else
	{
		$num=""; $nom=""; $text=""; $tipus=""; $caduc=""; $caduc2="";
	}
?>

<div class="contenidor_fac" style="border: 1px solid #a74fd7; width: 600px;margin-bottom: 20px;" >
<form action="editnota.php?id=<?php echo $numnota; ?>" method="post" name="editnota" id="editnota" >
<table width="100%" align="center" valign="middle" cellpadding="5" cellspacing="5">
<tr>
<td class="cos_majus">Numero:</td><td><input align="right" name="num" id="num" type="TEXT" maxlength="5" size="5"
value="<?php echo $num; ?>" readonly>
</td></tr>

<tr><td class="cos_majus">Nom:</td><td><input align="right" name="nom" id="nom" type="TEXT" maxlength="50" size="50"
value="<?php echo $nom; ?>"></td></tr>
<tr><td><span class="cos_majus">Text:</span><br/><span class="cos">(permet html)</span></td>
<td>
<TEXTAREA name="text" id="text" rows="8" cols="60">
<?php echo $text; ?>
</TEXTAREA>
</td></tr>

<tr><td class="cos_majus">Tauler:</td>
<td><SELECT name="tipus">
<?php
if ($tipus=='dreta'){
echo "<option value='dreta' selected>dreta";
echo "<option value='esquerra'>esquerra";
}
else{
echo "<option value='dreta'>dreta";
echo "<option value='esquerra' selected>esquerra";
}
?>
</SELECT></td></tr>

<tr><td class="cos_majus">Caducitat:</td>
<td><input value="<?php echo $dia; ?>" align="right" name="dia" id="dia" type="TEXT" maxlength="2" size="2">
 - <input value="<?php echo $mes; ?>" align="right" name="mes" id="mes" type="TEXT" maxlength="2" size="2">
 - <input value="<?php echo $any; ?>" align="right" name="any" id="any" type="TEXT" maxlength="2" size="2">
</td></tr>


</table>

<?php echo $button; ?>

</div></div></div>
</table>

<?php
	}
?>

</body>
</html>


<?php
include 'config/disconect.php';
}
else {
header("Location: index.php");
}
?>

