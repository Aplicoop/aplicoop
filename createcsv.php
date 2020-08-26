<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];

$bd_data=$_GET['id'];
$proces=$_GET['id2'];
$grup=$_GET['id3'];
$estoc=$_GET['id4'];

$data=date("d-m-Y", strtotime ($bd_data));

////////////////////////////////////////
///GET id4 genera la variable estoc ///
/// Aquesta pot ser 						///
/// 0: comanda sense estoc				///
/// 1: comanda amb estoc 				///
/// 2: factures 							////
///////////////////////////////////////

if ($estoc==1)
{
$link="totalcomanda.php?id=".$data."&id2=".$proces."&id3=".$grup."&id4=1";
$title="comanda total amb estoc";
$where="";
$pre_arxiu="comandatotal_amb_estoc".$bd_data."_".$grup."_".$proces.".csv";
}
elseif($estoc==0)
{
$link="totalcomanda.php?id=".$data."&id2=".$proces."&id3=".$grup."&id4=0";
$title="comanda total sense estoc";
$where="AND cat.estoc = 'no' ";
$pre_arxiu="comandatotal_sense_estoc".$bd_data."_".$grup."_".$proces.".csv";
}
elseif($estoc==2)
{
$link="cistelles.php?id2=".$data."&id3=".$proces."&id4=".$grup."&id5=0";
$title="total servit";
$where="";
$pre_arxiu="facturatotal_".$bd_data."_".$grup."_".$proces.".csv";
}
?>

<html lang="es">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>crea arxiu csv ::: la coope</title>

<style type="text/css">
	a#color:link, a#color:visited {color:white; border: 1px solid #9cff00;}
	a#color:hover {color:black; border: 1px solid #9cff00;   -moz-border-radius: 10%;}
   a#color:active {color:white; border: 1px solid #9cff00;  -moz-border-radius: 10%;}
</style>
</head>

<body>
<div class="pagina" style="margin-top: 10px;">

<div class="contenidor_1" style="border: 1px solid green;">
<p class='path'>
><a href='admint.php'>administració</a>
>><a href='grups_comandes.php'>grups de comandes i cistelles</a>
>>><a href='<?php echo $link; ?>'> <?php echo $title." ".$proces."-".$grup."-".$data; ?></a>
</p>
<p class="h1" style="background: green; text-align: left; padding-left: 20px;">
<?php echo $title." ".$proces."-".$grup."-".$data; ?></p>

<?php

include 'config/configuracio.php';

