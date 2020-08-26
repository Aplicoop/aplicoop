<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];

$pnom=$_POST['nom'];
$supernom=strtoupper($nom);
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

?>

<html lang="es">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>crear nova proveidora ::: la coope</title>


<script languaje="javascript">


function validar2(form) {
var totbe=1;
if (nouprov.nom.value == ""){
     alert("NOM no pot estar buit");
     nouprov.nom.focus();
     totbe=0;
}else{
	for ( i = 0; i < nouprov.nom.value.length; i++ ) {
     if ( nouprov.nom.value.charAt(i) == " " ) {
         alert("NOM no pot tenir espais en blanc");
         nouprov.nom.focus();
         totbe=0;
         break;
         }
     }

}

if (totbe==1){
form.submit();
	}
}



function validar(e) { // 1

    tecla = (document.all) ? e.keyCode : e.which; // 2
    if (tecla==8) return true; // 3
    patron =/[A-Za-z\s]/; // 4
    te = String.fromCharCode(tecla); // 5
    return patron.test(te); // 6
}



</script>
</head>

<body>

<div class="pagina" style="margin-top: 10px;">
<div class="contenidor_1" style="border: 1px solid #990000">

<p class='path'>
><a href='admint.php'>administració</a>
>><a href='proveidores.php'>editar, crear, eliminar proveïdores</a>
 >>><a href='createprov.php'>crear nova proveïdora</a>
</p>
<p class="h1" style="background: #990000; text-align: left; padding-left: 20px;">
Crear nova proveïdora</p>





<?php
if ($pnom !="")
{
	include 'config/configuracio.php';
	$taula= "SELECT nom FROM proveidores WHERE nom='$pnom'";
	$result=mysqli_query($conn,$taula);
	if (!$result) {die('Invalid query taula: ' . mysqli_error($conn));}

	$check=mysqli_num_rows($result);
	if ($check != 0)
	{
		echo "<td><p align='center' class='Estilo1'>La proveïdora ".$supernom." no es pot crear de nou perquè ja existeix.</p>";
	}
	else
	{
		$query = "INSERT INTO proveidores (nom, actiu, nomcomplert, contacte, adress, telf1, telf2, fax, web, email1, email2, notes)
			VALUES ('$pnom', '$pactiu', '$pnomcomplert', '$pcontacte', '$padress', '$ptelf1', '$ptelf2', '$pfax', '$pweb', '$pemail1', '$pemail2', '$pnotes')";
		mysqli_query($conn,$query) or die('Error, la inserció de dades a query no ha estat possible');


		echo "<p class='comment'>Les dades de l'empresa o persona proveïdora ".$supernom."
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
}
else
{
?>
<div class="contenidor_fac" style=" width:500px; border: 1px solid #990000; margin-bottom:20px;">

<form action="createprov.php" method="post" name="nouprov" id="nouprov" >
<table style="padding: 10px;" width="100%" align="center" cellspading="5" cellspacing="5" >

<tr>
<td class="cos_majus">Nom:(*)</td><td><input align="right" name="nom" id="nom" type="TEXT" maxlength="20" size="20"
onKeyUp="form.nom.value=form.nom.value.toLowerCase()" onkeypress="return validar(event)"></td></tr>
<tr><td class="cos_majus">Activat:</td>
<td>
si<input type="radio" name="actiu" value="activat" id="actiu" checked >
no<input type="radio" name="actiu" value="desactivat" id="actiu" >
</td></tr>
<tr><td class="cos_majus">Nom complert:</td><td><input align="right" name="nomcomplert" id="nomcomplert" type="TEXT" maxlength="50" size="35"></td></tr>
<tr><td class="cos_majus">Contacte:</td><td><input align="right" name="contacte" id="contacte" type="TEXT" maxlength="100" size="35"></td></tr>
<tr><td class="cos_majus">Adreça:</td><td><input align="right" name="adress" id="adress" type="TEXT" maxlength="255" size="35"></td></tr>
<tr><td class="cos_majus">Telèfon principal:</td><td><input align="right" name="telf1" id="telf1" type="TEXT" maxlength="9" size="9"></td></tr>
<tr><td class="cos_majus">Telèfon alternatiu:</td><td><input align="right" name="telf2" id="telf2" type="TEXT" maxlength="9" size="9"></td></tr>
<tr><td class="cos_majus">Fax:</td><td><input align="right" name="fax" id="fax" type="TEXT" maxlength="9" size="9"></td></tr>
<tr><td class="cos_majus">Pàgina web:</td><td><input align="right" name="web" id="web" type="TEXT" maxlength="100" size="35"></td></tr>
<tr><td class="cos_majus">e-mail principal:</td><td><input align="right" name="email1" id="email1" type="TEXT" maxlength="50" size="35"></td></tr>
<tr><td class="cos_majus">e-mail alternatiu:</td><td><input align="right" name="email2" id="email2" type="TEXT" maxlength="50" size="35"></td></tr>
<tr><td class="cos_majus">Comentaris:</td><td><input align="right" name="notes" id="notes" type="TEXT" maxlength="255" size="35"></td></tr>
</table>

<p class="linia_button2" style="background: #990000; text-align: center; vertical-align: middle;
	height:20px; padding:4px 0px;">
<input class="button2" name="acceptar" type="button" id="acceptar" value="Acceptar" onClick="validar2(this.form);">
</p>
</div>

<p class="cos2" style="clear: both; text-align: center; padding: 0px 100px;">
Per crear una nova proveïdora clica el botó ACCEPTAR al final de la fitxa.
<br/>
(*) el NOM és un codi per reconèixer la proveïdora.
Ha d'anar tot en minuscules, sense espais en blanc, ni signes que no siguin lletres
(ni ç, ni ñ, ni accents).
</p>

<?php
}
?>

</div></div>
</body>
</html>

<?php
}
else {
header("Location: index.php");
}
?>
