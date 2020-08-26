<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];

$pprov=$_POST['prov'];
$pcont=$_POST['cont'];
$pdata=$_POST['data'];
$pdata2 = explode ("/",$pdata);
$pdataintro = $pdata2[2]."-".$pdata2[1]."-".$pdata2[0];
$ptotsi=$_POST['totsi'];
$ptotiva=$_POST['totiva'];
$ptot=$_POST['tot'];
$pnotes=$_POST['notes'];
$pdes=$_POST['des'];
$num=$_POST['num'];
$nomp=$_POST['nomp'];
$unitat=$_POST['uni'];

include 'config/configuracio.php';

?>

<html lang="es">
	<head>
		<?php include 'head.php'; ?>
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>crear albarà ::: la coope</title>
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

for (i = 0; i < document.two.elements['num[]'].length; i++){
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

function copydata() {
    document.two.prov.value = document.one.prov.value;
    document.two.data.value = document.one.data.value;
    document.two.totsi.value = document.one.totsi.value;
    document.two.totiva.value = document.one.totiva.value;
    document.two.tot.value = document.one.tot.value;
    document.two.notes.value = document.one.notes.value;
    document.two.des.value = document.one.des.value;
}

function copydata2() {
    document.tres.prov.value = document.one.prov.value;
    document.tres.data.value = document.one.data.value;
    document.tres.totsi.value = document.one.totsi.value;
    document.tres.totiva.value = document.one.totiva.value;
    document.tres.tot.value = document.one.tot.value;
    document.tres.notes.value = document.one.notes.value;
    document.tres.submit();
}

</script>
</head>



<body>
 <?php include 'menu.php'; ?>
<div class="pagina" style="margin-top: 10px;">
<div class="contenidor_1" style="border: 1px solid #990000">

<p class='path'>
><a href='admint.php'>administració</a>
>><a href='albarans.php'>llistat d'albarans</a>
>>><a href='create_alb.php'>crear albarà</a>
</p>
<p class="h1" style="background: #990000; text-align: left; padding-left: 20px;">
Crear albarà</p>

<?php
$files = count($num);
if ($files!="")
{
	$query="INSERT INTO albara (proveidora,data,totsi,totiva,tot,notes)
	VALUES ('$pprov','$pdataintro','$ptotsi','$ptotiva','$ptot','$notes')";
	mysqli_query($conn,$query) or die('Error, la inserció de dades no ha estat possible');
	$num_alb=mysqli_insert_id();

	echo '<table style="padding: 10px;" width="70%" align="center" cellspading="5" cellspacing="5" >
				<tr>
				<td align="left" class="cos_majus">Albarà numero:<span class="cos" align="left" >'.$num_alb.'</span></td>
				<td align="left" class="cos_majus">Proveïdora:<span class="cos" align="left" >'.$pprov.'</span></td>
				<td align="left" class="cos_majus">Data:<span class="cos" align="left" >'.$pdata.'</span></td>
				</tr>
				</table>
				<table style="padding: 10px;" width="70%" align="center" cellspading="5" cellspacing="5" >
				<tr><td>';

	for ($i=0; $i<$files; $i++)
	{
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
			print ('<p class="cos" align="left">- '.$num[$i].' '.$unitat[$i].' de '.$nomp[$i].'.</p>');
		}
	}
	echo '</td></tr></table>
				<table style="padding: 10px;" width="70%" align="center" cellspading="5" cellspacing="5" >
				<tr>
				<td align="left" class="cos_majus">Total sense iva:<span class="cos" align="left" >'.$ptotsi.'</span></td>
				<td align="left" class="cos_majus">Total iva:<span class="cos" align="left" >'.$ptotiva.'</span></td>
				<td align="left" class="cos_majus">Total:<span class="cos" align="left" >'.$ptot.'</span></td>
				</tr>
				<tr><td align="left" class="cos_majus">Comentaris:<span class="cos" align="left" >'.$pnotes.'</span></td>
				<td></td><td></td>
				</tr>
				</table>';
}
else
{
?>
<div class="contenidor_fac" style="border: 1px solid #990000; margin-bottom:20px;">

<FORM action="create_alb.php"  method="POST" name="one" id="one" >
<table style="padding: 10px;" width="100%" align="center" cellspading="5" cellspacing="5" >
<tr>
<td align="left" width="20%" class="cos_majus">Proveïdora:</td>
<td class="cos">
<SELECT name="prov" id="prov" size="1" maxlength="30" onChange="document.one.submit();">
<option value="">elegeix una proveïdora</option>

<?php
$query= "SELECT nom FROM proveidores ORDER BY nom";
$result=mysqli_query($conn,$query);
if (!$result) {die("Query to show fields from table failed");}

while (list($sprov)=mysqli_fetch_row($result))
{
	if ($pprov==$sprov){echo '<option value="'.$sprov.'" selected>'.$sprov.'</option>';}
	else {echo '<option value="'.$sprov.'">'.$sprov.'</option>';}
}
echo '</SELECT></td></tr>';
echo'	<input type="hidden" value="" name="cont" id="cont" >';

if ($pprov!="")
{

?>
	<tr>
	<td class="cos_majus">Data: </td>
	<td class="cos">
	<input type="text" value="<?php echo $pdata; ?>" name="data" id="f_date_a" size="8" maxlength="10" readonly />
	<button type="text" name="budi" id="f_trigger_a">...</button>
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

	<tr><td class="cos_majus">Total sense iva:</td>
	<td class="cos"><input align="right" value="<?php echo $ptotsi; ?>" name="totsi" id="totsi" type="TEXT"
	maxlength="10" size="5" onChange="return checktotal()">
	</td></tr>

	<tr><td class="cos_majus">Total iva:</td>
	<td class="cos"><input align="right" value="<?php echo $ptotiva; ?>" name="totiva" id="totiva" type="TEXT"
	maxlength="10" size="5" onChange="return checktotal()">
	</td></tr>

	<tr><td class="cos_majus">Total</td>
	<td class="cos"><input align="right" value="<?php echo $ptot; ?>" name="tot" id="tot" type="TEXT" maxlength="10" size="5">
	</td></tr>

	<tr><td class="cos_majus">Comentaris:</td>
	<td class="cos"><input align="right" value="<?php echo $pnotes; ?>" name="notes" id="notes" type="TEXT" maxlength="255" size="35">
	</td></tr>	</table>
	</form>

	<FORM action="create_alb.php"  method="POST" name="tres" id="tres"
	onSubmit="return validar_Form();">
	<input type="hidden" value="" name="prov" id="prov" >
	<input type="hidden" value="" name="data" id="data" >
	<input type="hidden" value="" name="totsi" id="totsi" >
	<input type="hidden" value="" name="totiva" id="totiva" >
	<input type="hidden" value="" name="tot" id="tot" >
	<input type="hidden" value="" name="notes" id="notes" >


<?php

	$checked3="checked"; $checked4=""; $prodact="AND actiu='actiu'";
	if ($pdes=='si')
	{
		$checked3=""; $checked4="checked"; $prodact="";
	}

?>
	<p class="cos_majus" style="background: #990000; text-align: left; padding: 0 0 4px 20px;">
	Incloure:
	tots els productes<input type="radio" name="des" value="si" id="des" onClick="copydata2()" <?php echo $checked4; ?> >
	només actius<input type="radio" name="des" value="no" id="des" onClick="copydata2()" <?php echo $checked3; ?> >
	</p>
	</form>

	<div id="contenidor_1" style="height: 210px; overflow: scroll; overflow-x: hidden;">

	<FORM action="create_alb.php"  method="POST" name="two" id="two"
	onSubmit="return validar_Form();">
<input type="hidden" value="" name="prov" id="prov" >
<input type="hidden" value="" name="data" id="data" >
<input type="hidden" value="" name="totsi" id="totsi" >
<input type="hidden" value="" name="totiva" id="totiva" >
<input type="hidden" value="" name="tot" id="tot" >
<input type="hidden" value="" name="notes" id="notes" >
<input type="hidden" value="" name="des" id="des" >
<?php
	$sel3 = "SELECT nom,unitat
	FROM productes
	WHERE proveidora='$pprov' $prodact
	ORDER BY nom";
	$result3 = mysqli_query($conn,$sel3);
	if (!$result3) {die('Invalid query3: ' . mysqli_error($conn));}

	print ('<table style="padding: 10px;" width="80%" align="center" cellspading="5" cellspacing="5" >
	<tr cos="cos_majus"><td width="50%" align="left">Producte</td><td width="25%" align="center">Quantitat</td>
	<td width="25%" align="center">Unitat</td></tr>');
	$id=0; $contador=0;
	while(list($nomprod,$unitat) = mysqli_fetch_row($result3))
	{
		print('<tr class="cos"><td align="left">'.$nomprod.': <input type=hidden name="nomp[]" id="nom'.$id.'" value="'.$nomprod.'"></td>
		<td align="center"><input name="num[]" id="num'.$id.'" type="TEXT" maxlength="8" size="5"></td>
		<td align="center">'.$unitat.' <input type=hidden name="uni[]" id="uni'.$id.'" value="'.$unitat.'">
		</td></tr>');
   	$contador++;
		$id++;
	}
	print ('</table></div>');
	echo '<p class="linia_button2" style="background: #990000; text-align: center; vertical-align: middle;
	height:20px; padding:4px 0px;">
	<input class="button2" name="crear" id="crear" type="submit" value="CREAR" onClick="copydata()">
	</p>
	</form>';
}
	print ('</table>');
?>


</div>

<p class="cos2" style="clear: both; text-align: center; padding: 0px 100px;">
Elegeix una empresa o persona proveïdora, omple la fitxa que t'aparegui i
clica el botó CREAR abaix de tot</p>

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
