<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];

$num_alb = $_GET['id'];
$elim = $_GET['id2'];

$pprov=$_POST['prov'];
$pdata=$_POST['data'];
$pdata2 = explode ("/",$pdata);
$pdataintro = $pdata2[2]."-".$pdata2[1]."-".$pdata2[0];
$ptotsi=$_POST['totsi'];
$ptotiva=$_POST['totiva'];
$ptot=$_POST['tot'];
$pnotes=$_POST['notes'];
$num=$_POST['num'];
$numprevi=$_POST['numprevi'];
$nomp=$_POST['nomp'];
$unitat=$_POST['uni'];

include 'config/configuracio.php';

?>

<html lang="es">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>editar albarà ::: la coope</title>
		 <!-- calendar stylesheet -->
  <link rel="stylesheet" type="text/css" media="all" href="calendar/calendar-win2k-1.css" title="win2k-1" />

  <!-- main calendar program -->
  <script type="text/javascript" src="calendar/calendar.js"></script>

  <!-- language for the calendar -->
  <script type="text/javascript" src="calendar/lang/calendar-cat.js"></script>

  <!-- the following script defines the Calendar.setup helper function, which makes
       adding a calendar a matter of 1 or 2 lines of code. -->
  <script type="text/javascript" src="calendar/calendar-setup.js"></script>

<script language="javascript" type="text/javascript">

function checktotal() {

var jtotsi = document.getElementById('totsi');
var jtotsiv = parseFloat("jtotsi.value");
var jtotiva = document.getElementById('totiva');
var jtotivav = parseFloat("jtotiva.value");
var jtot = document.getElementById('tot');

if (jtotsi.value!="")
{
jtot.value=parseFloat(jtotsi.value)
}
if (jtotsi.value!="" && jtotiva.value!="")
{
jtot.value=parseFloat(jtotsi.value) + parseFloat(jtotiva.value);
jtot.readOnly=true;
}
else
{
jtot.disabled=false;
}
}

function validar_Form() {

var jprov = document.getElementById('prov');
var jdata = document.getElementById('f_date_a');
var jtotsi = document.getElementById('totsi');
var jtotiva = document.getElementById('totiva');
var jtot = document.getElementById('tot');
var x = new Array();
var nom = new Array();

if (jprov.value== "") {
     alert ('Has d\'elegir una proveïdora');
	  jprov.focus();
     return false;
}

if (jdata.value== "") {
     alert ('Has d\'introduir la data de l\'albarà');
	  jdata.focus();
     return false;
}

if (isNaN(jtotsi.value)) {
     alert ('El total sense iva ha de ser un valor numèric. El punt decimal no pot ser una coma.');
	  jtotsi.focus();
     return false;
}

if (isNaN(jtotiva.value)) {
     alert ('El total d\'iva ha de ser un valor numèric. El punt decimal no pot ser una coma.');
	  jtotiva.focus();
     return false;
}

if (isNaN(jtot.value)) {
     alert ('El total amb iva ha de ser un valor numèric. El punt decimal no pot ser una coma.');
	  jtot.focus();
     return false;
}

for (i = 0; i < this.document.noualbara.elements['num[]'].length; i++){
x[i] = document.getElementById("num"+i).value;
nom[i]= document.getElementById("nom"+i).value;

if (isNaN(x[i])) {
alert ('A ' + nom[i] + ': només s/accepten numeros i el punt decimal');
document.getElementById("num"+i).focus();
return false;
break;
}

}

return true;

}

</script>
</head>

<?php
$st3=">>><a href='edit_alb.php?id=".$num_alb."'>editar albarà ".$num_alb."</a>";
if ($elim!="")
{
$st3="";
}

?>


<body>

<FORM action="edit_alb.php?id=<?php echo $num_alb; ?>"  method="POST" name="noualbara" id="noualbara"
	onSubmit="return validar_Form();">
<div class="pagina" style="margin-top: 10px;">
<div class="contenidor_1" style="border: 1px solid #990000">