$select3="SELECT nom FROM proveidores";
$resultat3=mysqli_query($conn,$select3);
if (!$resultat3) {die("Query to show fields from table select3 failed");}
$numrowsat3=mysqli_num_rows($resultat3);
while (list($prove)=mysqli_fetch_row($resultat3))
{
	$select2="SELECT cl.numero, c.data, cl.ref, pr.nom, pr.categoria, cat.estoc
	FROM comanda_linia AS cl, comanda AS c, productes AS pr, categoria AS cat
	WHERE c.numero=cl.numero AND cl.ref=pr.ref AND c.proces='$proces' AND pr.categoria=cat.tipus
	AND c.proces='$proces' AND c.data='$bd_data' AND pr.proveidora='$prove' ".$where."
	ORDER BY pr.categoria, pr.nom";
	$resultat2=mysqli_query($conn,$select2);
	if (!$resultat2) {die("Query to show fields from table select2 failed");}
	$numrowsat2=mysqli_num_rows($resultat2);

	$titol="";
	$header1="";
	$dades="";
	$header2="";
	$line="";
	$final=="";
	if ($numrowsat2!=0)
	{
		///Títol proveïdor///
		$titol=$prove.";";

		$select = "SELECT numero,usuari,data2,numfact FROM comanda
		WHERE proces='$proces' AND grup='$grup' AND data='$bd_data'
		ORDER BY usuari";
		$result1=mysqli_query($conn,$select);
		if (!$result1) {die("Query to show fields from table select failed");}
		$numrows1=mysqli_num_rows($result1);

		/// Capçalera 1 ///
		$header1=";;;Famí­lies;";
		$dades="";
		$i=0;
		while (list($numero,$familia,$datafact,$numfact)=mysqli_fetch_row($result1))
		{
			$fila[]=$numero;
			if($estoc==2)
			{
				$yearfact= date_parse($datafact);
				$yearfact=$yearfact["year"];
				$header1 .= $familia.";".$numfact."/".$yearfact.";";
			}
			else
			{
				$header1 .= $familia.";num(".$fila[$i].");";
			}

			$i++;
		}
		if($estoc==2){$header1 .= "Total;Total;Total;Total;";}

		/// Capçalera2
		/// Si estoc=2 desglossem l'iva el descompte i els totals de preu i iva ///
		$header2="Productes;Preu/Kg;Pes Total;Euros Total;";
		if ($estoc==2){$header2="Productes;Preu/Kg;Iva;Descompte;";}

		for ($x=1; $x<=$numrows1; $x++)
		{
			if($estoc==2){$header2 .= "Quantitat;Subtotal;";}
			else{$header2 .= "Pes;Euros;";}
		}
		if ($estoc==2){$header2 .="servit;preu;iva;;";}
		///

		///línia de resultats///
		$taula = "SELECT cl.ref, pr.nom, pr.unitat AS uni, cl.preu, cl.iva, cl.descompte,
		SUM(cl.quantitat) AS sum, SUM(cl.cistella) AS totalservit,
		SUM(cl.cistella*cl.preu*(1-cl.descompte)) AS totalpreu, SUM(cl.cistella*cl.preu*cl.iva) AS totaliva,
		SUM(cl.cistella*cl.preu*(1-cl.descompte)*(1+cl.iva)) AS totalfinal
		FROM comanda AS c, comanda_linia AS cl, productes AS pr
		WHERE c.numero=cl.numero AND cl.ref=pr.ref AND c.proces='$proces'
		AND c.grup='$grup' AND c.data='$bd_data' AND pr.proveidora='$prove'
		GROUP BY cl.ref
		ORDER BY pr.nom";
		$result_taula = mysqli_query($conn,$taula);
		if (!$result_taula) {die('Invalid query: ' . mysqli_error($conn));}
		while (list($ref,$nomprod,$uni,$preu,$iva,$desc,$sum,$tservit,$tpreu,$tiva,$ttotal)=mysqli_fetch_row($result_taula))
		{
			/// Prevenim problemes en el pas a csv. Treiem apostrof i canviem . per comes en els decimals ///
			$line="";
			$nomprod_p=str_replace("\'", "", $nomprod);
			$uni_p=str_replace("\'", "", $uni);
			$sum=str_replace(".",",",$sum);
			$preu_p=str_replace(".",",",$preu);
			$desc_p=$desc*100;
			$desc_p=$desc_p." %";
			$iva_p=$iva*100;
			$iva_p=$iva_p." %";
			$tservit_p=str_replace(".",",",$tservit);
			$tpreu_p=sprintf ("%.2f", $tpreu);
			$tpreu_p=str_replace(".",",",$tpreu_p);
			$tiva_p=sprintf ("%.2f", $tiva);
			$tiva_p=str_replace(".",",",$tiva_p);
			$ttotal_p=sprintf ("%.2f", $ttotal);
			$ttotal_p=str_replace(".",",",$ttotal_p);

			/// Iniciem la linia de resultats ///
			$line = $nomprod_p.";;".$sum.";;";
			if ($estoc==2){$line = $nomprod_p.";".$preu_p."€/".$uni_p.";".$iva_p.";".$desc_p.";";}
			///
			$taula2 = "SELECT c.numero, c.usuari, cl.ref, cl.quantitat, cl.cistella
			FROM comanda AS c
			LEFT JOIN comanda_linia AS cl ON c.numero=cl.numero
			WHERE c.proces='$proces' AND c.grup='$grup' AND c.data='$bd_data'
			AND cl.ref='$ref'
			ORDER BY c.usuari";
			$result2 = mysqli_query($conn,$taula2);
			if (!$result2) {die('Invalid query: ' . mysqli_error($conn));}
			$numrows2=mysqli_num_rows($result2);
			$j=0; $i=0; $k=0;
			while(list($numcmda,$familia,$nomprod2,$quant,$cistella)=mysqli_fetch_row($result2))
			{

				/// numrows1 és el nombre de comandes del proces ///
				for ($i=$j; $i<$numrows1; $i++)
				{
					$numfila=$fila[$i];
					if ($numcmda==$numfila)
					{
 						if ($estoc==2)
 						{
							$cistella_p=str_replace(".",",",$cistella);
 							$subtotal= $cistella*$preu*(1+$iva)*(1-$desc);
 							$subtotal_p=str_replace(".",",",$subtotal);
 							$line .= $cistella_p.";".$subtotal_p.";";
 							///numrows2 és el nombre de comandes que contenen un producte en aquest procés///
 							$j++; $k++;
 							if($k<$numrows2){break;}
 						}
 						else
 						{
 							$quant=str_replace(".",",",$quant);
 							$line .= $quant.";;";
							$j++;
 							break;
 						}
 					}
					else
					{
						$line .= ";;";
						$j++;
 					}
				}
			}
			///Si estoc=2 acabem la fila amb els totals ///
			if ($estoc==2)	{$line .= $tservit_p.";".$tpreu_p.";".$tiva_p.";".$ttotal_p.";";}
		///

		//Ajuntem les línies de resultat
			$dades .= trim($line)."\n";
		}

		$dades = str_replace("\r", "", $dades);
		if ($dades == "")
		{
			$dades = "\n(0) Records Found!\n";
		}

	///Calculem els totals per proveÏdora ///
		$final = "\n\n";
		if ($estoc==2)
		{
			$final="Total per proveïdora;;";
			for ($i=0; $i<=$numrows1; $i++) {$final .=";;";}

	 		$taula_final = "SELECT pr.proveidora, c.proces, c.grup, c.data, SUM(cl.cistella) AS totalservit2,
			SUM(cl.cistella*cl.preu*(1-cl.descompte)) AS totalpreu2, SUM(cl.cistella*cl.preu*cl.iva) AS totaliva2,
			SUM(cl.cistella*cl.preu*(1-cl.descompte)*(1+cl.iva)) AS totalfinal2
			FROM comanda AS c, comanda_linia AS cl, productes AS pr
			WHERE c.numero=cl.numero AND pr.ref=cl.ref AND pr.proveidora='$prove'
			AND c.proces='$proces' AND c.grup='$grup' AND c.data='$bd_data'
			GROUP BY pr.proveidora";
			$result_taula_final = mysqli_query($conn,$taula_final);
			if (!$result_taula_final) {die(mysqli_errno() . mysqli_error($conn) .'a taula final');}
			list($nomprov2,$pr2,$gr2,$da2,$tservit2,$tpreu2,$tiva2,$ttotal2)=mysqli_fetch_row($result_taula_final);
			$tservit2_p=str_replace(".",",",$tservit2);
			$tpreu2_p=sprintf ("%.2f", $tpreu2);
			$tiva2_p=sprintf ("%.2f", $tiva2);
			$ttotal2_p=sprintf ("%.2f", $ttotal2);
			$tpreu2_p=str_replace(".",",",$tpreu2_p);
			$tiva2_p=str_replace(".",",",$tiva2_p);
			$ttotal2_p=str_replace(".",",",$ttotal2_p);
			$final.=$tservit2_p.";".$tpreu2_p.";".$tiva2_p.";".$ttotal2_p.";\n\n";
		}

	//Ajuntem el tot el cotingut de l'arxiu
		$content .= $titol."\n".$header1."\n".$header2."\n".$dades."\n".$final."\n";
	}
}
/// Si estoc=2 Calculem els totals per família al final de tot ///
if($estoc==2)
{
	$abaix1="Total per famílies;;;;";
	$abaix2=";;;;";
	$abaix3=";;;;";
	$abaix4=";;;;";

	$taula_abaix = "SELECT c.usuari, c.numero, c.numfact, SUM(cl.cistella*cl.preu*(1-cl.descompte)) AS totalpreu3,
	SUM(cl.cistella*cl.preu*cl.iva) AS totaliva3, SUM(cl.cistella*cl.preu*(1-cl.descompte)*(1+cl.iva)) AS totalfinal3
	FROM comanda AS c, comanda_linia AS cl
	WHERE c.numero=cl.numero AND c.proces='$proces' AND c.grup='$grup' AND c.data='$bd_data'
	GROUP BY c.usuari ORDER BY c.usuari";
	$result_taula_abaix = mysqli_query($conn,$taula_abaix);
	if (!$result_taula_abaix) {die(mysqli_errno() . mysqli_error($conn) .'a taula abaix');}
	while( list($fam3,$num3,$fact3,$tpreu3,$tiva3,$ttotal3)=mysqli_fetch_row($result_taula_abaix) )
	{
		$yearfact3=	date_parse($bd_data);
		$yearfact3=$yearfact3["year"];
		$tpreu3_p=sprintf ("%.2f", $tpreu3);
		$tiva3_p=sprintf ("%.2f", $tiva3);
		$ttotal3_p=sprintf ("%.2f", $ttotal3);
		$tpreu3_p=str_replace(".",",",$tpreu3_p);
		$tiva3_p=str_replace(".",",",$tiva3_p);
		$ttotal3_p=str_replace(".",",",$ttotal3_p);
		$abaix1.=$fam3.";".$fact3."/".$yearfact3.";";
		$abaix2.="Total preu;".$tpreu3_p.";";
		$abaix3.="Total iva;".$tiva3_p.";";
		$abaix4.="Total;".$ttotal3_p.";";
	}

	$abaix1 = trim($abaix1)."\n";
	$abaix2 = trim($abaix2)."\n";
	$abaix3 = trim($abaix3)."\n";
	$abaix4 = trim($abaix4)."\n";

	$abaix=$abaix1.$abaix2.$abaix3.$abaix4;
	$abaix = str_replace("\r", "", $abaix);

	$content.=$abaix;
}
/// Creem l'arxiu, la variable pre_arxiu està definida al principi ///
$arxiu=str_replace(" ","",$pre_arxiu);
$arxiu_dir= "download/".$arxiu;
$fp=fopen($arxiu_dir,"w");

