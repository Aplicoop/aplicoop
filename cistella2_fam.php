<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];

	$sessionid=$_SESSION['sessionid'];

	$gfam=$_GET['id'];
	$gdata=$_GET['id2'];
	$gproces=$_GET['id3'];
	$ggrup=$_GET['id4'];
	$gvis=$_GET['id5'];
	$gnumfact=$_GET['id6'];

	list($mdiatdx, $mestdx, $anytdx ) = explode("-", $gdata);
	$gbd_data=$anytdx."-".$mestdx."-".$mdiatdx;

	///Prové d'afegir producte cistella_mes.php///
	$pprov=$_POST['prov'];
	$pprod=$_POST['prod'];
	$pref=$_POST['ref'];

	// si hi ha un numero comanda de factura //
	if ($gnumfact!=""){$aw='AND c.numero='.$gnumfact; $id6='&id6='.$gnumfact; $id8='&id8='.$gnumfact;}
	else {$aw=""; $id6=""; $id8="";}
	//

	if ($_SESSION['codi_cistella'] != 'in')
	{
		$gvis=0;
	}

	if ($gvis==0)
	{
		$readonly="readonly";
		$button="";
		$sty="padding:4px 0px; height: 20px;";
		$intronouprod="";
	}
	else
	{
		$readonly="";
		$button='<input class="button2" name="acceptar" type="submit" value="Acceptar">';
		$sty="";
		$intronouprod='<input class="button2" style="width:180px;" type="button" value="INTRODUIR NOU PRODUCTE"
		onClick="javascript:window.location = \'cistella_mes.php?id3='.$gdata.'&id5='
		.$gvis.'&id6='.$gproces.'&id7='.$ggrup.'&id9='.$gfam.'\'">';
	}

	include 'config/configuracio.php';

	////Busquem el numero de comanda////
	$query= "SELECT c.numero FROM comanda AS c
	WHERE c.data='$gbd_data' AND c.proces='$gproces' AND c.grup='$ggrup' AND c.usuari='$gfam'";
	$result=mysqli_query($conn,$query);
	if (!$result) { die("Query to show fields from table failed");}
	list($numcmda)=mysqli_fetch_row($result);


	//////////////////////////////////////////////////
	//// Si existeix la variable POST procedent de cistella_mes.php ///
	/// vol dir que volem afegir un producte a la comanda de la família///
	//////////////////////////////////////////////////

	if ($pref!="" OR $pprod!="" OR $pprov!="")
	{
		echo "numcmda:".$numcmda;
		$query2 = "INSERT INTO comanda_linia (numero, ref, quantitat, cistella)
			VALUES ('$numcmda', '$pref', '1', '0')";
			mysqli_query($conn,$query2) or die('Error, insert query2 failed');
	}

///Inici html///
?>

<html lang="es">
	<head>
		<?php include 'head.php'; ?>
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>fer la cistella - productes ::: la coope</title>
</head>

<script language="javascript" type="text/javascript">

function validate_form(){

var x = new Array();
var nom = new Array();
var answered;
var i;
var error = "Les següents families tenen la cistella buida:\n\n";
var a = "";

/// la funció lenght no detecta arrays d'un item. Així pots solucionar-se el problema ///
var len= this.document.frmComanda.elements['num[]'].length;
if(len == undefined) len = 1;

for (i = 0; i < len; i++){
x[i] = document.getElementById("num"+i).value;
nom[i]= document.getElementById("nom"+i).value;

if (isNaN(x[i])) {
alert ('A ' + nom[i] + ': només s/accepten numeros i el punt decimal');
document.getElementById("num"+i).focus();
return false;
break;
}

if (x[i]>=100 || x[i]<0) {
alert ('A ' + nom[i] + ': el numero ha de ser superior que 0 i inferior a 100');
document.getElementById("num"+i).focus();
return false;
break;
}

if (x[i]=="" || x[i]==0) {
a += nom[i] + '\n';
}

}

if (a != "") {
a += '\nCancelar: Tornar a la pàgina per omplir les cistelles buides \nAcceptar: Continuar endavant';
var answered = confirm(error + a);
	if (answered){
		return true;
	}
	else{
      return false;
	}
}
return true;
}

</script>