<p class='path'>
><a href='admint.php'>administració</a>
>><a class='Estilo2' href='albarans.php'>llistat d'albarans</a>
<?php echo $st3; ?>
</P>
<p class="h1" style="background: #990000; text-align: left; padding-left: 20px;">
Editar albarà <?php echo $num_alb; ?>
<span style="display: inline; float: right; text-align: center; vertical-align: middle;
padding: 2px 50px 2px 0px;">
<input class="button2" type="button" value="ELIMINAR"
onClick="var answer = confirm ('Estas segur de borrar aquest albarà <?php echo $num_alb; ?>!!');
				if (answer)
					{window.location = 'edit_alb.php?id=<?php echo $num_alb; ?>&id2=elim';}">
</span>
</p>

<?php
if ($elim!="")
{
	$delete= "DELETE FROM albara WHERE numero='$num_alb'";
	mysqli_query($conn,$delete) or die('Invalid query deleting table1: ' . mysqli_error($conn));

	$select="SELECT producte, quantitat FROM albara_linia WHERE numero='$num_alb'";
	$result = mysqli_query($conn,$select);
	if (!$result) {die("Ha fallat result:" .mysqli_error($conn));}

	while (list($snomprod,$squant)=mysqli_fetch_row($result))
	{
		$query5= "SELECT pr.nom,pr.categoria,cat.estoc
			FROM productes AS pr, categoria AS cat
			WHERE pr.nom='$snomprod' AND pr.categoria=cat.tipus";
		$result5=mysqli_query($conn,$query5);
		if (!$result5) { die("Query5 to show fields from table failed");}
		list($v1,$v2,$sestoc)=mysqli_fetch_row($result5);
		if ($sestoc=='si')
		{
			$query8 = "UPDATE productes SET estoc=estoc-'$squant' WHERE nom='$snomprod'";
			mysqli_query($conn,$query8) or die('Error, update query8 failed');
		}
	}

	$delete2= "DELETE FROM albara_linia WHERE numero='$num_alb'";
	mysqli_query($conn,$delete2) or die('Invalid query deleting table2: ' . mysqli_error($conn));

	die ('<p class="comment">
			L\'albarà '.$num_alb.' ha estat eliminat</p>');
}


