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

	//vol evitar que algú es col·li escrivint codi directament a l'adreça html //
	if ($_SESSION['codi_cistella'] != 'in')
	{
		$gvis=0;
	}
	////

	list($mdiatdx, $mestdx, $anytdx ) = explode("-", $gdata);
	$gbd_data=$anytdx."-".$mestdx."-".$mdiatdx;

/// Hi ha dos possbile retorns POST ///
/// Un de cistella2_fam.php///
	$post_cistella=$_POST["num"];
	$post_ref=$_POST["ref"];

/// Un altre de cistella_mes.php ///
	$paddfam=$_POST['nouf'];
	$pprov=$_POST['prov'];
	$pprod=$_POST['prod'];
	$pref=$_POST['ref'];
	$pnum=$_POST['num'];

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
		$button='<button class="button button--save button--animated" name="Gcodi" type="button" value="PAS SEGÜENT" onClick="confirma()">Siguiente paso <i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i></button>';
		$sty="";
		$intronovafam='<button class="button button--save button--animated""
		onClick="javascript:window.location = \'cistella_mes.php?id3='.$gdata.'&id5='.$gvis.'&id6='.$gproces.'&id7='.$ggrup.'&id8=1\'">Nueva familia <i class="fa fa-plus" aria-hidden="true"></i></button>';
	}


	include 'config/configuracio.php';



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
				VALUES ('$pnum', '$pref', '1', '0')";
			mysqli_query($conn,$query2) or die('Error, insert query2 failed');
		}
		else
		{
			$query3 = "INSERT INTO comanda ( `usuari` , `proces`, `grup`, `sessionid` , `data` )
				VALUES ('$paddfam', '$gproces', '$ggrup', '$sessionid', '$gbd_data')";
			mysqli_query($conn,$query3) or die('Error, insert query3 failed');
			$inumcmda=mysqli_insert_id();

			$query4 = "INSERT INTO comanda_linia (numero, ref, quantitat, cistella)
				VALUES ('$inumcmda', '$pref', '1', '0')";
			mysqli_query($conn,$query4) or die('Error, insert query4 failed');
		}
	}

	////////////////////////////////////////////
	// si hi ha dades d'un producte GET id i POST llavors les guarda //
	// implica retorn de cistella2_fam.php /////////
	// amb informació de les cistelles per guardar //
	/////////////////////////////////////////////////

	if ($gfam!="")
	{
		$files = count($post_cistella);

		////Busquem el numero de comanda////
		$query= "SELECT c.numero FROM comanda AS c
		WHERE c.data='$gbd_data' AND c.proces='$gproces' AND c.grup='$ggrup' AND c.usuari='$gfam'";
		$result=mysqli_query($conn,$query);
		if (!$result) { die("Query to show fields from table failed");}
		list($pnumcmda)=mysqli_fetch_row($result);

		$count=0;
		for ($i=0; $i<$files; $i++)
		{

			if ($post_cistella[$i]==""){$post_cistella[$i]=0;}

			///Busquem el nomprod i nomprov de $post_ref///
			$query0= "SELECT nom, proveidora FROM productes WHERE ref='$post_ref[$i]'";
			$result0=mysqli_query($conn,$query0);
			if (!$result0) { die("Query0 to show fields from table failed");}
			list($post_prod,$post_prov)=mysqli_fetch_row($result0);

			///Busquem si existeixen quantitats de cistella introduides anteriorment///
			$select= "SELECT cistella FROM comanda_linia
			WHERE numero='$pnumcmda' AND ref='$post_ref[$i]'";
			$result = mysqli_query($conn,$select);
			if (!$result) { die('Invalid query select: ' . mysqli_error($conn));}
			list($c)= mysqli_fetch_row($result);

			///Busquem si és un producte d'estoc///
			$select9= "SELECT pr.categoria, cat.estoc
			FROM productes AS pr, categoria AS cat
			WHERE pr.categoria=cat.tipus AND pr.ref='$post_ref[$i]'";
			$result9 = mysqli_query($conn,$select9);
			if (!$result9) { die('Invalid query select9: ' . mysqli_error($conn));}
			list($scat,$sestoc)= mysqli_fetch_row($result9);

			///Si és d'estoc i te introduida una quantitat a cistella ///
			/// llavors la recupera del camp estoc de la taula productes ///
			if ($c!="" AND $sestoc=='si')
			{
				$query6= "UPDATE productes
				SET estoc=estoc+'$c'
				WHERE ref='$post_ref[$i]'";
				mysqli_query($conn,$query6) or die('Error, insert query6 failed');
			}


			/// Calculem el pvp sense iva. Preu*marge ///
			$select10= "SELECT preusi, iva, marge, descompte FROM productes WHERE ref='$post_ref[$i]'";
			$result10 = mysqli_query($conn,$select10);
			if (!$result10) { die('Invalid query select10: ' . mysqli_error($conn));}
			list($spreusi,$siva,$smarge,$sdescompte)= mysqli_fetch_row($result10);

			$pvp=$spreusi*(1+$smarge);
			$pvp=sprintf("%01.2f", $pvp);
			$pvp2=$pvp*(1-$sdescompte);
			$pvp2=sprintf("%01.2f", $pvp2);

			///Introduim les noves quantitats de cistella///
			$query= "UPDATE comanda_linia
			SET cistella='$post_cistella[$i]', preu='$pvp', iva='$siva', descompte='$sdescompte'
			WHERE numero='$pnumcmda' AND ref='$post_ref[$i]'";
			mysqli_query($conn,$query) or die('Error, insert query failed');

			if ($sestoc=='si')
			{
				$query7= "UPDATE productes
				SET estoc=estoc-'$post_cistella[$i]'
				WHERE ref='$post_ref[$i]'";
				mysqli_query($conn,$query7) or die('Error, insert query7 failed');
			}
		}

		$nota="<p class='alert alert--info'>Se han introducido correctamente los datos de la cesta de la familia ".$gfam." correspondiente al pedido número ".$pnumcmda."</p>";
	}

