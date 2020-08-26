<!DOCTYPE html>
<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];

    setlocale(LC_ALL, "CA");
    date_default_timezone_set("Europe/Madrid");

    $dayNames = array(
        'Monday' => 'Lunes',
        'Tuesday' => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday' => 'Jueves',
        'Friday' => 'Viernes',
        'Saturday' => 'Sabado',
        'Sunday' => 'Domingo',
    );

    function tradueixData2($d)
    {
        $angles = array("Monday",
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

        $catala = array("/Dilluns/",
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

// processos oberts//
    $sel = "SELECT dia FROM usuaris	WHERE nom='$user'";
    $result = mysqli_query($conn,$sel);
    if (!$result) {
        die('Invalid query: ' . mysqli_error($conn));
    }
    list ($grup) = mysqli_fetch_row($result);

    $sel2 = "SELECT nom, tipus, data_inici, data_fi, periode, dia_recollida, dia_tall, hora_tall
    FROM processos WHERE grup='$grup' AND actiu='actiu'";
    $result2 = mysqli_query($conn,$sel2);
    if (!$result2) {
        die('Invalid query2: ' . mysqli_error($conn));
    }

    while (list($proces, $tipus, $datai, $dataf, $periode, $diare, $diat, $horat) = mysqli_fetch_row($result2)) {
        $time_avui = time();
        list($yi, $mi, $di) = explode('-', $datai);
        $time_datai = mktime(0, 0, 0, $mi, $di, $yi);
        list($yf, $mf, $df) = explode('-', $dataf);
        $time_dataf = mktime(23, 59, 59, $mf, $df, $yf);

        if ($tipus == "període concret" AND $time_avui <= $time_dataf AND $time_avui >= $time_datai) {
            $ver_datai = date("d-m-Y", $time_datai);
            $ver_dataf = date("d-m-Y", $time_dataf);
            $bd_dataf = date("Y-m-d", $time_dataf);
            $sel3 = "SELECT numero	FROM comanda
            WHERE usuari='$user' AND proces='$proces' AND data<='$dataf' AND data>='$datai' ";
            $result3 = mysqli_query($conn,$sel3);
            if (!$result3) {
                die('Invalid query3: ' . mysqli_error($conn));
            }
            list ($numcmda1) = mysqli_fetch_row($result3);

            if ($numcmda1 != "") {
                $nota11 = $proces . ': tens la comanda numero ' . $numcmda1 . '
                <div><a class="button--save button--animated  button--save  u-mt-2 u-mb-1" href="cmda2.php?id=' . $proces . '&id2=' . $numcmda1 . '&id4=vis"
                title="Editar pedido">Editar <i class="fa fa-pencil" aria-hidden="true"></i></a></div> Finaliza el ' . $ver_dataf . ' (incluido).';
            } else {
                $nota11 = $proces . ': fins ' . $ver_dataf . ' (inclòs).
                <div><a class="button--save button--animated  button--save  u-mt-2 u-mb-1" href="cmda2.php?id=' . $proces . '&id4=create"
                title="Nuevo pedido">Nuevo pedido <i class="fa fa-plus-circle" aria-hidden="true"></i></a></div>';
            }
            $nota1 .= ' <div class="u-text-center">' . $nota11 . '</div> ';
        }

        if ($tipus == "continu" AND $periode == "setmanal") {
        ///Treiem l'hora i els minuts de l'hora de tall///
            $horat_0 = (int)substr($horat, 0, 2);
            $mint_0 = (int)substr($horat, 2, 2);
        ///Fem l'operació per convertir lhora i minuta de tall en minuts///
            $horat_calc = (60 * $horat_0) + $mint_0;
            $horat_verb = $horat_calc . " minutes";
        /// Traduim el dia de la setmana de tall a l'angles i agafem le stres primeres lletres///
            $diare_a = tradueixData2(ucfirst($diare));
            $diat_a = tradueixData2(ucfirst($diat));
            $diat_w3 = substr($diat_a, 0, 3);
        ///Si el dia de la setmana d'avui coincideix amb el dia de al setmana de tall///
        ///llavors mirem si falten hores i minuts per arribar al punt de tall///
            $diaw3_today = date('D');
            if ($diat_w3 == $diaw3_today) {
                $hora_ara = (int)date('G');
                $min_ara = (int)date('i');
                $ara = ($hora_ara * 60) + $min_ara;
            /// Si encara no ha és l'hora i minut del punt de tall ///
            /// llavors avui és la data de tall ///
                if ($horat_calc >= $ara) {
                    $diat_0 = mktime(0, 0, 0, date('m'), date('d'), date('y'));
                }
            /// altres possibilitats la data de tall és ///
            /// el proper dia de la setmana de tall///
                else {
                    $diat_0 = strtotime("next " . $diat_a);
                }
            } else {
                $diat_0 = strtotime("next " . $diat_a);
            }
        /// data exacta de tall és la data de tall més les hores i minuts de tall///
        /// tall superior ///
            $time_diats = strtotime("+ " . $horat_verb, $diat_0);
            $ver_diats = date("d-m-Y, H:i", $time_diats);
        /// tall inferior ///
            $diat_2 = strtotime("- 7 days", $time_diats);
            $time_diati = strtotime("+ 1 second", $diat_2);
            $ver_diati = date("d-m-Y H:i", $time_diati);
        /// data de recollida és el següent dia de la setmana de recollida de la data de tall superior//
            $time_diare = strtotime("next " . $diare_a, $diat_0);
            $bd_diare = date("Y-m-d", $time_diare);
            $ver_diare = date("d-m-Y", $time_diare);

            $sel3 = "SELECT numero
            FROM comanda
            WHERE usuari='$user' AND proces='$proces' AND data='$bd_diare'";
            $result3 = mysqli_query($conn,$sel3);
            if (!$result3) {
                die('Invalid query3: ' . mysqli_error($conn));
            }
            list ($numcmda1) = mysqli_fetch_row($result3);
            $date_difference = date_diff(date_create(date('Y-m-d')),date_create($ver_diare));

            if ($numcmda1 != "") {
                $nota11 = $proces . ': Tienes el pedido con número ' . $numcmda1 . ' para recoger el ' . $ver_diare . '.
                <div><a class="button button--save button--animated  button--save  u-mt-2 u-mb-1" href="cmda2.php?id=' . $proces . '&id2=' . $numcmda1 . '&id4=vis"
                title="Editar este pedido">Editar <i class="fa fa-pencil" aria-hidden="true"></i></a></div> Finaliza el ' . $ver_diats;
            } else {
                $select_para_contador = "SELECT COUNT(*) FROM comanda WHERE proces='$proces' AND data='$bd_diare'";
                $result_contador = mysqli_query($conn,$select_para_contador);
                list ($contador) = mysqli_fetch_row($result_contador);
                //printf($contador);

                $proces_name = explode(" ", $proces);
                $nota11 = $proces . ': abierto hasta el ' . $dayNames[date('l', strtotime($ver_diats))] .' '. $ver_diats . ' <span style="text-decoration:underline;">' . $contador . ' pedidos registrados.</span>' . '<div><a class="button button--animated button--save u-mt-2 u-mb-1" href="cmda2.php?id=' . $proces . '&id4=create" onclick="return confirm_alert(this,'.$date_difference->d.',\''.$proces_name[0].'\')";
                title="Nuevo pedido">Nuevo pedido <i class="fa fa-plus-circle" aria-hidden="true"></i></a></div>';
            }
            $nota1 .= ' <div class="u-text-center">' . $nota11 . '</div>';
        }
    }
//

// Actualització check1 despres 1 mes cistelles //
    $sel4 = "SELECT numero, data2	FROM comanda
    WHERE usuari='$user' AND check0='1' AND check1='0' AND data2!='0000-00-00'";
    $result4 = mysqli_query($conn,$sel4);
    if (!$result4) {
        die('Invalid query4: ' . mysqli_error($conn));
    }
    while (list ($numero, $data2) = mysqli_fetch_row($result4)) {
        list($year, $month, $day) = explode('-', $data2);
        $data2v = mktime(0, 0, 0, $month, $day, $year);
        $data_cl = strtotime('+ 1 month', $data2v);
        $ara = time();
        if ($ara > $data_cl) {
            $sel6 = "UPDATE comanda SET check1='1'	WHERE numero='$numero'";
            $result6 = mysqli_query($conn,$sel6) or die('Invalid query6: ' . mysqli_error($conn));
            $nota12 .= '<p class="cos_majus" style="color: grey; margin: 5px 10px 0px 10px;">
            La comanda numero ' . $numero . ' s\'ha validat automàticament.</p>';
        }
    }
//

//notes a l'escriptori
    $datacomp = date("Y-m-d");
    $sel2 = "SELECT * FROM notescrip WHERE caducitat>='$datacomp'";
    $result2 = mysqli_query($conn,$sel2);
    if (!$result2) {
        die('Invalid query result2: ' . mysqli_error($conn));
    }

    $nota13 = "";
    $nota21 = "";
    while (list($num, $nom, $text, $tipus, $caduc) = mysqli_fetch_row($result2)) {
        list($any, $mes, $dia) = explode("-", $caduc);
        $caduc2 = $dia . "-" . $mes . '-' . $any;

        if ($tipus == 'esquerra') {
            $nota13 .= '<p class="cos_majus" style="color: grey; margin: 10px auto; text-align: center;">' . $text . '</p>';
        } else {
            $nota21 .= '<p class="cos_majus" style="color: grey; margin: 10px auto; text-align: center;">' . $text . '</p>';
        }
    }
//

// moneder //
$sel6 = "SELECT SUM(valor) AS total FROM moneder WHERE familia='$user'"; //calcula realmente el total del moneder
$result6 = mysqli_query($conn,$sel6);
if (!$result6) {
    die('Invalid query6: ' . mysqli_error($conn));
}
list($moneder) = mysqli_fetch_row($result6);
$style = 'style="color: black;"';
if ($moneder <= 0) {
    $style = 'style="color: red;"';
}

//darrers moviments //
$sel7 = "SELECT data, concepte, valor FROM moneder WHERE familia='$user' ORDER BY data DESC LIMIT 5";
$result7 = mysqli_query($conn,$sel7);
if (!$result7) {
    die('Invalid query7: ' . mysqli_error($conn));
}
while (list($datam, $concepte, $valor) = mysqli_fetch_row($result7)) {
    $datam2 = explode('-', $datam);
    $datamov = $datam2[2] . '-' . $datam2[1] . '-' . $datam2[0];
    if ($valor > 0) {
        $colin = "style='color: blue;'";
    } else {
        $colin = "style='color: red;'";
    }
    $last .= "<tr><td align='center' width='35%'>" . $datamov . "</td>
    <td align='left' width='55%'>" . $concepte . "</td>
    <td align='right' width='10%' " . $colin . ">" . $valor . "</td></tr>";
}
//

// correus //

$sel5 = "SELECT * FROM incidencia
WHERE vist='0' AND (`to`='$user' OR `from`='$user')
ORDER BY data DESC
LIMIT 5";
$result5 = mysqli_query($conn,$sel5);
if (!$result5) {
    die('Invalid query5: ' . mysqli_error($conn));
}
while (list($from, $to, $sub, $tex, $datac, $vis) = mysqli_fetch_row($result5)) {

    $correu_linia .= '<div class="correu_f1"><SPAN style="font-weight: bold;"> Gaia: </span>' . $sub . '<span style="font-weight: bold;"> Data: </span>' . $datac . '</div>
    <div class="correu_f2">' . $tex . '</div>';
}

//


?>

<html lang="es">
<head>
    <title>aplicoop - escritorio</title>
    <?php include 'head.php'; ?>
    <script type="text/javascript">
        function confirm_alert(node,datediff,proces) {
            if(datediff > 7)
                return confirm("El pedido " + proces + " de esta semana ya esta cerrado. Si realizas tu pedido ahora se registrará para la semana siguiente. ¿Estás de acuerdo?");
        }
    </script>
</head>

<body>
    <?php include 'menu.php'; ?>
    <div class="page">

        <div class="container">
            <div class="desktop-wrapper">
                <div class="box">
                    <h2 class="box-title">Pedido</h2>
                    <div>
                        <?php echo $nota1; ?>
                        <?php echo $nota12; ?>
                        <?php echo $nota13; ?>
                    </div>
                </div>
                <div class="box">
                    <h2 class="box-title clearfix">Monedero <span
                        class="pull-right" <?php echo $style . ">** " . $moneder; ?> **</span></h2>

                        <h3 class="u-mb-1 u-mt-1">
                            &Uacute;ltimos movimientos contabilizados
                        </h3>
                        <table class="table table-striped"><?php echo $last; ?></table>

                        <?php echo $nota21; ?>

                    </div>
                    <div class="box">
                        <h2 class="box-title">Agenda</h2>
                        <div>
                            <iframe title="calendar_widget" src="<?php echo $gcal; ?>" style=" border-width:0 " width="400" height="300" frameborder="0" scrolling="no"></iframe>
                        </div>
                    </div>
                    <div class="box">
                        <h2 class="box-title">Correos</h2>
                        <div id="correu">
                            <?php echo $correu_linia; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>

    <?php

    include 'config/disconect.php';
} else {
    header("Location: index.php");
}
?>