$files = count($num);
if ($files!="")
{
	$query="UPDATE albara SET data='$pdataintro',totsi='$ptotsi',totiva='$ptotiva',tot='$ptot',notes='$pnotes'
	WHERE numero='$num_alb'";
	mysqli_query($conn,$query) or die('Error, la inserció de dades no ha estat possible');

	echo '<table style="padding: 10px;" width="70%" align="center" cellspading="5" cellspacing="5" >
				<tr>
				<td align="left" class="cos_majus">Albarà numero:'.$num_alb.'</td>
				<td align="left" class="cos_majus">Proveïdora:'.$pprov.'</td>
				<td align="left" class="cos_majus">Data:'.$pdata.'</td>
				</tr>
				</table>';
	echo '<table style="padding: 10px;" width="70%" align="center" cellspading="5" cellspacing="5" >
				<tr><td align="left" class="cos">';
	$query6= "SELECT nom	FROM productes WHERE proveidora='$pprov'";
	$result6=mysqli_query($conn,$query6);
	if (!$result6) { die("Query6 to show fields from table failed");}
	$contador=mysqli_num_rows($result6);

	for ($i=0; $i<$contador; $i++)
	{
		if ($numprevi[$i]!="")
		   {
				$delete2= "DELETE FROM albara_linia WHERE numero='$num_alb' AND producte='$nomp[$i]'";
				mysqli_query($conn,$delete2) or die('Invalid query deleting table2: ' . mysqli_error($conn));
		   	$query5= "SELECT pr.nom,pr.categoria,cat.estoc
				FROM productes AS pr, categoria AS cat
				WHERE pr.nom='$nomp[$i]' AND pr.categoria=cat.tipus";
				$result5=mysqli_query($conn,$query5);
				if (!$result5) { die("Query5 to show fields from table failed");}
				list($v1,$v2,$sestoc)=mysqli_fetch_row($result5);
				if ($sestoc=='si')
				{
		  		 	$query6 = "UPDATE productes SET estoc=estoc-'$numprevi[$i]' WHERE nom='$nomp[$i]'";
					mysqli_query($conn,$query6) or die('Error, update query6 failed');
				}
		   }
		if ($num[$i] != "")
		{
			$query4 = "INSERT INTO albara_linia(numero, producte, quantitat)
			VALUES ('$num_alb', '$nomp[$i]', '$num[$i]')";
			mysqli_query($conn,$query4) or die('Error, insert4 query failed');

			$query5= "SELECT pr.nom,pr.categoria,cat.estoc
				FROM productes AS pr, categoria AS cat
				WHERE pr.nom='$nomp[$i]' AND pr.categoria=cat.tipus";
			$result5=mysqli_query($conn,$query5);
			if (!$result5) { die("Query5 to show fields from table failed");}
			list($v1,$v2,$sestoc)=mysqli_fetch_row($result5);
			if ($sestoc=='si')
				{
					$query7 = "UPDATE productes SET estoc=estoc+'$num[$i]' WHERE nom='$nomp[$i]'";
					mysqli_query($conn,$query7) or die('Error, update query7 failed');
				}

			print ('<p align="left">- '.$num[$i].' '.$unitat[$i].' de '.$nomp[$i].'.</p>');
		}
	}
	echo '</td></tr></table>
			<table style="padding: 10px;" width="70%" align="center" cellspading="5" cellspacing="5" >
				<tr>
				<td align="left" class="cos_majus">Total sense iva:'.$ptotsi.'</td>
				<td align="left" class="cos_majus">Total iva:'.$ptotiva.'</td>
				<td align="left" class="cos_majus">Total:'.$ptot.'</td>
				</tr>
				<tr>
				<td align="left" class="cos">Comentaris:'.$pnotes.'</td>
				<td></td><td></td>
				</tr>
				</table>';
}
else
{
?>

<div class="contenidor_fac" style="border: 1px solid #990000; margin-bottom:20px;">

<table style="padding: 10px;" width="80%" align="center" cellspading="5" cellspacing="5" >

<?php

	$sel2 = "SELECT proveidora,data,totsi,totiva,tot,notes
	FROM albara WHERE numero='$num_alb'";
	$result2 = mysqli_query($conn,$sel2);
	if (!$result2) {die('Invalid query2: ' . mysqli_error($conn));}
	list($snomprov,$sdataintro,$stotsi,$stotiva,$stot,$snotes) = mysqli_fetch_row($result2);
	$sdata2 = explode ("-",$sdataintro);
	$sdata = $sdata2[2].'/'.$sdata2[1].'/'.$sdata2[0];

?>
<tr>
<td align="left" width="50%" class="cos_majus">Numero: <input align="right" value="<?php echo $num_alb; ?>" name="num_alb" id="num_alb" type="TEXT"
	maxlength="10" size="5" readOnly>
</td>
<td align="left" width="50%" class="cos_majus">Proveïdora:<input align="right" value="<?php echo $snomprov; ?>" name="prov" id="prov" type="TEXT"
	maxlength="10" size="5" readOnly>
</td></tr>
<tr>
<td align="left" class="cos_majus">Data:
	<input type="text" value="<?php echo $sdata; ?>" name="data" id="f_date_a" size="8" maxlength="10" readonly />
	<button type="text" name="budi" id="f_trigger_a">...</button>
	<script type="text/javascript">
    Calendar.setup({
        inputField     :    "f_date_a",     // id of the input field
        ifFormat       :    "%d/%m/%Y",     // format of the input field (even if hidden, this format will be honored)
        button         :    "f_trigger_a",  // trigger for the calendar (button ID)
        singleClick    :    true
    });
	</script>
	<input type=hidden name="num_alb" value="<?php echo $num_alb; ?>">
	</td><td></td>
	</tr>

	<tr><td class="cos_majus">Total sense iva: <input align="right" value="<?php echo $stotsi; ?>" name="totsi" id="totsi" type="TEXT"
	maxlength="10" size="5" onChange="return checktotal()">
	</td>
	<td class="cos_majus">Total iva: <input align="right" value="<?php echo $stotiva; ?>" name="totiva" id="totiva" type="TEXT"
	maxlength="10" size="5" onChange="return checktotal()">
	</td></tr>

	<tr><td class="cos_majus">Total: <input align="right" value="<?php echo $stot; ?>" name="tot" id="tot" type="TEXT" maxlength="10" size="5">
	</td><td></td></tr>

	<tr><td class="cos">Comentaris: <input align="right" value="<?php echo $snotes; ?>" name="notes" id="notes" type="TEXT" maxlength="255" size="35">
	</td><td></td></tr>

</table>

	<div id="contenidor_1" style="height: 210px; overflow: scroll; overflow-x: hidden;">
<table style="padding: 10px;" width="80%" align="center" cellspading="5" cellspacing="5" >
<?php
	$sel3 = "SELECT nom,unitat
	FROM productes
	WHERE proveidora='$snomprov'
	ORDER BY nom";
	$result3 = mysqli_query($conn,$sel3);
	if (!$result3) {die('Invalid query3: ' . mysqli_error($conn));}

	print ('<tr class="cos_majus"><td width="40%" align="left">Producte</td><td width="20%" align="center">Quantitat</td>
	<td width="20%" align="left">Unitat</td></tr>');
	$id=0; $contador=0;
	while(list($ssnomprod,$ssunitat) = mysqli_fetch_row($result3))
	{
		$sel4 = "SELECT quantitat
		FROM albara_linia
		WHERE producte='$ssnomprod' AND numero='$num_alb'";
		$result4 = mysqli_query($conn,$sel4);
		if (!$result4) {die('Invalid query4: ' . mysqli_error($conn));}
		list($quant)=mysqli_fetch_row($result4);
		$qdec="";
		if ($quant!="")
		{
			/// per veure la quantitat amb els decimals imprescindibles /////
			$r2=round($quant, 2)*1000;
			$r1=round($quant, 1)*1000;
			$r0=round($quant)*1000;
			$rb=$quant*1000;
			if ($rb==$r0)
			{
				$nd=0;
			}
			else
			{
				if ($rb==$r1) {$nd=1;}
				else
				{
					if ($rb==$r2) {$nd=2;}
					else {$nd=3;}
				}
			}
			$qdec=round($quant, $nd);
			//////////////////////////////////////
		}

		print('<tr class="cos"><td>'.$ssnomprod.': <input type=hidden name="nomp[]" id="nom'.$id.'" value="'.$ssnomprod.'"></td>
		<td><input align="center" name="num[]" id="num'.$id.'" value="'.$qdec.'" type="TEXT" maxlength="8" size="5"></td>
		    <input type=hidden name="numprevi[]" id="numprevi'.$id.'" value="'.$quant.'">
		<td>'.$ssunitat.' <input type=hidden name="uni[]" id="uni'.$id.'" value="'.$ssunitat.'">
		</td></tr>');
   	$contador++;
		$id++;
	}
print ('</table></div>');
?>

<p class="linia_button2" style="background: #990000; text-align: center; vertical-align: middle;
	height:20px; padding:4px 0px;">
<input class="button2" name="crear" id="crear" type="submit" value="GUARDAR">
</p>

</div>

<p class="cos2" style="clear: both; text-align: center; padding: 0px 100px;">
Per editar un albarà fes i canvis i clica GUARDAR al final de la fitxa.
Per elimar l'albarà clica el botó ELIMINAR.</p>
</div></div>
</body>
</html>


<?php
}
include 'config/disconect.php';
}
else {
header("Location: index.php");
}
?>
