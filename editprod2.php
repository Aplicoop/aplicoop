<?php
session_start();

if ($_SESSION['image_is_logged_in'] == 'true' )
{

	$user = $_SESSION['user'];

	$gnom = $_GET['id'];
	$gprov = $_GET['id2'];
	$que = $_GET['id3'];


	$pnom=$_POST['nom'];
	$punitat=$_POST['unitat'];
	$pprov=$_POST['prov'];
	$pcat=$_POST['tipus'];
	$subm_cat=$_POST['subm_cat'];
	$psubcat=$_POST['subtipus'];
	$pactiu=$_POST['actiu'];
	$ppreu=$_POST['preu'];
	$pestoc=$_POST['estoc'];
	$pnotes=$_POST['notes'];

	include 'config/configuracio.php';

?>


<html lang="es">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>crear-editar nou producte ::: la coope</title>



<script language="javascript" type="text/javascript">

function Validate(){

var nom = document.getElementById("nom").value;
var unitat = document.getElementById("unitat").value;
var prov = document.getElementById("prov").value;
var cat = document.getElementById("tipus").value;
var preu = document.getElementById("preu").value;


if (nom=="") {
alert ("T'has deixat el nom en blanc");
document.getElementById("nom").focus();
return false;
}

if (unitat==""){
var answer = confirm("T'has deixat unitat buida? \nD'acord: Continuar \nCancelar: Tornar a omplir el camp unitat");
	if (answer){
	return true;
	}
	else
	{
	document.getElementById("unitat").focus();
	return false;
	}
}

if (prov=="") {
alert ("No has elegit cap proveïdora");
document.getElementById("prov").focus();
return false;
}

if (cat=="") {
alert ("No has elegit cap categoria");
document.getElementById("tipus").focus();
return false;
}

if (preu==""){
var answer = confirm("T'has deixat preu buit? \nD'acord: Continuar \nCancelar: Tornar a omplir el camp preu");
	if (answer){
	return true;
	}
	else
	{
	document.getElementById("preu").focus();
	return false;
	}
}

var illegalChars= /[\<\>\'\;\:\\\/\"\+\!\¡\º\ª\$\|\@\#\%\¬\=\?\¿\{\}\_\[\]]/
if (nom.match(illegalChars)) {
   alert ('A nom: només s/accepten lletres, numeros, espai en blanc, punts, comes, guió alt i parentesis');
		document.getElementById("nom").focus();
		return false;
}

if (isNaN(preu)) {
alert ('A preu: només s/accepten numeros i el punt decimal');
document.getElementById("preu").focus();
return false;
}

if (preu<0) {
alert ('A preu: el numero ha de ser superior que 0');
document.getElementById("preu").focus();
return false;
}

if (preu.indexOf('.') == -1) preu += ".";
dectext = preu.substring(preu.indexOf('.')+1, preu.length);

if (dectext.length > 2)
{
alert('A preu: el numero de decimals no pot ser superior a 2');
document.getElementById("preu").focus();
return false;
}

return true;
}


function dropdownlist(listindex)
{

	document.nouprod.subtipus.options.length = 0;
	switch (listindex)
	{
<?php

	$query9= "SELECT tipus FROM categoria ORDER BY tipus";
	$result9=mysqli_query($conn,$query9);
	if (!$result9) { die("Query9 to show fields from table categoria failed");}
	while (list($jtipus)=mysqli_fetch_row($result9))
	{
?>
		case "<?php echo $jtipus; ?>":
		document.nouprod.subtipus.options[0]=new Option("elegeix subcategoria","");
<?php
		$query8= "SELECT subcategoria FROM subcategoria
		WHERE categoria='".$jtipus."' ORDER BY subcategoria";
		$result8=mysqli_query($conn,$query8);
		if (!$result8) {die("Query8 to show fields from table subcategoria failed");}
		$i=1;
		while (list($jsubcat)=mysqli_fetch_row($result8))
		{
?>
		document.nouprod.subtipus.options[<?php echo $i; ?>]=new Option("<?php echo $jsubcat; ?>","<?php echo $jsubcat; ?>");
<?php
			$i++;
		}
?>
		break;
<?php
	}
?>
	}
return true;
}

</script>
</head>

<?php
	$supernom=strtoupper($gnom);
	$head3=" >>><a href='editprod.php?id=".$gnom."&id2=".$gprov."'>editar producte ".$gnom."</a>";
	$tit='<p class="h1" style="background: #990000; text-align: left; padding-left: 20px;">Editar producte '.$supernom.'</p>';
	$subtit='Per editar un producte realitza els canvis en la fitxa i clica el botó GUARDAR
	al final per fer-los efectius. Per eliminar un producte clica el boto ELIMINAR.';
	$formact="editprod.php?id=".$gnom."&id2=".$gprov."&id3=edit";
	$width="50%";
	$buteli='<p class="linia_button2" style="background: #990000; text-align: center; vertical-align: middle;">
				<input class="button2" type="submit" value="GUARDAR">
				<input class="button2" name="eliminar" type="button" id="eliminar" value="ELIMINAR"
     			onClick="var answer = confirm (\'Estas segur de borrar aquest producte!!\')
				if (answer)
					{window.location=\'editprod.php?id='.$gnom.'&id2='.$gprov.'&id3=elim\'}"></p>';
	if (!$gnom)
	{
		$supernom=strtoupper($pnom);
		$head3=">>><a href='editprod.php'>crear nou producte</a>";
		$tit='<p class="h1" style="background: #990000; text-align: left; padding-left: 20px;">Crear nou producte</p>';
		$subtit='Per crear un nou producte omple el formulari i clica el botó GUARDAR al final.';
		$formact="editprod.php?id3=create";
		$width="100%";
		$buteli='<p class="linia_button2" style="background: #990000; padding:4px 0px;
					height: 20px; text-align: center; vertical-align: middle;">
					<input class="button2" type="submit" value="GUARDAR"></p>';
	}
?>

<body>
<div class="pagina" style="margin-top: 10px;">
<div class="contenidor_1" style="border: 1px solid #990000;">
<p class='path'>
><a href='admint.php'>administració</a>
 >><a class='Estilo2' href='productes.php'>editar, crear, eliminar productes</a>
<?php echo $head3; ?>
</p>

<?php echo $tit; ?>

<?php

if ($que=='create')
{
	$select= "SELECT nom,proveidora FROM productes
	WHERE nom='".$pnom."' AND proveidora='".$pprov."'";
	$query=mysqli_query($conn,$select);
	if (!$query) {die('Invalid query: ' . mysqli_error($conn));}
	if (mysqli_num_rows($query) == 1)
   {
   	die
   	("<p class='comment'>El producte ".$pnom." de la proveïdora ".$pprov." ja existeix.</p>");
	}
 	else
 	{
 		$query2= "INSERT INTO productes
 				VALUES ('".$pnom."','".$punitat."','".$pprov."','".$pcat."','".$psubcat."','".$pactiu."','".$ppreu."','0','".$pnotes."')";
		mysqli_query($conn,$query2) or die('Error, insert query2 failed');
		die ("<p class='comment'>El producte ".$supernom." s'ha introduït correctament a la base de dades:</p>
			<p class='cos2'>unitat: ".$punitat."</p>
			<p class='cos2'>proveïdora: ".$pprov."</p>
			<p class='cos2'>categoria: ".$pcat."</p>
			<p class='cos2'>subcategoria: ".$psubcat."</p>
			<p class='cos2'>actiu: ".$pactiu."</p>
			<p class='cos2'>preu: ".$ppreu."</p>
			<p class='cos2'>comentaris: ".$pnotes."</p>");
	}
}

if ($que=='edit')
{
	$query3= "UPDATE productes SET unitat='".$punitat."',categoria='".$pcat."',
	subcategoria='".$psubcat."',actiu='".$pactiu."',preu='".$ppreu."',notes='".$pnotes."'
	WHERE nom='".$gnom."' AND proveidora='".$gprov."'";
	mysqli_query($conn,$query3) or die('Error, insert query3 failed');
	die ("<p class='comment'>El producte ".$supernom." ha canviat a les següents dades:</p>
			<p class='cos2'>unitat: ".$punitat."</p>
			<p class='cos2'>proveïdora: ".$gprov."</p>
			<p class='cos2'>categoria: ".$pcat."</p>
			<p class='cos2'>subcategoria: ".$psubcat."</p>
			<p class='cos2'>actiu: ".$pactiu."</p>
			<p class='cos2'>preu: ".$ppreu."</p>
			<p class='cos2'>comentaris: ".$pnotes."</p>");
}

if ($que=='elim')
{
	$query4= "SELECT producte FROM albara_linia
	WHERE producte='".$gnom."'";
	$result4 = mysqli_query($conn,$query4);
	if (!$result4) {die('Invalid query4: ' . mysqli_error($conn));}
	if (mysqli_num_rows($result4) != 0)
	{
		die
   	("<p class='comment'>El producte ".$gnom." de la proveïdora ".$gprov." ja ha estat utilitzat.</p>
   	<p class='commnet'>Pots desactivar-lo, però no borrar-lo</p>");
	}
	$query5= "SELECT cl.ref, pr.nom FROM comanda_linia AS cl, productes AS pr
	WHERE cl.ref=pr.ref AND pr.nom='$gnom'";
	$result5 = mysqli_query($conn,$query5);
	if (!$result5) {die('Invalid query5: ' . mysqli_error($conn));}
	if (mysqli_num_rows($result5) != 0)
	{
		die
   	("<p class='coment'>El producte ".$gnom." de la proveïdora ".$gprov." ja ha estat utilitzat.</p>
   	<p class='comment'>Pots desactivar-lo, però no borrar-lo</p>");
	}

	$query6= "DELETE FROM productes WHERE nom='".$gnom."' AND proveidora='".$gprov."'";
	mysqli_query($conn,$query6) or die('Error, insert query6 failed');
	die
   	("<p class='comment'>El producte ".$gnom." de la proveïdora ".$gprov." s'ha eliminat de la base de dades.</p>");
}
?>


<?php

	$readonly="";
	if ($gnom!="")
	{
		$select= "SELECT * FROM productes
		WHERE nom='".$gnom."' AND proveidora='".$gprov."'";
		$query=mysqli_query($conn,$select);
		if (!$query) {die('Invalid query: ' . mysqli_error($conn));}

		list($nom,$unitat,$proveidora,$tipus,$subtipus,$actiu,$preu,$estoc,$notes)=mysqli_fetch_row($query);
		$readonly="readonly";
	}

?>

<div class="contenidor_fac" style=" border: 1px solid #990000; margin-bottom:20px;">
<table width="80%" align="center">
<form action="<?php echo $formact; ?>" method="post" name="nouprod" id="nouprod" onSubmit="return Validate();">
<tr style="padding-top: 10px;">
<td class="cos_majus">Nom:</td><td class="cos_majus"><input align="right" name="nom" id="nom" type="TEXT" maxlength="50" size="50"
value="<?php echo $nom; ?>" <?php echo $readonly; ?>>
</td></tr>

<tr><td class="cos_majus">Unitat:</td><td><input align="right" name="unitat" id="unitat" type="TEXT" maxlength="20" size="5"
value="<?php echo $unitat; ?>"></td></tr>
<tr><td class="cos_majus">Proveidora:</td>

<?php
	if ($gprov!="")
	{
		echo '<td>
		<input align="right" name="prov" id="prov" type="TEXT" maxlength="30" size="20" value="'.$gprov.'" readonly>
		</td>';
	}
	else
	{
	echo '<td><SELECT name="prov" id="prov" size="1" maxlenght="30"><option value="">elegeix proveïdora</option>';
	$query= "SELECT nom FROM proveidores ORDER BY nom";
	$result=mysqli_query($conn,$query);
	if (!$result) { die("Query to show fields from table proveidores failed");}
	while (list($sprov)=mysqli_fetch_row($result))
	{
		echo "<option value='".$sprov."'>".$sprov."</option>";
	}
	echo "</SELECT></td>";
	}
?>
</tr>

<tr><td class="cos_majus">Categoria:</td>
<td><SELECT id="tipus" name="tipus" size="1" maxlenght="30"
onChange="javascript: dropdownlist(this.options[this.selectedIndex].value);">
<option value="">elegeix categoria</option>

<?php

	$query= "SELECT tipus FROM categoria ORDER BY tipus";
	$result=mysqli_query($conn,$query);
	if (!$result) { die("Query to show fields from table tipus_prod failed");}
	while (list($stipus)=mysqli_fetch_row($result))
	{
		if ($stipus==$tipus)
		{
			echo "<option value='".$stipus."' selected>".$stipus."</option>";
		}
		else
		{
			echo "<option value='".$stipus."'>".$stipus."</option>";
		}
	}
?>
</SELECT></td></tr>

<tr><td class="cos_majus">Subcategoria: </td>
<td>
<script type="text/javascript" language="JavaScript">
document.write('<select name="subtipus" id="subtipus"><option value="">elegeix subcategoria</option></select>')
</script>
<noscript>
<select name="subtipus" id="subtipus" >
<option value="">elegeix subcategoria</option>
</select>
</noscript>
</td></tr>

<tr><td class="cos_majus">Actiu:</td>

<?php
$checked1='checked'; $checked2="";
if ($actiu=='baixa') {$checked1=""; $checked2="checked";}
?>
<td>
<INPUT type='radio' name='actiu' value='actiu' <?php echo $checked1; ?>>si</INPUT>
<INPUT type='radio' name='actiu' value='baixa' <?php echo $checked2; ?>>no</INPUT>
</td></tr>

<tr><td class="cos_majus">Preu:</td>
<td><input align="right" name="preu" id="preu" type="TEXT" maxlength="7" size="5" value="<?php echo $preu; ?>">
</td></tr>

<?php
if ($estoc!="")
{
?>
<tr><td class="cos_majus">Estoc:</td>
<td><input align="right" name="estoc" id="estoc" type="TEXT" maxlength="7"
size="5" value="<?php echo $estoc; ?>" readonly>
</td></tr>
<?php
}
?>

<tr><td class="cos_majus">Comentaris:</td>
<td><input align="right" name="notes" id="notes" type="TEXT" maxlength="255" size="35" value="<?php echo $notes; ?>">
</td></tr>
</table>

<?php echo $buteli; ?>
</div>

<p class="cos2" style="clear: both; text-align: center; padding: 0px 100px;">
<?php echo $subtit; ?>
</p>
</div></div>
</body>
</html>


<?php
include 'config/disconect.php';
}
else {
header("Location: index.php");
}
?>
