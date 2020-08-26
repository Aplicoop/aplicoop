<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];

	$sessionid=$_SESSION['sessionid'];

	$gprodref=$_GET['id'];
	$gdata=$_GET['id3'];
	$gcat=$_GET['id4'];
	$gvis=$_GET['id5'];
	$gproces=$_GET['id6'];
	$ggrup=$_GET['id7'];
	$gnumfact=$_GET['id8'];

	$paddfam=$_POST['nouf'];
	$pnum=$_POST['num'];

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
		$intronovafam="";
	}
	else
	{
		$readonly="";
		$button='<button class="button button button--save button--animated" name="acceptar" type="submit">Aceptar <i class="fa fa-check" aria-hidden="true"></i></button>';
		$sty="";
		$intronovafam='<button class="button button--animated pull-right" type="button"
		onClick="javascript:window.location = \'cistella_mes.php?id='.$gprodref.'&id3='.$gdata.'&id4='.$gcat.'&id5='
		.$gvis.'&id6='.$gproces.'&id7='.$ggrup.'\'">Añadir nueva familia <i class="fa fa-plus" aria-hidden="true"></i></button>';
	}

	list($mdiatdx, $mestdx, $anytdx ) = explode("-", $gdata);
	$gbd_data=$anytdx."-".$mestdx."-".$mdiatdx;

	include 'config/configuracio.php';

	/// Busquem nomprod i nomprov a partir de prodref ////
	$query0= "SELECT nom, proveidora FROM productes WHERE ref='$gprodref'";
	$result0=mysqli_query($conn,$query0);
	if (!$result0) { die("Query0 to show fields from table failed");}

	list($gnomprod,$gprov)=mysqli_fetch_row($result0);
	///////////

	//////////////////////////////////////////////////
	//// Si existeix la variable POST procedent de cistella_mes.php ///
	/// vol dir que volem afegir una família per aquest producte en aquest procés///
	/// llavors: ///
	/// Si ja existeix comanda de la família en el proces ///
	/// Inserta una nova linia a comanda linia ///
	/// Si no existeix comanda ///
	/// Crea comanda de la família per aquest procés i la línia corresponent al producte ///
	/////////////////////////////////////////////////
	if ($paddfam)
	{
		if($pnum!="")
		{
			$query2 = "INSERT INTO comanda_linia (numero, ref, quantitat, cistella)
				VALUES ('$pnum', '$gprodref', '1', '0')";
			mysqli_query($conn,$query2) or die('Error, insert query2 failed');
		}
		else
		{
			$query3 = "INSERT INTO comanda ( `usuari` , `proces`, `grup`, `sessionid` , `data` )
				VALUES ('$paddfam', '$gproces', '$ggrup', '$sessionid', '$gbd_data')";
			mysqli_query($conn,$query3) or die('Error, insert query3 failed');
			$inumcmda=mysqli_insert_id();

			$query4 = "INSERT INTO comanda_linia (numero, ref, quantitat, cistella)
				VALUES ('$inumcmda', '$gprodref', '1', '0')";
			mysqli_query($conn,$query4) or die('Error, insert query4 failed');
		}
	}

///Inici html///
?>

<html lang="es">
	<head>
		<?php include 'head.php'; ?>
		<title>aplicoop - editar pedido / producto</title>
	</head>

<script language="javascript" type="text/javascript">

function validate_form(){

var x = new Array();
var nom = new Array();
var answered;
var i;
var error = "Las siguientes familias tienen la cesta vacia:\n\n";
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
alert ('A ' + nom[i] + ': el numero ha de ser superior a 0 e inferior a 100');
document.getElementById("num"+i).focus();
return false;
break;
}

if (x[i]=="" || x[i]==0) {
a += nom[i] + '\n';
}

}

