<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];
$superuser=strtoupper($_SESSION['user']);

$pnom=$_POST['nom'];
$pgrup=$_POST['grup'];
$ptipus=$_POST['tipus'];
$pperiode=$_POST['periode'];
$pdiare=$_POST['diare'];
$pdiat=$_POST['diat'];
$phorat=$_POST['horat'];
$pdatai=$_POST['datai'];
$pdataf=$_POST['dataf'];
$pactiu=$_POST['actiu'];

include ('config/configuracio.php');

?>

<html lang="es">
	<head>
		<?php include 'head.php'; ?>
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>crear nou proces ::: la coope</title>

		 <!-- calendar stylesheet -->
  <link rel="stylesheet" type="text/css" media="all" href="calendar/calendar-win2k-1.css" title="win2k-1" />

  <!-- main calendar program -->
  <script type="text/javascript" src="calendar/calendar.js"></script>

  <!-- language for the calendar -->
  <script type="text/javascript" src="calendar/lang/calendar-cat.js"></script>

  <!-- the following script defines the Calendar.setup helper function, which makes
       adding a calendar a matter of 1 or 2 lines of code. -->
  <script type="text/javascript" src="calendar/calendar-setup.js"></script>


	</head>

<script languaje="javascript">

function change_tipus()
{
	var nom = document.getElementById("nom");
	var grup = document.getElementById("grup");
	var tipus = document.getElementById("tipus");

		if (nom.value=="") {
		alert ("T'has deixat el nom en blanc");
		tipus.value="";
		document.getElementById("nom").focus();
		return false;
		}
		if (grup.value=="") {
		alert ("T'has deixat el grup en blanc");
		tipus.value="";
		document.getElementById("grup").focus();
		return false;
		}
document.one.submit();
}

function validar_formulari()
{
	var nom = document.getElementById("nom");
	var grup = document.getElementById("grup");
	var tipus = document.getElementById("tipus");

		if (nom.value=="") {
		alert ("T'has deixat el nom en blanc");
		document.getElementById("nom").focus();
		return false;
		}
		if (grup.value=="") {
		alert ("T'has deixat el grup en blanc");
		document.getElementById("grup").focus();
		return false;
		}
	if (tipus.value=="") {
		alert ("Has d'escollir un tipus de procés");
		document.getElementById("tipus").focus();
		return false;
		}
	if (tipus.value == "període concret"){
		var datai = document.getElementById("f_date_a");
		array_datai=datai.value.split("/");
		var dataini=array_datai[2]+array_datai[1]+array_datai[0];
		var dataf = document.getElementById("f_date_b");
		array_dataf=dataf.value.split("/");
		var datafi=array_dataf[2]+array_dataf[1]+array_dataf[0];
		if (datai.value==""){
			alert ("T'has deixat la data inicial en blanc");
			document.getElementById("f_date_a").focus();
			return false;
			}
		if (dataf.value==""){
			alert ("T'has deixat la data final en blanc");
			document.getElementById("f_date_b").focus();
			return false;
			}
		if (parseInt(datafi)<=parseInt(dataini)){
			alert ("La data final ha de ser igual o superior a la data inicial");
			document.getElementById("f_date_a").focus();
			return false;
			}
		}
	if (tipus.value == "continu")
	{
		var per = document.getElementById("periode");
		var diare = document.getElementById("diare");
		var diat = document.getElementById("diat");
		var horat = document.getElementById("horat");
		if (per.value==""){
			alert ("T'has deixat el període en blanc");
			document.getElementById("periode").focus();
			return false;
			}
			if (diare.value==""){
			alert ("T'has deixat el dia de recollida en blanc");
			document.getElementById("diare").focus();
			return false;
			}
			if (diat.value==""){
			alert ("T'has deixat el dia de tall en blanc");
			document.getElementById("diat").focus();
			return false;
			}
		if (horat.value==""){
			alert ("T'has deixat hora de tall en blanc");
			document.getElementById("horat").focus();
			return false;
			}

		var horat1=horat.value.substring(0,2);
		var horat2=horat.value.substring(2);
		if (isNaN(horat1) || isNaN(horat2) || horat1=="" || horat2=="" || horat.value.length<4)
		{
			alert ("L'hora de tall ha de ser una xifra de quatre nombres: els dos primers representen l'hora i els dos segons els minuts");
			document.getElementById("horat").focus();
			return false;
		}

		if (horat1<0 || horat1>23)
		{
			alert("A horat: els dos primers nombres han d'estar entre l'interval	00 i 23 ja que respresenten les hores del dia");
			document.getElementById("horat").focus();
			return false;
		}

		if (horat2<0 || horat2>60)
		{
			alert("A horat: els dos darrers nombres han d'estar entre l'interval	00 i 59 ja que respresenten els minuts");
			document.getElementById("horat").focus();
			return false;
		}
	}
return true;
}

