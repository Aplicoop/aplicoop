<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];
$superuser=strtoupper($_SESSION['user']);

$nom = $_GET['id'];
$supernom=strtoupper($nom);
$actiu=$_POST['actiu'];
$notes=$_POST['notes'];

include ('config/configuracio.php');

?>

<html lang="es">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>editar dades grups ::: la coope</title>
	</head>

<body>
<div class="pagina" style="margin-top: 10px;">
<div class="contenidor_1" style="border: 1px solid red;">
<p class='path'>
><a href='admint.php'>administració</a>
 >><a href='editgrups.php'>crear, editar i eliminar grups</a>
 >>><a href='editgrups2.php?id=<?php echo $nom; ?>'>editar grup <?php echo $nom; ?></a>
</p>
<p class="h1" style="background: red; text-align: left; padding-left: 20px;">
Editar grup <?php echo $supernom; ?>
</p>


<?php
if ($actiu!="")
	{
	$query2 = "UPDATE grups
	SET actiu='".$actiu."', notes='".$notes."'
	WHERE nom='".$nom."' ";

	mysqli_query($conn,$query2) or die('Error, insert query2 failed');

	echo "<p class='comment'>Els canvis en el grup ".$supernom." s'han guardat correctament
	</p>";
	}
else {
?>

<div class="contenidor_fac" style="border: 1px solid red; width: 600px; margin-bottom: 20px;" >

<form action="editgrups2.php?id=<?php echo $nom; ?>" method="post" name="frmeditdadesp" id="frmeditdadesp">

<?php

$select= "SELECT actiu,notes FROM grups WHERE nom='$nom'";

$query=mysqli_query($conn,$select);

if (!$query) {
    die('Invalid query: ' . mysqli_error($conn));
    }

list($preactiu,$prenotes)=mysqli_fetch_row($query);

	$sel9="";$sel10="";
	if ($preactiu=="actiu") {$sel9="selected";}
	else {$sel10="selected";}
?>

<table width="80%" align="center" valign="middle" cellpadding="5" cellspacing="5">
<tr>
<td class="cos_majus">Nom</td>
<td>
<input type="text" name="nom" id="nom" value="<?php echo $nom; ?>" size="10" maxlength="30" readonly>
</td>
</tr>

<tr>
<td class="cos_majus">Activitat</td>
<td>
<SELECT name="actiu" id="actiu" size="1" maxlength="12">
 <OPTION value="actiu" <?php echo $sel9; ?> >Actiu
 <OPTION value="no actiu" <?php echo $sel10; ?> >No actiu
</SELECT></td>
</tr>
<tr>
<td class="cos_majus">Comentaris</td>
<td>
<textarea name="notes" cols="65" rows="4" id="notes"><?php echo $prenotes; ?></textarea></td>
</tr>
</table>
</div>

<p class="linia_button2" style="background: red; text-align: center; vertical-align: middle;">
<input class="button2" type="submit" value="GUARDAR">
<input class="button2" type="button" value="SORTIR" onClick="javascript:window.location = 'escriptori2.php';">
</p>

<p class="cos2" style="clear: both; text-align: center;">Per canviar les dades clica el botó GUARDAR
</p>
<?php
}
?>

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
