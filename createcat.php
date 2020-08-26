<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' )
{

	$user = $_SESSION['user'];

	$ptipus=$_POST['tipus'];
	$supertipus=strtoupper($ptipus);
	$estoc=$_POST['estoc'];

?>

<html lang="es">
	<head>
		<?php include 'head.php'; ?>
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>crear nova categoria ::: la coope</title>
	</head>

<body>
	<?php include 'menu.php'; ?>
<div class="pagina" style="margin-top: 10px;">
<div class="contenidor_1" style="border: 1px solid #990000;">
<p class='path'>
><a href='admint.php'>administració</a>
 >><a href='categories.php'>editar, crear, eliminar categories</a>
 >>><a href='createcat.php'>crear nova categoria</a>
</p>

<p class="h1" style="background: #990000; text-align: left; padding-left: 20px;">
Crear nova categoria</p>

<?php

	include 'config/configuracio.php';

	if ($ptipus!="")
	{
		$select= "SELECT tipus FROM categoria
		WHERE tipus='".$ptipus."' ";
		$result = mysqli_query($conn,$select) or die("Query failed. " . mysqli_error($conn));

   	if (mysqli_num_rows($result) == 1)
   	{
   		die
   		("<p class='comment'>No es pot crear de nou la categoria ".$ptipus." perquè ja existeix.</p>");
		}
		else
		{
			$query2 = "INSERT INTO categoria
			VALUES ('".$ptipus."', 'activat', '".$estoc."') ";
			mysqli_query($conn,$query2) or die('Error, insert query2 failed');

			echo
			"<p class='comment'>La categoria ".$supertipus." s'ha introduït correctament a la base de dades.</p>";
		}
	}
	else
	{

?>
<div class="contenidor_fac" style="border: 1px solid #990000; width: 500px; margin-bottom: 20px;" >
<form action="createcat.php" method="post" name="noucat" id="noucat" >
<table width="80%" align="center" valign="middle" cellpadding="5" cellspacing="5">
<tr>
<td class="cos_majus">Categoria:</td>
<td><input align="right" name="tipus" id="tipus" type="TEXT" maxlength="50" size="20">
</td></tr>
<tr>
<td class="cos_majus">Estoc:</td>
<td>
si<input type="radio" name="estoc" value="si" id="estoc">
no<input type="radio" name="estoc" value="no" id="estoc">
</td></tr>
</table>
</div>

<p class="linia_button2" style="background: #990000; padding:4px 0px;
height: 20px; text-align: center; vertical-align: middle;">
<input class="button2" name="acceptar" type="submit" id="acceptar" value="Acceptar">
</p>
</div>
</div>

</body>
</html>


<?php
	}
}
else {
header("Location: index.php");
}
?>