fwrite($fp,$content);
fclose($fp);

if (!$fp) { die("No s'han pogut crear els arxius desitjats");}

else{
$exit="L'arxiu ".$arxiu." s'ha creat amb exit";
//$exit=iconv("UTF-8","ISO-8859-1",$exit); Ha de ser-hi si no es treballa amb UTF8

//copia de seguretat
//$avui=date("Y-m-d");
//$sqlFile = 'download/'.$dbname.'_'.$avui.'.sql';

//$creatBackup = "mysqldump --add-drop-table -u ".$dbuser." --password=".$dbpass." ".$dbname." > ".$sqlFile;
//exec($creatBackup);

//$exit2="Igualment s'ha realitzar amb èxit la copia de seguretat que podeu trobar a ".$sqlFile;
}

?>

<div class="contenidor_fac">
<p class='cos2'><?php echo $exit; ?></p>
<p class='cos2'><?php echo $exit2; ?></p>
<p class="linia_button2" style="padding:4px 0px; height: 20px; background: green; text-align: center; vertical-align: middle;">
<input class="button2" type="button" value="BAIXA-TE'L"
onClick="javascript:window.location = '<?php echo $arxiu_dir; ?>'">
</p>
<p class='cos2'>Recorda: Joc de caracters Unicode(UTF8) i Separat per PUNT I COMA.</p>
</div>
<div class="contenidor_fac" style="padding-bottom: 20px;">
<p class="cos" style="white-space: -moz-pre-wrap; word-wrap: break-word;"><?php echo $content; ?></p>
</div>
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
