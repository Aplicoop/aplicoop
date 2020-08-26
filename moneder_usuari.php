<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];

$pactiu=$_POST['actiu'];
$pgrup=$_POST['grup'];
$ptipus=$_POST['tipus'];

include 'config/configuracio.php';

?>

<html lang="es">
	<head>
		<?php include 'head.php'; ?>
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>moneder famílies ::: la coope</title>

</head>
<body>
<?php include 'menu.php'; ?>
<div class="pagina" style="margin-top: 10px;">

<div class="contenidor_1" style="border: 1px solid grey;">

<p class='path'>
><a href='admint.php'>administració</a>
>><a href='moneder_usuari.php'>moneder famílies</a>
</p>
<p class="h1" style="background: grey; text-align: left; padding-left: 20px;">
Moneder famílies
</p>

<table width="80%" align="center" style="padding: 10px 0px;">
<form action="moneder_usuari.php" method="post" name="fam" id="fam">
<tr>
<td width="33%" align="center" class="form">Actiu/Baixa</td>
<td width="33%" align="center" class="form">Grup</td>
<td width="33%" align="center" class="form">Comissió</td>
</tr>

<tr>
<td align="center">
<SELECT name="actiu" id="actiu" size="1" maxlength="5" onChange="this.form.submit()">

<?php

if ($pactiu=='actiu') {$checked1='selected';$checked2="";}
if ($pactiu=='baixa') {$checked2='selected';$checked1="";}


?>
	<option value="">Tots</option>
	<option value="actiu" <?php echo $checked1; ?>>Actiu</option>
	<option value="baixa" <?php echo $checked2; ?>>Baixa</option>

</select>
</td>

<td align="center">
<SELECT name="grup" id="grup" size="1" maxlength="30" onChange="this.form.submit()">
<option value="">Tots</option>

<?php
$select3= "SELECT nom FROM grups ORDER BY nom";
$query3=mysqli_query($conn,$select3);
if (!$query3) {die('Invalid query3: ' . mysqli_error($conn));}

while (list($sgrup)=mysqli_fetch_row($query3))
{
	if ($pgrup==$sgrup){echo '<option value="'.$sgrup.'" selected>'.$sgrup.'</option>';}
	else {echo '<option value="'.$sgrup.'">'.$sgrup.'</option>';}
}

?>

</select>
</td>
<td align="center">
<SELECT name="tipus" id="tipus" size="1" maxlength="30" onChange="this.form.submit()">
<option value="">Tots</option>
<?php
if ($ptipus=='user') {$checked3='selected';$checked4="";$checked5="";$checked6="";$checked7="";$checked8="";}
elseif ($ptipus=='admin') {$checked3='';$checked4="selected";$checked5="";$checked6="";$checked7="";$checked8="";}
elseif ($ptipus=='eco') {$checked3='';$checked4="";$checked5="selected";$checked6="";$checked7="";$checked8="";}
elseif ($ptipus=='prov') {$checked3='';$checked4="";$checked5="";$checked6="selected";$checked7="";$checked8="";}
elseif ($ptipus=='cist') {$checked3='';$checked4="";$checked5="";$checked6="";$checked7="selected";$checked8="";}
elseif ($ptipus=='super') {$checked3='';$checked4="";$checked5="";$checked6="";$checked7="";$checked8="selected";}
echo'
<option value="user" '.$checked3.'>user</option>
<option value="admin" '.$checked4.'>admin</option>
<option value="eco" '.$checked5.'>eco</option>
<option value="prov" '.$checked6.'>prov</option>
<option value="cist" '.$checked7.'>cist</option>
<option value="super" '.$checked8.'>super</option>';
?>

</select>
</td>
</form>
</tr></table>

<div class="contenidor_fac" style="border: 1px solid grey; max-height: 350px; overflow: scroll; overflow-x: hidden;
margin-bottom: 20px; padding-bottom: 20px;">

<?php

	if ($pactiu!="" AND $pgrup=="" AND $ptipus=="") {$where="AND tipus2='".$pactiu."'"; $title="Famílies en ".$pactiu;}
	elseif ($pactiu!="" AND $pgrup!="" AND $ptipus=="") {$where="AND tipus2='".$pactiu."' AND dia='".$pgrup."'"; $title="Famílies en ".$pactiu." del grup ".$pgrup;}
	elseif ($pactiu!="" AND $pgrup!="" AND $ptipus!="") {$where="AND tipus2='".$pactiu."' AND dia='".$pgrup."' AND tipus='".$ptipus."'"; $title="Famílies en ".$pactiu." del grup ".$pgrup." i de la comissió ".$ptipus;}
	elseif ($pactiu!="" AND $pgrup=="" AND $ptipus!="") {$where="AND tipus2='".$pactiu."' AND tipus='".$ptipus."'"; $title="Famílies en ".$pactiu." de la comissió ".$ptipus;}
	elseif ($pactiu=="" AND $pgrup=="" AND $ptipus!="") {$where="AND tipus='".$ptipus."'"; $title="Famílies de la comissió ".$ptipus;}
	elseif ($pactiu=="" AND $pgrup!="" AND $ptipus!="") {$where="AND dia='".$pgrup."' AND tipus='".$ptipus."'"; $title="Famílies del grup ".$pgrup." i de la comissió ".$ptipus;}
	elseif ($pactiu=="" AND $pgrup!="" AND $ptipus=="") {$where="AND dia='".$pgrup."'"; $title="Famílies del grup ".$pgrup;}
	elseif ($pactiu=="" AND $pgrup=="" AND $ptipus=="") {$where=""; $title="Totes les famílies";}

	print ('<p class="h1"
		style="background: grey; font-size:14px; text-align: left;
		height: 20px; padding-left: 20px;">
		'.$title.'
		</p>');

	print('<table width="80%" align="center" cellspading="5" cellspacing="5" >
		<tr class="cos_majus">
		<td align="center" width="30%">nom</td>
		<td align="center" width="40%">components</td>
		<td align="center" width="35%">moneder</td></tr>');

	$taula = "SELECT usuaris.nom, usuaris.components, SUM(moneder.valor) as total
	FROM usuaris, moneder
	WHERE moneder.familia=usuaris.nom ".$where."
	GROUP BY moneder.familia
	ORDER BY total ASC";
	$result = mysqli_query($conn,$taula);
	if (!$result) {die('Invalid query: ' . mysqli_error($conn));}

	while (list($nom,$components,$mone)=mysqli_fetch_row($result))
	{
		echo "<tr class='cos'>
		<td align='center'><a href='comptes.php?id4=".$nom."'>".$nom."</a></td>
		<td align='center'>".$components."</td>
		<td align='center'>".$mone."</td>
		</tr>";
	}

	echo "</table></div></div>";

?>

<p class="cos2" style="clear: both; text-align: center;">
Per veure la fitxa completa d'una família clicka sobre el seu nom
</p>
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
