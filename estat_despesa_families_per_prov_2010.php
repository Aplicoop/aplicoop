
<html lang="es">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>total factura x proveïdors amb o sense estoc ::: la coope</title>
		<style type="text/css">
	a#color:link, a#color:visited {color:white; border: 1px solid #9cff00;}
	a#color:hover {color:black; border: 1px solid #9cff00;   -moz-border-radius: 10%;}
   a#color:active {color:white; border: 1px solid #9cff00;  -moz-border-radius: 10%;}
		</style>
</head>

<body>

<div class="pagina" style="margin-top: 10px;">

<div class="contenidor_1" style="border: 1px solid black;">
<p class='path'>
><a href='admint.php'>administració</a>
</p>
<p class="h1" style="background: black; text-align: left; padding-left: 20px;">
Despesa famílies per proveïdors 2010
</p>
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
	$select2="SELECT cl.numero, c.data, cl.ref, pr.nom, pr.proveidora
	FROM comanda_linia AS cl, comanda AS c, productes AS pr
	WHERE c.numero=cl.numero AND pr.ref=cl.ref
	AND c.data>='2010-01-01' AND c.data<'2011-01-01' AND pr.proveidora='$prove'
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
<div class="contenidor_fac" style="border: 1px solid black; height: 350px; overflow: scroll;
overflow-x: hidden;">';

$cc=0;
$select3="SELECT nom FROM proveidores";
$resultat3=mysqli_query($conn,$select3);
if (!$resultat3) {die("Query to show fields from table select3 failed");}
$numrowsat3=mysqli_num_rows($resultat3);
while (list($prove)=mysqli_fetch_row($resultat3))
{
	$select2="SELECT cl.numero, c.data, cl.ref, pr.nom, pr.proveidora
	FROM comanda_linia AS cl, comanda AS c, productes AS pr
	WHERE c.numero=cl.numero AND pr.ref=cl.ref
	AND c.data>='2010-01-01' AND c.data<'2011-01-01' AND pr.proveidora='$prove'
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
		WHERE data>='2010-01-01' AND data<'2011-01-01'
		ORDER BY usuari";
		$result=mysqli_query($conn,$query);
		if (!$result) { die("Query to show fields from table failed");}
		$numrows1=mysqli_num_rows($result);

		// printing table headers
		echo "<tr class='cos_majus'><td>Producte</td>";
		echo "<td align='center'>Total demanat</td>
				<td align='center'>Total servit</td>
				<td align='center'>Preu</td>
				<td align='center'>Total a pagar</td>";
		echo "</tr>";

		$taula = "SELECT cl.ref, pr.nom, SUM(cl.quantitat) AS sum,
		SUM(cl.cistella) as sum2, cl.preu, SUM(cl.cistella*cl.preu*(1+cl.iva)*(1-cl.descompte)) as tpag
		FROM comanda AS c, comanda_linia AS cl, productes AS pr
		WHERE c.numero=cl.numero AND cl.ref=pr.ref AND c.data>='2010-01-01'
		AND c.data<'2011-01-01' AND pr.proveidora='$prove'
		GROUP BY cl.producte
		ORDER BY cl.proveidora, cl.producte";

		$result = mysqli_query($conn,$taula);
		if (!$result) {die('Invalid query: ' . mysqli_error($conn));}
		while (list($ref,$nomprod,$sum,$sumc,$preu,$tpag)=mysqli_fetch_row($result))
		{
		$ttpag=$ttpag+$tpag;
		$tpag=sprintf("%01.2f", $tpag);
		$ttpag=sprintf("%01.2f", $ttpag);
?>

<tr class='cos'><td><?php echo $ref." - ".$nomprod; ?></td>
<td align='center'><?php echo $sum; ?> <?php echo $uni; ?></td>
<td align='center'><?php echo $sumc; ?> <?php echo $uni; ?></td>
<td align='right'> <?php echo $preu; ?>&#8364;</td>
<td align='right'><?php echo $tpag; ?>&#8364;</td>
</tr>

<?php
		}
		echo "<tr><td>&nbsp</td><td>&nbsp</td><td>&nbsp</td>
		<td style='cos16' style='font-weight: bold;'>Total</td><td align='right'>".$ttpag."&#8364;</td></tr></table>";
	}
	$total_final=$total_final+$ttpag;
	$ttpag=0;
}
?>
</div>

<p class="linia_button2" style="background: green; text-align: center; vertical-align: middle;">
Total: <?php echo $total_final; ?>
</p>

</div>
</div>
</body>
</html>


<?php
include 'config/disconect.php';
?>
