	<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' OR $_SESSION['codi_cistella'] != 'in')
{
	$user = $_SESSION['user'];

	$gref=$_GET['id'];
	$gdata=$_GET['id3'];
	$gcat=$_GET['id4'];
	$gvis=$_GET['id5'];
	$gproces=$_GET['id6'];
	$ggrup=$_GET['id7'];
	$gcist2=$_GET['id8'];
	$gfam=$_GET['id9'];

	$paddfam=$_POST['nouf'];
	$pprov=$_POST['prov'];
	$pprod=$_POST['prod'];
	$pref=$_POST['ref'];
	$pnum=$_POST['num'];

	list($mdiatdx, $mestdx, $anytdx ) = explode("-", $gdata);
	$gbd_data=$anytdx."-".$mestdx."-".$mdiatdx;

	include 'config/configuracio.php';

	/// Busquem nomprod i nomprov a partir de gref ////
	$query0= "SELECT nom, proveidora FROM productes WHERE ref='$gref'";
	$result0=mysqli_query($conn,$query0);
	if (!$result0) { die("Query0 to show fields from table failed");}

	list($gnomprod,$gprov)=mysqli_fetch_row($result0);
	///////////

	$nota="";

	////////////////////////////////////////////
	// si hi ha dades GET id ($gref) vol dir que prové de cistella_prod.php //
	// i es vol afegir una família al producte ///////////////////////////////
	///////////////////////////////////////////
	// si no pot procedir de cistella2_fam.php (gfam GET id9 existeix) ///
	// i es vol afegir un producte quan ja tenim la família ///
	// o de cistelles.php o cistelles2.php i es vol afegir un producte //
	// (associat a una família i comanda) a un proces-grup ///////////////////
	/////////////////////////////////////////////////////////////////////////

	$title_array=array("Introducir nueva familia","Añadir nuevo producto");
	$a="id=".$gref;
	$form_action_pre_array=array("cistella_mes.php?","id=$gref",
		"&id3=".$gdata."&id4=".$gcat."&id5=".$gvis."&id6=".$gproces."&id7=".$ggrup."","&id8=1","&id9=$gfam",";");
	if ($gref!="")
	{
		/// Valors si procedim de cistella_prod.php///
		$link_cap=">>>><a href='cistella_prod.php?id=".$gref."&id3=".$gdata."&id4=".$gcat."&id5=".$gvis."&id6=".$gproces."
			&id7=".$ggrup."'>".$gnomprod."</a>";
		$title=$title_array[0];
		$form_action_pre=$form_action_pre_array[0].$form_action_pre_array[1].$form_action_pre_array[2];
		$form_action="cistella_prod.php?id=".$gref."&id3=".$gdata."&id4=".$gcat."&id5=".$gvis."&id6=".$gproces."&id7=".$ggrup;
		$subtit='Elegeix una família i clica ACCEPTAR.';
		$disabled3="disabled";
		if ($paddfam!=""){$disabled3="";}
		$sortir="cistella_prod.php?id=".$gref."&id3=".$gdata."&id4=".$gcat."&id5=".$gvis."&id6=".$gproces."&id7=".$ggrup;
		$idcist2="";
	}
	else
	{
		if($gfam!="")
		{
			///Valors quan provenim de cistella2_fam.php ///
			$procedencia="cistella2_fam.php?id=".$gfam."&id2=".$gdata."&id3=".$gproces."&id4=".$ggrup."&id5=".$gvis;
			$title=$title_array[1];
			$link_cap=">>>><a href='".$procedencia."'>"
			.$gfam."</a>";
			$form_action_pre=$form_action_pre_array[0].$form_action_pre_array[2].$form_action_pre_array[4];
			$form_action=$procedencia;
			$subtit='Elegeix una proveïdora i un producte, en aquest ordre, i clica ACCEPTAR.';
			$disabled2="disabled";
			$disabled3="disabled";
			if ($pprov!="")
			{
				$disabled2="";
				if ($pprod!="")
				{
					$disabled3="";
				}
			}
			$sortir=$procedencia;
			$idcist2="2";
		}
		else
		{
			if($gcist2!="")
			/// Valors quan procedim de cistelles2.php ///
			{
				$idcist2="2";
				$title=$title_array[0];$form_action_pre=$form_action_pre_array[0].$form_action_pre_array[2].$form_action_pre_array[3];
			}
			else
			{
				/// Valors quan procedim de cistelles.php ///
				$idcist2="";
				$title=$title_array[1];$form_action_pre=$form_action_pre_array[0].$form_action_pre_array[2];
			}
			/// Valors per cistelles.php i cistelles2.php///
			$link_cap="";
			$disabled="disabled";
			$disabled2="disabled";
			$disabled3="disabled";
			$form_action="cistelles".$idcist2.".php?id2=".$gdata."&id3=".$gproces."&id4=".$ggrup."&id5=".$gvis;
			$subtit='Elige una familia, un proveedor y un producto y pulsa ACEPTAR.';
			$sortir="cistelles".$idcist2.".php?id2=".$gdata."&id3=".$gproces."&id4=".$ggrup."&id5=".$gvis;
			if ($paddfam!="")
			{
				$disabled="";
				if ($pprov!="")
				{
					$disabled2="";
					if ($pprod!="")
					{
						$disabled3="";
					}
				}
			}
		}
	}
	?>


	<html lang="es">
		<head>
			<?php include 'head.php'; ?>
			<title>aplicoop - editar pedido</title>
		</head>

<body>
<?php include 'menu.php'; ?>
<div class="page">

<div class="container">

<h1><?php echo $title; ?></h1>

<p class="alert alert--info">
<?php echo $subtit; ?>
</p>

<div class="box" >

<form action="<?php echo $form_action_pre; ?>" method="post" name="frmmes" id="frmmes" >

 <div class="form-group row">
 	 <label for="nouf" class="col-sm-3 control-label">Familia</label>
     <div class="col-sm-9">

		<?php
			if ($gfam!="")
			{
				echo $gfam;
			}
			else
			{
				echo'<SELECT class="button2" name="nouf" id="nouf" size="1" maxlength="30" onchange="document.frmmes.submit()">
				<option value="">--</option>';

				// Es pot elegir entre totes les famílies actives del grup //

				$taula7="SELECT u.nom, c.numero, cl.ref FROM usuaris AS u LEFT JOIN comanda AS c
				ON u.nom=c.usuari AND c.data='$gbd_data' AND c.proces='$gproces' AND c.grup='$ggrup'
				LEFT JOIN comanda_linia AS cl ON c.numero=cl.numero AND cl.ref='$gref'
				WHERE u.dia='$ggrup' AND u.tipus2='actiu'
				ORDER BY u.nom";
				$result7 = mysqli_query($conn,$taula7);
				if (!$result7) {die('Invalid query7: ' . mysqli_error($conn));}

				while(list($sfam,$snum,$sref)=mysqli_fetch_row($result7))
				{
					if($sref=="")
					{
						$text="";
						if($snum!="")
						{
							$text=$sfam.'('.$snum.')';
						}
						else
						{
							$text=$sfam;
						}

						if ($paddfam==$sfam)
						{
							echo '<option value="'.$sfam.'" selected>'.$text.'</option>';
							$input_num=$snum;
						}
						else
						{
							echo '<option value="'.$sfam.'">'.$text.'</option>';
						}
					}
				}
				echo '</select>';
			}
		?>
	 </div>
 </div>
 <div class="form-group u-mt-1 row">
 	 <label for="prov" class="col-sm-3 control-label">Proveedor</label>
     <div class="col-sm-9">
		<?php
			if ($gref!="")
			{
				echo $gprov;
			}
			else
			{
				echo '<SELECT name="prov" id="prov" size="1" maxlength="30" '.$disabled.' onChange="document.frmmes.submit();">
					<option value="">--</option>';

				$query= "SELECT nom FROM proveidores ORDER BY nom";
				$result=mysqli_query($conn,$query);
				if (!$result) {die("Query to show fields from table failed");}

				while (list($sprov)=mysqli_fetch_row($result))
				{
					if ($pprov==$sprov){echo '<option value="'.$sprov.'" selected>'.$sprov.'</option>';}
					else {echo '<option value="'.$sprov.'">'.$sprov.'</option>';}
				}
				echo '</SELECT>';
			}
		?>
	 </div>
 </div>
 <div class="form-group u-mt-1 row">
 	 <label for="prod" class="col-sm-3 control-label">Producto</label>
     <div class="col-sm-9">
		<?php
			if ($gref!="")
			{
				echo $gnomprod;
			}
			else
			{
				echo '<SELECT name="prod" id="prod" size="1" maxlength="30" '.$disabled2.' onchange="document.frmmes.submit()">
					<option value="">--</option>';

				// Es pot elegir entre tots els productes excepte els que ja hi son ///

				$query2 = "SELECT pr.ref, pr.nom FROM productes AS pr	WHERE pr.proveidora='$pprov' ORDER BY pr.nom";
				$result2=mysqli_query($conn,$query2);
				if (!$result2) {die("Query2 to show fields from table failed:" . mysqli_error($conn));}

				while (list($sref,$sprod)=mysqli_fetch_row($result2))
				{
					$query3 = "SELECT cl.ref, pr.nom FROM comanda_linia AS cl, comanda AS c, productes AS pr
					WHERE c.numero=cl.numero AND cl.ref=pr.ref AND cl.ref='$sref'
					AND c.data='$gbd_data' AND c.proces='$gproces' AND c.grup='$ggrup'
					GROUP BY cl.ref";
					$result3=mysqli_query($conn,$query3);
					if (!$result3) {die("Query3 to show fields from table failed:" . mysqli_error($conn));}

					list($r, $ssprod)=mysqli_fetch_row($result3);
					if(!$ssprod)
					{
						if ($pprod==$sprod)
						{
							echo '<option value="'.$sprod.'" selected>'.$sprod.'</option>';
							$input_ref=$sref;
						}
						else {echo '<option value="'.$sprod.'">'.$sprod.'</option>';}
					}
				}
				echo '</SELECT>';
			}
		?>
	 </div>
 </div>



</div>
</form>
<form action="<?php echo $form_action; ?>" method="post" name="frmmes2" id="frmmes2">

<input type="hidden" name="nouf" id="nouf" value="<?php echo $paddfam; ?>">
<input type="hidden" name="prov" id="prov" value="<?php echo $pprov; ?>">
<input type="hidden" name="prod" id="prod" value="<?php echo $pprod; ?>">
<input type="hidden" name="ref" id="ref" value="<?php echo $input_ref; ?>">
<input type="hidden" name="num" id="num" value="<?php echo $input_num; ?>">

<div class="u-text-center">
<button class="button  button--white button--animated" name="acceptar" type="button" <?php echo $disabled3; ?> onclick="document.frmmes2.submit()">Aceptar  <i class="fa fa-check" aria-hidden="true"></i></button>
</div>


</form>


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


