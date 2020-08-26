<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];

$data=$_GET['id'];
$proces=$_GET['id2'];
$grup=$_GET['id3'];


$bd_data=date("Y-m-d", strtotime ($data));



?>

<html lang="es">
	<head>
		<?php include 'head.php'; ?>
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>total factura x proveïdors amb o sense estoc ::: la coope</title>
		<style type="text/css">
	a#color:link, a#color:visited {color:white; border: 1px solid #9cff00;}
	a#color:hover {color:black; border: 1px solid #9cff00;   -moz-border-radius: 10%;}
   a#color:active {color:white; border: 1px solid #9cff00;  -moz-border-radius: 10%;}
		</style>
</head>

<body>
<?php include 'menu.php'; ?>
<div class="pagina" style="margin-top: 10px;">

<div class="contenidor_1" style="border: 1px solid green;">
<p class='path'>
><a href='admint.php'>administració</a>
>><a href='grups_comandes.php'>grups de comandes i cistelles</a>
>>><a href='totalfactura.php?id=<?php echo $data."&id2=".$proces."&id3=".$grup; ?>'>factura total <?php echo $title." ".$proces."-".$grup."-".$data; ?></a>
</p>
<p class="h1" style="background: green; text-align: left; padding-left: 20px;">Factura total <?php echo $title." ".$proces."-".$grup."-".$data; ?></p>
<div class="cat" style="width: 750px; margin-left: auto; margin-right: auto;">


<?php