</script>

<body>
<?php include 'menu.php'; ?>
<div class="pagina" style="margin-top: 10px;">
<div class="contenidor_1" style="border: 1px solid #66FF66;">
<p class='path'>
><a href='admint.php'>administració</a>
>><a href='editprocessos.php'>crear, editar i eliminar processos</a>
>>><a href='createproces.php'>crear nou procés</a>
</p>
<p class="h1" style="background: #66FF66; text-align: left; padding-left: 20px;">
Crear nou procés
</p>

<?php

if ($pnom!="" AND $pgrup!="" AND $pactiu!="")
{

	$select= "SELECT nom FROM processos
	WHERE nom='".$pnom."' AND grup='".$pgrup."' ";
	$result = mysqli_query($conn,$select) or die("Query failed. " . mysqli_error($conn));

   	if (mysqli_num_rows($result) == 1)
   	{
   		die
   		("<p class='comment'>
   		No es pot crear de nou el procés ".$pnom." del grup ".$pgrup." perquè ja existeix.
   		</p>");
		}
		else
		{

			$pdatai2 = explode ("/",$pdatai);
			$pdataf2 = explode ("/",$pdataf);
			$pdataini = $pdatai2[2]."/".$pdatai2[1]."/".$pdatai2[0];
			$pdatafin = $pdataf2[2]."/".$pdataf2[1]."/".$pdataf2[0];

			$query2 = "INSERT INTO processos
			VALUES ('".$pnom."', '".$pgrup."', '".$ptipus."', '".$pdataini."', '".$pdatafin."',
			'".$pperiode."', '".$pdiare."', '".$pdiat."', '".$phorat."', '".$pactiu."') ";

			mysqli_query($conn,$query2) or die('Error, insert query2 failed');

			echo
			"<p class='comment'>
			Un nou procés s'ha introduit a la base de dades:</p>
			<table width='25%' align='center' class='cos2' style='margin-bottom: 20px;'>
			<tr><td>Nom:</td><td>".$pnom."</td></tr>
			<tr><td>Grup:</td><td>".$pgrup."</td></tr>
			<tr><td>Tipus:</td><td>".$ptipus."</td></tr>
			<tr><td>Data inicial:</td><td>".$pdatai."</td></tr>
			<tr><td>Data final:</td><td>".$pdataf."</td></tr>
			<tr><td>Període:</td><td>".$pperiode."</td></tr>
			<tr><td>Dia recollida:</td><td>".$pdiare."</td></tr>
			<tr><td>Dia tall:</td><td>".$pdiat."</td></tr>
			<tr><td>Hora tall:</td><td>".$phorat."</td></tr>
			<tr><td>Actiu:</td><td>".$pactiu."</td></tr>
			</table>";
		}
}
else {
?>

<div class="contenidor_fac" style="border: 1px solid #66FF66; width: 500px; margin-bottom: 20px;" >
<form action="createproces.php" method="post" name="one" id="one">
<table width="60%" align="center" valign="middle" cellpadding="5" cellspacing="5">
<tr>
<td width="20%" class="cos_majus">Nom</td>
<td width="80%">
<input type="text" name="nom" id="nom" size="15" maxlength="30" value="<?php echo $pnom; ?>">
</td></tr>

<tr>
<td class="cos_majus">Grup</td>
<td>
<SELECT name="grup" id="grup" size="1" maxlength="30" >
<option value="">Elegeix un grup</option>

<?php

	$select2= "SELECT nom FROM grups WHERE actiu='actiu' ORDER BY nom";
	$result2 = mysqli_query($conn,$select2) or die("Query2 failed. " . mysqli_error($conn));
	while (list($sgrup)=mysqli_fetch_row($result2))
{
if ($pgrup==$sgrup)
	{
	echo '<option value="'.$sgrup.'" selected>'.$sgrup.'</option>';
	}
else
	{
	echo '<option value="'.$sgrup.'">'.$sgrup.'</option>';
	}
}
?>
</SELECT>
</td></tr>

<tr>
<td class="cos_majus">Tipus</td>
<td>
<SELECT name="tipus" id="tipus" size="1" maxlength="20" onChange="return change_tipus();">

<?php
if ($ptipus=='continu') {$check1="selected"; $check2="";}
if ($ptipus=='període concret') {$check1=""; $check2="selected";}
?>

 <OPTION value="">Elegeix-ne un</OPTION>
 <OPTION value="continu" <?php echo $check1; ?>>Continu</OPTION>
 <OPTION value="període concret" <?php echo $check2; ?>>Període concret</OPTION>
</SELECT></td>
</tr>
</form>
<form name="two" id="two" action="createproces.php" method="post">
<?php
if ($ptipus!="")
{
?>

<input type="hidden" name="nom" id="nom" value="<?php echo $pnom; ?>" >
<input type="hidden" name="grup" id="grup" value="<?php echo $pgrup; ?>" >
<input type="hidden" name="tipus" id="tipus" value="<?php echo $ptipus; ?>" >

<?php
if ($ptipus=="continu")
{
?>
<tr>
<td class="cos_majus">Període</td>
<td>
<SELECT name="periode" id="periode" size="1" maxlength="20">
 <OPTION value=""></OPTION>
 <OPTION value="setmanal">Setmanal</OPTION>
</SELECT></td>
</tr>
<tr>
<td class="cos_majus">Dia recollida</td>
<td>
<SELECT name="diare" id="diare" size="1" maxlength="20">
 <OPTION value="dilluns">dilluns</OPTION>
 <OPTION value="dimarts">dimarts</OPTION>
 <OPTION value="dimecres">dimecres</OPTION>
 <OPTION value="dijous">dijous</OPTION>
 <OPTION value="divendres">divendres</OPTION>
 <OPTION value="dissabte">dissabte</OPTION>
 <OPTION value="diumenge">diumenge</OPTION>
</SELECT></td>
</tr>
<tr>
<td class="cos_majus">Dia tall</td>
<td>
<SELECT name="diat" id="diat" size="1" maxlength="20">
 <OPTION value="dilluns">dilluns</OPTION>
 <OPTION value="dimarts">dimarts</OPTION>
 <OPTION value="dimecres">dimecres</OPTION>
 <OPTION value="dijous">dijous</OPTION>
 <OPTION value="divendres">divendres</OPTION>
 <OPTION value="dissabte">dissabte</OPTION>
 <OPTION value="diumenge">diumenge</OPTION>
</SELECT></td>
</tr>
<tr>
<td class="cos_majus">Hora tall (HHMM)</td>
<td>
<input type="text" name="horat" id="horat" size="4" maxlength="4" value="0000">
</td>
</tr>
<tr>
<td class="cos_majus">Activitat</td>
<td>
<SELECT name="actiu" id="actiu" size="1" maxlength="12">
 <OPTION value="actiu" selected>Actiu
 <OPTION value="aturat">Aturat
</SELECT></td>
</tr>
<?php
}
if ($ptipus=="període concret")
{
?>
<input type="hidden" name="diare" id="diare" value="no" size="10" maxlength="30">
<tr>
<td class="cos_majus">Data inicial</td>
<td>

<input type="text" name="datai" id="f_date_a" size="8" maxlength="10" readonly />
<button type="reset" name="budi" id="f_trigger_a">...</button></p>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "f_date_a",     // id of the input field
        ifFormat       :    "%d/%m/%Y",     // format of the input field (even if hidden, this format will be honored)
        button         :    "f_trigger_a",  // trigger for the calendar (button ID)
        singleClick    :    true
    });
</script>

</td>
</tr>

<tr>
<td class="cos_majus">Data final</td>
<td>
<input type="text" name="dataf" id="f_date_b" size="8" maxlength="10" readonly />
<button type="reset" name="budf" id="f_trigger_b">...</button></p>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "f_date_b",     // id of the input field
        ifFormat       :    "%d/%m/%Y",     // format of the input field (even if hidden, this format will be honored)
        button         :    "f_trigger_b",  // trigger for the calendar (button ID)
        singleClick    :    true
    });
</script>
</td>
</tr>

<tr>
<td class="cos_majus">Activitat</td>
<td>
<SELECT name="actiu" id="actiu" size="1" maxlength="12">
 <OPTION value="actiu" selected>Actiu
 <OPTION value="aturat">Aturat
</SELECT></td>
</tr>
<?php
}
}
?>
</table>
<p class="linia_button2" style="background: #66FF66; text-align: center; vertical-align: middle;">
<input class="button2" type="submit" id="guardar" name="guardar" value="GUARDAR" onClick="return validar_formulari();">
<input class="button2" type="button" value="SORTIR" onClick="javascript:window.location = 'editprocessos.php';">
</p>
</form>

<p class="cos2" style="clear: both; text-align: center;">
Per canviar les dades clica el botó GUARDAR
</p>

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