///Inici html///
?>

<html lang="es">
	<head>
		<?php include 'head.php'; ?>
		<title>aplicoop - editar pedido - lista de familias</title>
</head>

<script language="javascript" type="text/javascript">
			function confirma()
			{
				var answer;
				var answer = confirm("Estas cerrando el apartado de HACER CESTAS \nSe mandará una notificación electrónica a todas las familias y se generara un código de edición\nAceptar: ir a facturacion \nCancelar: continuar haciendo cestas");
				if (answer)
				{
	  				window.location = 'cistella_check1.php?id=<?php echo $gproces."&id2=".$ggrup."&id3=".$gbd_data; ?>';
				}
			}
</script>

<body>
<?php include 'menu.php'; ?>
<div class="page">
    <div class="container">

	<div class="u-cf">
		<h1 class="pull-left">Pedido <?php echo $gdata." - ".$gproces." - ".$ggrup; ?></h1>
		<div class="pull-right u-mt-1 u-mb-1">
			<button class="button button--white button--animated" type="button" onClick="javascript:window.location = 'cistelles.php?id2=<?php echo $gdata.'&id3='.$gproces.'&id4='.$ggrup.'&id5='.$gvis; ?>'">Cestas por producto</button>
  		</div>
	</div>


		<?php echo $nota; ?>



<div class="box">

	<div class="u-cf">
		<h2 class="pull-left">Lista familias</h1>
		<div class="pull-right u-mt-1 u-mb-1">
			<?php echo $intronovafam; ?>
  		</div>
	</div>

<h2 >


<div class="table-responsive">
<table  class="table table-striped table-bordered">

<tr class='cos_majus'><td class='u-text-semibold u-text-center'>Família</td>
<td class='u-text-semibold  u-text-center'>Nímero pedido</td>
<td class='u-text-semibold  u-text-center'>Unidades demandadas</td>
<td class='u-text-semibold  u-text-center'>Unidades servidas</td>
</tr>


<?php

	$taula3 = "SELECT c.numero, c.usuari, SUM(cl.quantitat), SUM(cl.cistella)
	FROM comanda_linia AS cl, comanda AS c
	WHERE cl.numero=c.numero AND c.data='$gbd_data'	AND c.proces='$gproces' AND c.grup='$ggrup'
	GROUP BY c.numero	ORDER BY c.usuari";

	$result3 = mysqli_query($conn,$taula3);
	if (!$result3) {die('Invalid query3: ' . mysqli_error($conn));}

	$i=0;
	while(list($numero,$familia,$demanat,$servit)=mysqli_fetch_row($result3))
	{
		$color="";
		if ($servit!=0)
			{
			$color="style='color: green;'";
			}
		$estil="";
		if ($servit!=0 AND $servit<>$demanat)
			{
			$color="style='color: red;'";
  			}
?>


<tr class="cos">
<td align="center">
<a id='color2' class="link link--visitable" href="cistella2_fam.php?id=<?php echo $familia.'&id2='.$gdata.'&id3='.$gproces.'&id4='.$ggrup.'&id5='.$gvis; ?>" >
<?php echo $familia; ?></a></td>
<td align="center"><?php echo $numero; ?></td>
<td align="center" <?php echo $color; ?>><?php echo $demanat; ?></td>
<td align="center" <?php echo $color; ?>><?php echo $servit; ?></td>
</tr>

<?php
		$i++;
	}
?>
</table>
</div>
<p class="u-text-center">

<?php
	if ($gvis=='1')
	{
		echo $button;
	}
?>


</p>
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