if (a != "") {
a += '\nCancelar: Volver a la página para llenar las cestas vacías \nAceptar: Seguir adelante';
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

<div class="page;">
<div class="container">

<h1>Cesta <?php echo $gdata." - ".$gproces." - ".$ggrup; ?></h1>
<div class="box">

<div class="u-cf u-mb-1">
	<h2 class="pull-left"><?php echo $gnomprod."-".$gprov; ?></h2>
	<?php echo $intronovafam; ?>
</div>

<?php
	////Busquem dades sobre el producte en qüestió////
	$query= "SELECT unitat,preusi,iva,marge,descompte
	FROM productes
	WHERE ref='$gprodref'";
	$result=mysqli_query($conn,$query);
	if (!$result) { die("Query to show fields from table failed");}

	list($unitat,$preusi,$iva,$marge,$descompte)=mysqli_fetch_row($result);
	/// el pvp sense iva -pvpsi- és el preu més el marge menys el descompte ///
	/// el pvp amb iva -pvp- el el pvpsi més l'iva ///
	$pvpsi=$preusi*(1+$marge);
	$pvpsi=sprintf("%01.2f", $pvpsi);
	$pvp=$pvpsi*(1+$iva);
	$pvp=sprintf("%01.2f", $pvp);
	/// marge, iva i descompte els visualitzem en % ///
	$vis_marge=$marge*100;
	$vis_iva=$iva*100;
	$vis_descompte=$descompte*100;

?>
<form action="cistelles.php?id=<?php echo $gprodref.'&id2='.$gdata.'&id3='.$gproces.'&id4='.$ggrup.'&id5='.$gvis.$id6; ?>#<?php echo $gcat; ?>"
 method="post" name="frmComanda" id="frmComanda"   onSubmit="return validate_form();" >

<div class="alert alert--info" style="position: relative">
	<div>PVP sin iva: <?php echo $pvpsi; ?> €/<?php echo $unitat; ?>
		<span style="color: grey;">[Precio:<?php echo $preusi; ?>]+[Margen:<?php echo $vis_marge; ?>%]</span>
	</div>
	<div>
		PVP con iva: <?php echo $pvp; ?> €/<?php echo $unitat; ?> <span style="color: grey;">[Iva: <?php echo $vis_iva; ?>%] </span>
	</div>
	<div>
		Descuento: <?php echo $vis_descompte; ?>%
	</div>

	<a class="button button--white button--animated" style="position: absolute; right: .6rem; top: .6rem;" href="canvi_massiu_productes.php?id=<?php echo $gprodref.'&id3='.$gdata.'
&id4='.$gcat.'&id5='.$gvis.'&id6='.$gproces.'&id7='.$ggrup; ?>">Cambio datos</a>
</div>

<table class="table table-striped table-bordered u-mt-2">

<tr >
<td  class='u-text-semibold'>Familia</td>
<td  class='u-text-semibold'>Pedido</td>
<td  class='u-text-semibold'>Cesta</td>
</tr>

<?php

	$taula3 = "SELECT cl.numero, c.usuari, cl.ref, cl.quantitat, cl.cistella
	FROM comanda_linia AS cl, comanda AS c
	WHERE cl.numero=c.numero AND cl.ref='$gprodref' AND c.data='$gbd_data'
	AND c.proces='$gproces' AND c.grup='$ggrup' ".$aw."
	ORDER BY c.usuari";

	$result3 = mysqli_query($conn,$taula3);
	if (!$result3) {die('Invalid query3: ' . mysqli_error($conn));}

	$i=0;
	while(list($numero,$familia,$p,$quantitat,$cistella)=mysqli_fetch_row($result3))
	{

?>


<tr class="cos"><td><?php echo $familia; ?> (<?php echo $numero; ?>)</td>

<td align="center"><?php echo $quantitat; ?></td>

<td align="center">
		<input align="right" name="num[]" id="num<?php echo $i; ?>" type="TEXT" maxlength="7" size="5"
		value="<?php echo $cistella; ?>" <?php echo $readonly; ?> >
    <input type="hidden" name="nom[]" id="nom<?php echo $i; ?>" value="<?php echo $familia; ?>">
    <input type="hidden" name="numcmda[]" id="numcmda<?php echo $i; ?>" value="<?php echo $numero; ?>">

</td>
</tr>

<?php
		$i++;
	}
?>
</table>
<div class="u-text-center u-mt-2 u-mb-1">
<?php
	if ($gvis=='1')
	{
		echo $button;
	}
?>
</div>

</div>

</form>

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