<body>
<?php include 'menu.php'; ?>
<div class="pagina" style="margin-top: 10px;">
<div class="contenidor_1" style="border: 1px solid green;">
<p class='path'>
><a href='admint.php'>administració</a>
>><a href='grups_comandes.php'>grups de comandes i cistelles</a>
>>><a href='cistelles2.php?id2=<?php echo $gdata."&id3=".$gproces."&id4=".$ggrup."&id5=".$gvis; ?>'>cistella <?php echo $gdata." - ".$gproces." - ".$ggrup; ?></a>
>>>><a href='cistella2_fam.php?id=<?php echo $gfam."&id2=".$gdata."&id3=".$gproces."&id4=".$ggrup."&id5=".$gvis; ?>'>
Família <?php echo $gfam; ?></a>
</p>
<p class="h1" style="background: green; text-align: left; padding-left: 20px;">cistella <?php echo $gdata." - ".$gproces." - ".$ggrup; ?></p>
<div class="contenidor_fac" style="border: 1px solid green; max-height: 350px; overflow: scroll; overflow-x:hidden;">
<p class="h1" style="background: green; text-align: left; padding-left: 20px;"> Família <?php echo $gfam; ?>
<span style="display: inline; float: right; text-align: center; vertical-align: middle; padding: 2px 50px 2px 0px;">
<?php echo $intronouprod; ?>
</span>
</p>

<form action="cistelles2.php?id=<?php echo $gfam.'&id2='.$gdata.'&id3='.$gproces.'&id4='.$ggrup.'&id5='.$gvis; ?>"
 method="post" name="frmComanda" id="frmComanda"   onSubmit="return validate_form();" >

<table width="85%" align="center" valign="left" cellpadding="5" cellspacing="5">

<tr>
<td class="cos16" align="center" style="border: 1px solid green;">
Comanda numero: <?php echo $numcmda; ?>
</td></tr></table>

<table width="80%" align="center" valign="middle" cellpadding="5" cellspacing="5">

<tr class='cos_majus' width='50%'><td>Producte</td>
<td align="center" width='20%'>preu-marge-IVA-dte</td>
<td align="center" width='15%'>Comanda</td>
<td align="center" width='15%'>Cistella</td>
</tr>

<?php

	$taula3 = "SELECT cl.ref, pr.nom, pr.proveidora, pr.categoria, pr.preusi, pr.marge, pr.iva, pr.descompte, cl.quantitat, cl.cistella
	FROM comanda_linia AS cl, comanda AS c, productes AS pr
	WHERE cl.numero=c.numero AND pr.ref=cl.ref AND c.usuari='$gfam' AND c.data='$gbd_data' AND c.proces='$gproces'
	AND c.grup='$ggrup'
	ORDER BY pr.categoria, pr.nom";

	$result3 = mysqli_query($conn,$taula3);
	if (!$result3) {die('Invalid query3: ' . mysqli_error($conn));}

	$i=0;
	while(list($ref,$nomprod,$nomprov,$cat,$preu,$marge,$iva,$desc,$quantitat,$cistella)=mysqli_fetch_row($result3))
	{
		$v_marge=$marge*100;
		$v_marge=$v_marge."%";
		$v_iva=$iva*100;
		$v_iva=$v_iva."%";
		$v_desc=$desc*100;
		$v_desc=$v_desc."%";
?>

<tr class="cos">
<td><?php echo $nomprod; ?> (<?php echo $nomprov; ?>)</td>
<td align="center">
<a href="canvi_massiu_productes.php?id=<?php echo $ref.'&id3='.$gdata.
			'&id4='.$cat.'&id5='.$gvis.'&id6='.$gproces.'&id7='.$ggrup.'&id9='.$gfam; ?>">
<?php echo $preu."€/u-".$v_marge."-".$v_iva."-".$v_desc; ?>
</a></td>
<td align="center"><?php echo $quantitat; ?></td>
<td align="center">
		<input align="right" name="num[]" id="num<?php echo $i; ?>" type="TEXT" maxlength="7" size="5"
		value="<?php echo $cistella; ?>" <?php echo $readonly; ?> >
    <input type="hidden" name="ref[]" id="ref<?php echo $i; ?>" value="<?php echo $ref; ?>">
</td>
</tr>

<?php
		$i++;
	}
?>
</table>
</div>


<p class="linia_button2" style="<?php echo $sty; ?> background: green; text-align: center; vertical-align: middle;">

<?php
	if ($gvis=='1')
	{
		echo $button;
	}
?>

<input class="button2" type="button" value="Sortir"
	onClick="location.href='cistelles2.php?id2=<?php echo $gdata."&id3=".$gproces."&id4=".$ggrup."&id5=".$gvis; ?>'">
</p>

</form>

<p class="cos2" style="clear: both; text-align: center; padding: 0px 100px;">
	Per guardar els canvis és indispensable apretar el botó ACCEPTAR.
	Per canviar els camps del producte (preu, iva, marge, descompte) clica sobre ells</p>

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