include 'config/configuracio.php';
$color=array("#F0C900","#00b2ff","orange","#b20000","#14e500","red","#8524ba");
$cc=0;
$select3="SELECT nom FROM proveidores";
$resultat3=mysqli_query($conn,$select3);
if (!$resultat3) {die("Query to show fields from table select3 failed");}
$numrowsat3=mysqli_num_rows($resultat3);
while (list($prove)=mysqli_fetch_row($resultat3))
{
	$select2="SELECT cl.numero, c.data, cl.ref, pr.nom, pr.proveidora, pr.categoria, cat.estoc
	FROM comanda_linia AS cl, comanda AS c, productes AS pr, categoria AS cat
	WHERE c.numero=cl.numero AND cl.ref=pr.ref AND c.proces='$proces' AND pr.categoria=cat.tipus
	AND c.grup='$grup' AND c.data='$bd_data' AND pr.proveidora='$prove'
	ORDER BY pr.proveidora, pr.nom";
	$resultat2=mysqli_query($conn,$select2);
	if (!$resultat2) {die("Query to show fields from table select2 failed");}
	$numrowsat2=mysqli_num_rows($resultat2);

	if ($numrowsat2!=0)
	{
		print ('<a href="#'.$prove.'" id="color" style="background: '.$color[$cc].';
				margin-bottom: 5px; margin-right: 3px; white-space: -moz-pre-wrap; word-wrap: break-word;">
				<span>'.$prove.'</span></a>');
				$cc++;
				if ($cc==7){$cc=0;}
	}
	mysqli_free_result($resultat2);
}


echo'</div>
<div class="contenidor_fac" style="border: 1px solid green; height: 350px; overflow: scroll;
overflow-x: hidden;">';

$cc=0;
$select3="SELECT nom FROM proveidores";
$resultat3=mysqli_query($conn,$select3);
if (!$resultat3) {die("Query to show fields from table select3 failed");}
$numrowsat3=mysqli_num_rows($resultat3);
while (list($prove)=mysqli_fetch_row($resultat3))
{
	$select2="SELECT cl.numero, c.data, cl.ref, pr.nom, pr.proveidora, pr.categoria, cat.estoc
	FROM comanda_linia AS cl, comanda AS c, productes AS pr, categoria AS cat
	WHERE c.numero=cl.numero AND cl.ref=pr.ref AND c.proces='$proces' AND pr.categoria=cat.tipus
	AND c.grup='$grup' AND c.data='$bd_data' AND pr.proveidora='$prove'
	ORDER BY pr.proveidora, pr.nom";
	$resultat2=mysqli_query($conn,$select2);
	if (!$resultat2) {die("Query to show fields from table select2 failed");}
	$numrowsat2=mysqli_num_rows($resultat2);

	if ($numrowsat2!=0)
	{
		echo '<a name="'.$prove.'"></a>
		<p class="h1"
		style="background: '.$color[$cc].'; font-size:14px; text-align: left;
		height: 20px; padding-left: 20px; clear: left;">
		'.$prove.'
		</p>';
		$cc++;
		if ($cc==7){$cc=0;}
		echo '<table width="100%" align="center" valign="middle" cellpadding="5" cellspacing="5">';

		$query= "SELECT numero,usuari FROM comanda
		WHERE proces='$proces' AND grup='$grup' AND data='$bd_data'
		ORDER BY usuari";
		$result=mysqli_query($conn,$query);
		if (!$result) { die("Query to show fields from table failed");}
		$numrows1=mysqli_num_rows($result);

		// printing table headers
		echo "<tr class='cos_majus'><td width='25%'>Producte</td>";
		echo "<td align='center' width='20%'>Total demanat</td>
				<td align='center' width='20%'>Total servit</td>
				<td align='center' width='10%'>Preu Unitari</td>
				<td align='center' width='10%'>tipus d'IVA</td>
				<td align='center' width='15%'>Total a pagar (*)</td>";
		echo "</tr>";

		$taula = "SELECT cl.ref, pr.nom, pr.unitat AS uni, SUM(cl.quantitat) AS sum,
		SUM(cl.cistella) as sum2, cl.preu/(1+pr.marge) AS preusm, SUM(cl.cistella*cl.preu/(1+pr.marge)) AS tpag,
		cl.iva
		FROM comanda AS c, comanda_linia AS cl, productes AS pr
		WHERE c.numero=cl.numero AND cl.ref=pr.ref AND c.proces='$proces'
		AND c.grup='$grup' AND c.data='$bd_data' AND pr.proveidora='$prove'
		GROUP BY cl.ref
		ORDER BY pr.proveidora, pr.nom";

		$result = mysqli_query($conn,$taula);
		if (!$result) {die('Invalid query: ' . mysqli_error($conn));}
		while (list($ref,$nomprod,$uni,$sum,$sumc,$preu,$tpag,$iva)=mysqli_fetch_row($result))
		{
		$preu=sprintf("%01.2f", $preu);
		$ttpag=$ttpag+$tpag;
		$tpag=sprintf("%01.2f", $tpag);
		$tiva=$tpag-($tpag/(1+$iva));
		$ttpag=sprintf("%01.2f", $ttpag);
		$v_iva=$iva*100;
		$ttiva=$ttiva+$tiva;
		$ttiva=sprintf("%01.2f", $ttiva);
?>

<tr class='cos'><td><?php echo $nomprod; ?></td>
<td align='center'><?php echo $sum; ?> <?php echo $uni; ?></td>
<td align='center'><?php echo $sumc; ?> <?php echo $uni; ?></td>
<td align='center'> <?php echo $preu; ?>&#8364;</td>
<td align='center'> <?php echo $v_iva; ?>%</td>
<td align='right'><?php echo $tpag; ?>&#8364;</td>
</tr>

<?php
		}
		echo "<tr><td>&nbsp</td><td>&nbsp</td><td>&nbsp</td><td>&nbsp</td>
		<td style='cos16' style='font-weight: bold;'>Total</td><td align='right'>".$ttpag."&#8364;</td></tr>
		<tr><td>&nbsp</td><td>&nbsp</td><td>&nbsp</td><td>&nbsp</td>
		<td style='cos16' style='font-weight: bold;'>Total iva suportat (aprox)</td><td align='right'>".$ttiva."&#8364;</td></tr>
		</table>";
	}
	$total_final=$total_final+$ttpag;
	$ttpag=0; $ttiva=0;
}
?>
</div>

<p class="linia_button2" style="background: green; text-align: center; vertical-align: middle;">
Total: <?php echo $total_final; ?>€
</p>

<p class="cos2" style="clear: both; text-align: center; padding: 0px 100px;">
(*) Els càlculs son aproximats i depenen del preu actual.A mesura que passi el temps es faran més inexactes</p>

</div>
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
