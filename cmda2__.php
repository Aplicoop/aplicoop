<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' )
{

	$superuser=strtoupper($_SESSION['user']);
	$user=$_SESSION['user'];

	$sessionid = $_SESSION['sessionid'];

	$num=$_POST["num"];
	$unitat=$_POST["uni"];
	$pref=$_POST["ref"];

	$files = count($num);

	$proces=$_GET['id'];
	$numcmda=$_GET['id2'];
	//$data=$_GET['id3'];
	$pres=$_GET['id4'];

	function tradueixData2($d)
	{
				$angles = array(    "Monday",
                                "Tuesday",
                                "Wednesday",
                                "Thursday",
                                "Friday",
                                "Saturday",
                                "Sunday",
                                "Mon",
                                "Tue",
                                "Wed",
                                "Thu",
                                "Fri",
                                "Sat",
                                "Sun",
                                "January",
                                "February",
                                "March",
                                "April",
                                "May",
                                "June",
                                "July",
                                "August",
                                "September",
                                "October",
                                "November",
                                "December",
                                "Jan",
                                "Feb",
                                "Mar",
                                "Apr",
                                "May",
                                "Jun",
                                "Jul",
                                "Aug",
                                "Sep",
                                "Oct",
                                "Nov",
                                "Dec");

 				$catala = array(    "/Dilluns/",
                                "/Dimarts/",
                                "/Dimecres/",
                                "/Dijous/",
                                "/Divendres/",
                                "/Dissabte/",
                                "/Diumenge/",
                                "/Dll/",
                                "/Dmr/",
                                "/Dmc/",
                                "/Djs/",
                                "/Dvd/",
                                "/Dss/",
                                "/Dmg/",
                                "/Gener/",
                                "/Febrer/",
                                "/Març/",
                                "/Abril/",
                                "/Maig/",
                                "/Juny/",
                                "/Juliol/",
                                "/Agost/",
                                "/Setembre/",
                                "/Octubre/",
                                "/Novembre/",
                                "/Desembre/",
                                "/Gen/",
                                "/Feb/",
                                "/Mar/",
                                "/Abr/",
                                "/Mai/",
                                "/Jun/",
                                "/Jul/",
                                "/Ago/",
                                "/Set/",
                                "/Oct/",
                                "/Nov/",
                                "/Des/");

		$ret2 = preg_replace($catala, $angles, $d);
		return $ret2;
	}

	include 'config/configuracio.php';
?>

<html lang="es">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>crear/editar pedido ::: la coope</title>
	</head>
	<style type="text/css">
	a#color:link, a#color:visited {color:white; border: 1px solid #9cff00;}
	a#color:hover {color:black; border: 3px solid #9cff00;   -moz-border-radius: 10%;}
   a#color:active {color:white; border: 3px solid #9cff00;  -moz-border-radius: 10%;}
	</style>

	<style type="text/css" media="print">
    .NonPrintable
    {
      display: none;
    }
  </style>


<script language="javascript" type="text/javascript">

function validate_form(){
var x = new Array();
var nom = new Array();

for (i = 0; i < this.document.frmComanda.elements['num[]'].length; i++){
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

}
return true;
}

</script>

<body>

<div class="pagina" style="margin-top: 10px;">
	<div class="contenidor_1" style="border: 1px solid #9cff00;">

<?php
	$sel5 = "SELECT dia FROM usuaris WHERE nom='$user'";
	$result5 = mysqli_query($conn,$sel5);
	if (!$result5) {die('Invalid query5: ' . mysqli_error($conn)); }
	list($grup)= mysqli_fetch_row($result5);

	$sel6 = "SELECT tipus, data_inici, data_fi, periode, dia_recollida, dia_tall, hora_tall
				FROM processos WHERE nom='$proces' AND grup='$grup'";
	$result6 = mysqli_query($conn,$sel6);
	if (!$result6) {die('Invalid query6: ' . mysqli_error($conn)); }
	list($tipus,$datai,$dataf,$periode,$diare,$diat,$horat)= mysqli_fetch_row($result6);

	$title=$proces." / grup ".$grup;

	////////////////////////
	// Això és id4=create //
	////////////////////////

	if ($pres=='create')
	{
		if (!$numcmda)
		{
			/////////////////////////////////////
			// anem a generar una comanda nova //
			// apareix la fitxa de productes buida   //
			/////////////////////////////////////

			$cap=$title;
		  	$goto='cmda2.php?id='.$proces.'&id4=vis';
		}
		else
		{
			//////////////////////////////////////////////////////
			/// Editem una comanda ja realitzada /////
			//////////////////////////////////////////////////////

			$cap='Comanda numero: '.$numcmda.' / '.$title;
			$goto='cmda2.php?id='.$proces.'&id2='.$numcmda.'&id4=vis';
		}
?>

		<p class="h1" style="background: #9cff00; text-align: left; padding-left: 20px;">
		<?php echo $cap; ?></p>

		<div class="cat">
<?php

		$color=array("#C0C000","#00b2ff","orange","#b20000","#14e500","red","#8524ba","green");

		$sel = "SELECT categoria FROM proces_linia
		WHERE proces='$proces' AND grup='$grup' AND actiu='activat'
		ORDER BY ordre";
		$result = mysqli_query($conn,$sel);
		if (!$result) {die('Invalid query: ' . mysqli_error($conn)); }

		$cc=0; $slc=0;
		while (list($cat)= mysqli_fetch_row($result))
		{
			print ('<a href="#'.$cat.'" id="color" style="background: '.$color[$slc].'; "><span>'.$cat.'</span></a>');
			$cc++; $slc++;
			if ($cc==7)
			{
				print ('</div><div class="cat">');
				$cc=0;
			}
			if ($slc==8) {$slc=0;}
		}
		$count=count($cat);
		mysqli_free_result($result);

		?>

		</div>

		<div id="contenidor_1" style="height: 350px; clear: both; overflow: scroll; overflow-x: hidden; ">

		<form action="<?php echo $goto; ?>" method="post" name="frmComanda" id="frmComanda" onSubmit="return validate_form()"  >

		<?php

		$sel = "SELECT categoria FROM proces_linia
		WHERE proces='$proces' AND grup='$grup' AND actiu='activat'
		ORDER BY ordre";
		$result = mysqli_query($conn,$sel);
		if (!$result) {die('Invalid query: ' . mysqli_error($conn)); }

		$id=0;
		$cc=0;
		while (list($cat)= mysqli_fetch_row($result))
		{
			print ('<a name="'.$cat.'"></a>
					<p class="h1" style="background: '.$color[$cc].'; text-align: left; padding-left: 20px;">'.$cat.'</a></p>');
			$cc++;
			if ($cc==7){$cc=0;}

			$sel2 = "SELECT pr.ref,pr.nom,pr.unitat,pr.proveidora,ctg.tipus,ctg.estoc,pr.subcategoria,pr.preusi,pr.iva,
			pr.marge, pr.descompte,pr.estoc FROM productes AS pr, categoria AS ctg
			WHERE pr.categoria=ctg.tipus AND pr.categoria='$cat' AND pr.actiu='actiu'
			ORDER BY pr.categoria, pr.nom ";

			$result2 = mysqli_query($conn,$sel2);
			if (!$result2) { die('Invalid query2: ' . mysqli_error($conn)); }

			print ('<table align="centre" width="100%" class="cos"
			style="padding-left: 30; padding-right: 30; ">
			<tr>');

			$contador=0;
			while(list($ref,$nomprod,$unitat,$prov,$categ,$ctg_estoc,$subcat,$preu,$iva,$marge,$descompte,$pr_estoc) = mysqli_fetch_row($result2))
			{
				//// En els productes d'estoc, apareix l'estoc ////
				//// Si l'estoc es negatiu apareix en gris ////
				if ($ctg_estoc=='si')
				{
					$rpr_estoc=round($pr_estoc,1);
					$w_estoc="[".$rpr_estoc."]";
					if ($pr_estoc<=0) {$color_cos="color: grey;";}
					else {$color_cos="";}
				}
				else {$w_estoc="";}

				if (!$numcmda)
				{
					$qdec="";
				}
				else
				{
					$sel3 = "SELECT quantitat FROM comanda_linia
					WHERE numero='$numcmda' AND ref='$ref'";

					$result3 = mysqli_query($conn,$sel3);
					if (!$result3) { die('Invalid query3: ' . mysqli_error($conn)); }
					list ($quantitat) = mysqli_fetch_row($result3);
					$qdec="";
					if ($quantitat!="")
					{
					/// per veure la quantitat amb els decimals imprescindibles /////
						$r2=round($quantitat, 2)*1000;
						$r1=round($quantitat, 1)*1000;
						$r0=round($quantitat)*1000;
						$rb=$quantitat*1000;
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
					$qdec=round($quantitat, $nd);
					}
					//////////////////////////////////////
				}

				//// càlcul del pvp ///
				/// inclou iva i marge, però no descompte ////
				$pvp=$preu*(1+$iva)*(1+$marge);
				$pvp=sprintf("%01.2f", $pvp);

				//// si existeix un descompte apareix en vermell ////
				$w_desc="";
				if ($descompte!=0)
				{
					$descompte=$descompte*100;
					$w_desc="<span style='color:red; text-decoration:blink' > descompte:".$descompte."%</span>";
				}

				print('<td width="6%">
		      <input align="right" name="num[]" id="num'.$id.'" type="TEXT" value="'.$qdec.'" maxlength="5" size="3">
				</td>
				<td width="26%" style="'.$color_cos.'">
            '.$nomprod.' ('.$pvp.' &#8364;/'.$unitat.') '.$w_estoc.' '.$w_desc.'
            <input type=hidden name="ref[]" id="ref'.$id.'" value="'.$ref.'">
            <input type=hidden name="nom[]" id="nom'.$id.'" value="'.$nomprod.'">
             <input type=hidden name="uni[]" value="'.$unitat.'">
            </td>');

				$contador++;
				$id++;

				if ($contador==3)
				{
					print ('</tr><tr>');
					$contador=0;
				}

			}

			print ('</tr></table>');
		}

		?>

		</div>
		<p class="linia_button2" style="background: #9cff00; text-align: center; vertical-align: middle;">
		<input class="button2" name="acceptar" type="submit" id="btnComanda" value="Acceptar">
		<input class="button2" type="button" value="Sortir" onClick="javascript:history.go(-1);">
		</p>

<?php
	}

	////////////////////////
	// Això és id4=vis    //
	////////////////////////

	else
	{
		if ($tipus=='continu' AND $periode='setmanal'){$title1="recollida";}
		if ($tipus=='període concret'){$title1="fi periode";}

		if (!$numcmda)
		{
			////////////////////////////////////////
			// Anem a visualitzar la comanda nova //
			// Manca data i numcmda    			  //
			////////////////////////////////////////

			$count_files=0;
			for ($i=0; $i<$files; $i++)
			{
				if ($num[$i] != "")
				{
					$count_files++;
				}
			}
			if ($count_files==0)
			{
				// Si no hi ha cap quantitat elegida no continua endavant //

				echo '<p class="error" style="padding-top: 50px;">
				No has introduït cap quantitat a cap producte!!!!
				</p>';

				die ('<p class="error" style="font-size: 14px; padding-bottom: 50px;"><a href="cmda2.php?id='.$proces.'&id4=create"
				title="clica per tornar a la comanda">
				Torna a la comanda</a></p>');
			}
				//////////////////
			else
			{
				date_default_timezone_set("Europe/Madrid");
				$time_avui = time();
				if ($tipus=='continu' AND $periode='setmanal')
				{
					$horat_0=(int)substr($horat,0,2);
					$mint_0=(int)substr($horat,2,2);
					$horat_calc=(60*$horat_0)+$mint_0;
					$horat_verb=$horat_calc." minutes";
					$diare_a=tradueixData2(ucfirst($diare));
					$diat_a=tradueixData2(ucfirst($diat));
					$diat_w3=substr($diat_a, 0, 3);
					$diaw3_today=date('D');
					if ($diat_w3==$diaw3_today)
					{
						$hora_ara=(int)date('G');
						$min_ara=(int)date('i');
						$ara=($hora_ara*60)+$min_ara;
						if ($horat_calc>=$ara)
						{
							$diat_0=mktime(0,0,0,date('m'),date('d'),date('y'));
						}
						else
						{
							$diat_0=strtotime("next ".$diat_a);
						}
					}
					else
					{
						$diat_0=strtotime("next ".$diat_a);
					}
					$time_diats=strtotime("+ ".$horat_verb, $diat_0);
					$ver_diats=date("d-m-Y H:i", $time_diats);
					$diat_2=strtotime("- 7 days", $time_diats);
					$time_diati=strtotime("+ 1 second", $diat_2);
					$ver_diati=date("d-m-Y H:i", $time_diati);
					$time_diare=strtotime("next ".$diare_a, $diat_0);
					$bd_diare=date("Y-m-d", $time_diare);
					$ver_diare=date("d-m-Y", $time_diare);
					$data=$ver_diare;
					$bd_data=$bd_diare;
				}
				if ($tipus=='període concret')
				{
					$time_datai = strtotime($datai);
					$time_dataf = strtotime($dataf);
					$ver_datai = date("d-m-Y", $time_datai);
					$ver_dataf = date("d-m-Y", $time_dataf);
					$data=$ver_dataf;
					$bd_data=$dataf;
				}

				//comprovació de que no es generen dues comandes alhora //
				$sel = "SELECT numero
				FROM comanda
				WHERE usuari='$user' AND proces='$proces' AND grup='$grup' AND data='$bd_data'";
				$result = mysqli_query($conn,$sel);
				if (!$result) {die('Invalid query: ' . mysqli_error($conn));}

				list ($numcmda1) = mysqli_fetch_row($result);

				if ($numcmda1 != "")
				{
					echo '<p class="error" style="padding-top: 50px;">
						PERILL DE DUPLICACIÓ DE COMANDA!!!!
						</p>';
					die ('<p class="error">
						Ja heu creat una comanda per a un procés '.$proces.'-'.$grup.' amb data '.$data.'.
						</p>
						<p class="error" style="font-size: 14px;">
						<a href="cmda2.php?id='.$proces.'&id2='.$numcmda1.'&id4=vis"
						title="clica per editar aquesta comanda">
						editar la comanda vigent</a>
						</p>');
				}

				/////////////////////////////////////////

				/// La data a la taula comanda és data fi periode si el procés és de eríode concret ///
				/// o la data de recollida si és un procés continu setmanal ///
				$query2 = "INSERT INTO `comanda` ( `usuari` , `proces`, `grup`, `sessionid` , `data` )
				VALUES ('$user', '$proces', '$grup', '$sessionid', '$bd_data')";
				mysqli_query($conn,$query2) or die('Error, insert query2 failed');
				$numcmda=mysqli_insert_id();
				$ver_datase=date("d-m-Y");
				$notescmda="";
				$familia=$user;
				$superfam=strtoupper($familia);
			}
		}
		else
		{
			///////////////////////////////////////////////
			// Anem a visualitzar una comanda ja realitzada  //
			// Agafem data de la base de dades i numcmda //
			// del GET.												//
			///////////////////////////////////////////////

			$sel3="SELECT c.usuari, c.data, c.sessionid, c.notes, s.date
			FROM comanda AS c, session AS s
			WHERE c.numero='$numcmda' AND c.sessionid=s.sessionid";

			$query3=mysqli_query($conn,$sel3) or die(mysqli_error($conn));
			list($familia, $bd_data, $sessionid, $notescmda, $bd_datase) = mysqli_fetch_row($query3);
			$superfam=strtoupper($familia);
			$time_dataf = strtotime($bd_data);
			$data = date("d-m-Y", $time_dataf);
			list($any, $mes, $first) = explode("-", $bd_datase);
			list($mdia, $second) = explode (" ", $first);
			$ver_datase=$mdia." - ".$mes." - ".$any;
		}

		// $editar=1 implica que la comanda encara està en periode d'edició. //
		// $editar=0 no es pot editar //
		date_default_timezone_set('Europe/Madrid');
 		if ($tipus=='període concret')
 		{
 			$ver_avui = date("Y-m-d");
			$time_avui = strtotime(date("Y-m-d"));
			$time_data=strtotime($bd_data);
 			if ($time_avui<=$time_data) {$editar=1;}
 			else {$editar=0;}
 		}

 		if ($tipus=='continu' AND $periode='setmanal')
 		{
 			$ver_avui = date("Y-m-d H:i");
			$time_avui = strtotime(date("Y-m-d H:i"));
 			$horat_0=(int)substr($horat,0,2);
			$mint_0=(int)substr($horat,2,2);
			$horat_calc=(60*$horat_0)+$mint_0;
			$horat_verb=$horat_calc." minutes";
			$diat_a=tradueixData2(ucfirst($diat));
			$time_data=strtotime($bd_data);
			$diat_0=strtotime("last ".$diat_a, $time_data);
			$time_diats=strtotime("+ ".$horat_verb, $diat_0);
			$ver_diats=date("d-m-Y H:i", $time_diats);
			$diat_2=strtotime("- 7 days", $time_diats);
			$time_diati=strtotime("+ 1 second", $diat_2);
			$ver_diati=date("d-m-Y H:i", $time_diati);
 			if ($time_diats>=$time_avui){$editar=1;}
 			else {$editar=0;}
 		}

 		//User=familia vol dir que qui ho consulta és el propietari de la comanda
 		//Si és diferent vol dir que qui vol veure-ho no és el propietari i per tant entra
 		//només per fer consulta.
 		//$logo_factura està definida a l'arxiu de configuració
 		if ($user!=$familia)
 		{
 			$editar=0;
 		}

 		if ($editar==0)
		{
		$button='<input class="button2" type="button" value="SORTIR"
		onClick="javascript:history.go(-1);">
		<input class="button2" type="button" name="imprimir" value="IMPRIMIR" onclick="window.print();">';
		}
		else
		{
		$button='<input class="button2" type="button" value="D\'ACORD"
		onClick="javascript:window.location = \'comandes.php?id3='.$user.' \';">
		<input class="button2" type="button" value="EDITAR"
		onClick="javascript:window.location = \'cmda2.php?id='.$proces.'&id2='.$numcmda.'&id4=create \';">
		<input class="button2" type="button" value="ELIMINAR"
		onClick="var answer = confirm (\'Estàs segur que vols borrar aquesta comanda!! \')
				if (answer)
					{window.location=\'delcom.php?id='.$numcmda.' \'}">
		<input class="button2" type="button" name="imprimir" value="IMPRIMIR" onclick="window.print();">';
		}
?>

<body>

	<div class="contenidor_fac">

	<div class="NonPrintable">
	<p class="linia_button2" style="background: #9cff00; text-align: center; vertical-align: middle;">
	<?php echo $button; ?>
	</p>
	</div>
			<div class="contenidor_4" style="float:left;">
				<img id="fig" style="width:175px; height:85px; padding: 10px 0px 20px 0px ;" src="<?php echo $logo_factura; ?>">
			</div>

			<div style="width: 510px; float:right;">
				<p class="h3"
				style="font-weight: bold; text-align: left; padding-left:40px; padding-right:25px;
					 vertical-align: middle;">
				<span style="color: grey;">Comanda nº </span><?php echo $numcmda; ?>
				<br/>
				<span style="color: grey;">Nom família: </span><?php echo $superfam; ?>
				<br/>
				<span style="color: grey;">Data <?php echo $title1; ?>: </span><?php echo $data; ?>
				</p>
			</div>

		<div style="clear: both; border-top: 1px solid black; border-bottom: 1px solid black;">
		<table width="100%" align="center">
			<tr class="cos_majus" style="font-size:18px;" valign="baseline">
				<td width="50%" align="left" style="padding:15px 0px;"><u>Producte</u></td>
				<td width="20%" align="center"><u>Quantitat</u></td>
				<td width="10%" align="center"><u>PVP</u><sup>*</sup></td>
				<td width="10%" align="center"><u>Descompte</u></td>
				<td width="10%" align="right"><u>Total</u></td>
			</tr>
<?php
		if ($files!=0)
		{
			//////////////////////////////////////////////////////////////////
			// Entren quantitats de productes, en el cas que exiteixin POST //
			//////////////////////////////////////////////////////////////////
			$sel4= "SELECT numero FROM comanda_linia WHERE numero='$numcmda'";
			$query4=mysqli_query($conn,$sel4) or die(mysqli_error($conn));
			list($cl_num) = mysqli_fetch_row($query4);
			$cl_num_ver= count($cl_num);
			if ($cl_num_ver !=0)
			{
				///////////////////////////////////////////////////////////////
				// Si estem reeditant, primer borrem les quantitats antigues //
				///////////////////////////////////////////////////////////////
				$query5="DELETE FROM comanda_linia WHERE numero='$numcmda'";
				mysqli_query($conn,$query5) or die('Error, delete query5 failed');
			}
				////////////////////////////////////////////////////////////////////
				// Editant per primera vegada o reeditant, entrem les dades noves //
				////////////////////////////////////////////////////////////////////
			for ($i=0; $i<$files; $i++)
			{
				if ($num[$i] != "" AND $num[$i] != 0)
				{
					$query4 = "INSERT INTO `comanda_linia` ( `numero` , `ref`, `quantitat` )
					VALUES ('$numcmda', '$pref[$i]', '$num[$i]')";
					mysqli_query($conn,$query4) or die('Error, insert query failed');
				}
			}
		}

		// Visualitzem les dades //
		$sel5="SELECT cl.ref, pr.nom, pr.proveidora, cl.quantitat, pr.unitat,pr.preusi,pr.iva,pr.marge,pr.descompte
		FROM comanda_linia AS cl, productes AS pr
		WHERE numero='$numcmda' AND cl.ref=pr.ref
		ORDER BY pr.categoria,pr.proveidora,pr.nom";
		$result5=mysqli_query($conn,$sel5) or die(mysqli_error($conn));
		while (list ($clref, $nomprod, $nomprod2, $quantitat, $unitat, $preu, $iva, $marge, $descompte)= mysqli_fetch_row($result5))
		{
			$pvp=$preu*(1+$marge)*(1+$iva);
			$pvp=sprintf("%01.2f", $pvp);
			$qdec = sprintf("%01.2f", $quantitat);
			$total=$quantitat*$pvp*(1-$descompte);
			$qtot = sprintf("%01.2f", $total);
			$total2=$total2+$total;
			$qtot2 = sprintf("%01.2f", $total2);
			if ($descompte!=0)
			{
				$desc=$descompte*100;
				$w_desco='<td align="center">'.$desc.'%</td>';
			}
			else { $w_desco="<td></td>";}

			print('<tr class="cos16"><td>'.$clref.' - '.$nomprod.' - '.$nomprod2.'</td>
				<td align="center">'.$qdec.' '.$unitat.'</td>
				<td align="center">'.$pvp.'&#8364;</td>
				'.$w_desco.'
				<td align="right">'.$qtot.'&#8364;</td></tr>');
		}
?>
	</table>
	<table width="100%"><tr class="cos16" style="font-weight: bold;">
	<td width="80%" align="right" style="padding:15px 0px;">Total</td>
	<td width="20%" align="right"><?php echo $qtot2; ?>&#8364;</td>
	</tr>
	</table>
	</div>
	<p class="cos2" style="clear: both; text-align: left;">
	Comanda realitzada el <?php echo $ver_datase; ?> dins la sessió nº <?php echo $sessionid; ?>.
	(*) Preu de venda aproximat (darrer preu actualitzat). Inclou iva.
	</p>
	</div>

<?php
	}
?>

	</form>
	</div></div>
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
