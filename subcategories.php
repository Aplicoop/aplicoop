<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' )
{

	$user = $_SESSION['user'];

	$pcat=$_POST['cat'];
	$psubcat=$_POST['subcat'];

	$gcat = $_GET['id'];
	$gsubcat = $_GET['id2'];
	$gactiu = $_GET['id3'];
	$elim = $_GET['id4'];

	include 'config/configuracio.php';
?>

<html lang="es">
<head>
	<?php include 'head.php'; ?>
	<link rel="stylesheet" type="text/css" href="coope.css" />
	<title>editar subcategories ::: la coope</title>
</head>

<body>
<?php include 'menu.php'; ?>
<div class="pagina" style="margin-top: 10px;">
<div class="contenidor_1" style="border: 1px solid #990000;">
<p class='path'>
><a href='admint.php'>administració</a>
>><a href='categories.php'>crear, editar i eliminar categories</a>
>>><a href='subcategories.php?id=<?php echo $gcat; ?>'>subcategories a <?php echo $gcat; ?></a>
</p>


<p class="h1" style="background: #990000; text-align: left; padding-left: 20px;">
Subcategories a <?php echo $gcat; ?>
</p>


<?php

	if ($psubcat != "")
	{
		$select= "SELECT subcategoria FROM subcategoria
		WHERE subcategoria='".$psubcat."' AND categoria='".$pcat."' ";
		$result = mysqli_query($conn,$select) or die("Query failed. " . mysqli_error($conn));

   	if (mysqli_num_rows($result) == 1)
   	{
   		die
   		("<p class='comment'>La subcategoria ".$psubcat." amb la categoria ".$pcat." ja existeix.</p>");
		}
		else
		{
			$query2 = "INSERT INTO subcategoria
			VALUES ('".$psubcat."', '".$pcat."', 'activada') ";
			mysqli_query($conn,$query2) or die("Error, insert query2 failed");
		}
	}

	if ($gactiu != "")
		{
			$query3 = "UPDATE subcategoria
			SET actiu= '".$gactiu."'
			WHERE subcategoria='".$gsubcat."' AND categoria='".$gcat."' ";
			mysqli_query($conn,$query3) or die('Error, insert query3 failed');
		}

	if ($elim != "")
		{
			$query4 = "DELETE FROM subcategoria
			WHERE subcategoria='".$gsubcat."' AND categoria='".$gcat."' ";
			mysqli_query($conn,$query4) or die('Error, insert query4 failed');

			echo "<p class='comment'>La subcategoria ".$gsubcat." s'ha eliminat corectament</p>";
		}

?>

<div class="contenidor_fac" style="border: 1px solid #990000; width: 600px; margin-bottom: 20px;" >

<table width="80%" align="center" valign="middle" cellpadding="5" cellspacing="5">
<tr class="cos_majus">
<td width="20%" align="center">categoria</td>
<td width="20%" align="center">subcategoria</td>
<td width="20%" align="center">activa</td>
<td width="20%" align="center">eliminar</td>
</tr>

<?php

		$taula = "SELECT subcategoria,actiu FROM subcategoria
		WHERE categoria='".$gcat."'
		ORDER BY actiu, subcategoria";
		$result = mysqli_query($conn,$taula);
		if (!$result) {die('Invalid query: ' . mysqli_error($conn));}

		$k=0;
		while (list($subcat,$actiu)=mysqli_fetch_row($result))
		{
			$checked1="";
			$checked2="";
			if ($actiu=="activada") {$checked1="checked";}
			else {$checked2="checked";}
?>

<tr class="cos">
<td align="center"><?php echo $gcat; ?></td>
<td align="center"><?php echo $subcat; ?></td>
<td align="center">
si<input type="radio" name="actiu<?php echo $k; ?>" value="si" <?php echo $checked1; ?> id="actiu<?php echo $k; ?>" onClick="javascript:window.location = 'subcategories.php?id=<?php echo $gcat; ?>&id2=<?php echo $subcat; ?>&id3=activada';">
no<input type="radio" name="actiu<?php echo $k; ?>" value="no" id="actiu<?php echo $k; ?>" <?php echo $checked2; ?> onClick="javascript:window.location = 'subcategories.php?id=<?php echo $gcat; ?>&id2=<?php echo $subcat; ?>&id3=desactivada';">
</td>
<td align='center'><a href='subcategories.php?id=<?php echo $gcat; ?>&id2=<?php echo $subcat; ?>&id4=borrar'
onClick='if(confirm("Estas segur que vols eliminar la subcategoria <?php echo $subcat; ?>?") == false){return false;}'>X</a>
</td>

<?php
			$k++;
		}
?>

</tr></table></div>

<p class="cos2" style="clear: both; text-align: center; padding: 0px 100px;">
Per activar o desactivar clica el botó desitjat.
Per borrar clica sobre la X de la columna ELIMINAR
</p>


<p class="h1" style="background: #990000; text-align: left; padding-left: 20px;">
Crear nova subcategoria a <?php echo $gcat; ?>
</p>

<div class="contenidor_fac" style="border: 1px solid #990000; width: 600px; margin-bottom: 20px;" >
<form action="subcategories.php?id=<?php echo $gcat; ?>" method="post" name="frmeditdadesp" id="frmeditdadesp">
<table width="80%" align="center" valign="middle" cellpadding="5" cellspacing="5">
<tr class="cos_majus">
<td width="30%" >categoria</td>
<td width="30%" >subcategoria</td>
</tr>
<tr>
<td class="cos">
<input type="text" name="cat" id="cat" value="<?php echo $gcat; ?>" size="15" maxlength="30" readonly></td>
</td>
<td class="cos">
<input type="text" name="subcat" id="subcat" value="" size="15" maxlength="30"></td>
</td>
</tr>
</table>


<p class="linia_button2" style="background: #990000; padding:4px 0px;
height: 20px; text-align: center; vertical-align: middle;">
<input class="button2" type="submit" value="CREAR">
</p>
</div>
<p class="cos2" style="clear: both; text-align: center; padding: 0px 100px;">
Per crear una nova subcategoria omple l espai en blanc i clica CREAR.
</p>
</div>
</div>
</body>
</html>


<?php
		include 'config/disconect.php';
	}

else
{
	header("Location: index.php");
}
?>
