<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {
    $user = $_SESSION['user'];
    $_SESSION['codi_cistella'] = 'off';

    $gcont = $_GET['id'];
    $gdata = $_GET['id2'];
    $gproces = $_GET['id3'];
    $ggrup = $_GET['id4'];

    list($gdia, $gmes, $gany) = explode("-", $gdata);
    $gbd_data = $gany . '-' . $gmes . '-' . $gdia;

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

    $errorMessage = '';
    if (isset($_POST['txtcodi']) && isset($_POST['data'])) {
        $pcodi = $_POST['txtcodi'];
        $pproces = $_POST['proces'];
        $pgrup = $_POST['grup'];
        $pbd_data = $_POST['data'];

        list($pany, $pmes, $pdia) = explode("-", $pbd_data);
        $pdata = $pdia . '-' . $pmes . '-' . $pany;

        if ($pcodi == "") {
            $errorMessage = "Has de introducir la clave";
        } else {
            $sql = "SELECT codi FROM cistella_check
			WHERE codi = '$pcodi'
			AND proces='$pproces' AND grup='$pgrup' AND data ='$pbd_data'";
            $result = mysqli_query($conn,$sql) or die('Query failed. ' . mysqli_error($conn));
            if (mysqli_num_rows($result) == 1) {
                $_SESSION['codi_cistella'] = 'in';
                ?>

                <SCRIPT LANGUAGE="javascript">
                    <!--
                    window.location = 'cistelles.php?id2=<?php echo $pdata . "&id3=" . $pproces . "&id4=" . $pgrup . "&id5=1"; ?>';
                    -->
                </SCRIPT>

                <?php
                exit;
            } else {
                $errorMessage = "Lo sentimos, la clave de edición no es correcta. Prueba otra vez..";
                include 'config/disconect.php';
            }
        }
        $nota = "";
        if ($errorMessage != '') {
            $nota = "<p class='alert alert--error'>" . $errorMessage . "</p>";
        }
    }

    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <title>aplicoop - grupos de pedidos y cestas</title>

    </head>

    <body>
    <?php include 'menu.php'; ?>
    <div class="page">

        <div class="container">

            <h1>Grupos de pedidos y cestas</h1>

            <?php echo $nota; ?>

            <form action="" method="post" name="frmLogin" id="frmLogin" class="box">
                <div class="table-responsive">


                <table class="table table-striped">
                <?php
                include 'config/configuracio.php';

                ////////////////////////////////////////////////////////
                // si s'envia una data concreta mira si te el check1 //
                ///////////////////////////////////////////////////////
                if ($gdata != "")
                {
                    $sql = "SELECT check1 FROM cistella_check
		WHERE proces='$gproces' AND grup='$ggrup' AND data ='$gbd_data'";
                    $result = mysqli_query($conn,$sql) or die('Query failed. ' . mysqli_error($conn));
                    list($check) = mysqli_fetch_row($result);

//////////////////////////////////////////////////////////////////////////////////
// si el proces te check1=1 (ja s'han fet les cistelles) llavors demana el codi //
//////////////////////////////////////////////////////////////////////////////////

                    if ($check == "1") {
                        echo '<thead>';
                        echo '<tr class="cos_majus" align="center">';
                        echo "<td>Proceso - Grupo</td>";
                        echo "<td>Fecha</td>";
                        echo "<td>Código</td>";
                        echo "</tr>";
                        echo "</thead>";
                        ?>
                        <tbody>
                        <tr>
                            <td align="center" class='cos'><?php echo $gproces . "-" . $ggrup; ?></td>
                            <input type=hidden name="proces" id="proces" value="<?php echo $gproces; ?>">
                            <input type=hidden name="grup" id="grup" value="<?php echo $ggrup; ?>">
                            <td align="center" class='cos'><?php echo $gdata; ?></td>
                            <input type=hidden name="data" id="data" value="<?php echo $gbd_data; ?>">
                            <td align="center"><input name="txtcodi" type="text" maxlength="7" size="5" id="txtcodi"
                                                      value=""></td>
                        </tr>
                        </tbody>
                        </table>

                        <div class="u-text-center">
                            <button class="button button--animated" name="submit" type="submit" value="ACCEPTAR">Aceptar</button>
                        </div>

                        <?php
                    }

/////////////////////////////////////////////////////////////////
// si el proces te check1=0 passa directament a fer la cistella //
/////////////////////////////////////////////////////////////////
                    else {
                        $_SESSION['codi_cistella'] = 'in';
                        echo '<META HTTP-EQUIV="Refresh" Content="0;
     		URL=cistelles.php?id2=' . $gdata . '&id3=' . $gproces . '&id4=' . $ggrup . '&id5=1">';
                        exit;
                    }
                }


                ///////////////////////////////////////////////////////////////////////////////////
                // si no s'envia una data concreta fa el llistat normal de processos i cistelles //
                ///////////////////////////////////////////////////////////////////////////////////
                else
                {
                echo "<thead>";
                echo "<tr><td class='u-text-semibold u-text-center'>Proceso - Grupp</td>";
                echo "<td class='u-text-semibold u-text-center'>Fecha</td>";
                echo "<td class='u-text-semibold u-text-center'>Pedido proveedores<br/>(sin incluir el stock)</td>";
                echo "<td class='u-text-semibold u-text-center'>Ver totales pedido<br/>(incluye el stock)</td>";
                echo "<td class='u-text-semibold u-text-center'>Ver pedido</td>";
                echo "<td class='u-text-semibold u-text-center'>Editar pedido</td>";
                echo "<td class='u-text-semibold u-text-center'>Pago proveedores</td>";
                echo "</tr>";
                echo "</thead>";

                $taula = "SELECT proces, grup, data
		FROM comanda
		GROUP BY proces, grup, data
		ORDER BY data DESC";
                $result = mysqli_query($conn,$taula);
                if (!$result) {
                    die('Invalid query: ' . mysqli_error($conn));
                }
                $rnum = mysqli_num_rows($result);

                if (!$gcont) {
                    $cont = 20;
                } else {
                    $cont = $gcont;
                }

                $taula2 = "SELECT proces, grup, data
		FROM comanda
		GROUP BY proces, grup, data
		ORDER BY data DESC
		LIMIT " . $cont;
                $result2 = mysqli_query($conn,$taula2);
                if (!$result2) {
                    die('Invalid query2: ' . mysqli_error($conn));
                }

                while (list($proces, $grup, $bd_data) = mysqli_fetch_row($result2)) {
                    //$eco_column="";
                    //if ($user=="economia")
                    //{
                    //	$taula2 = "SELECT codi FROM cistella_check WHERE data='$periode'";
                    //	$result2 = mysqli_query($conn,$taula2);
                    //	if (!$result2) {die('Invalid query2: ' . mysqli_error($conn));}
                    //	list($eco_codi)=mysqli_fetch_row($result2);
                    //	$eco_column="<td align='center' class='Estilo1'>".$eco_codi."</td>";
                    //	}

                    $data = date("d-m-Y", strtotime($bd_data));
                    ?>

                    <tr>
                        <td align="center" class='cos'><?php echo $proces . "-" . $grup; ?></td>
                        <td align="center" class='cos'><?php echo $data; ?></td>
                        <td align="center" class='cos'><a  class="link"
                                href='totalcomanda.php?id=<?php echo $data . "&id2=" . $proces . "&id3=" . $grup; ?>&id4=0'>CP</a>
                        </td>
                        <td align="center" class='cos'><a  class="link"
                                href='totalcomanda.php?id=<?php echo $data . "&id2=" . $proces . "&id3=" . $grup; ?>&id4=1'>VT</a>
                        </td>

                        <?php
                        $taula3 = "SELECT check1
			FROM cistella_check
			WHERE proces='$proces' AND grup='$grup' AND data='$bd_data'";
                        $result3 = mysqli_query($conn,$taula3);
                        if (!$result3) {
                            die('Invalid query3: ' . mysqli_error($conn));
                        }

                        list($check) = mysqli_fetch_row($result3);
                        if ($check == 1) {
                            $vis_cist = "<a href='cistelles.php?id2=" . $data . "&id3=" . $proces . "&id4=" . $grup . "&id5=0'  class=\"link\">VC</a>";
                        } else {
                            $vis_cist = "";
                        }

                        ?>
                        <td align="center" class='cos'><?php echo $vis_cist; ?></td>
                        <td align="center" class='cos'><a class="link"
                                href='grups_comandes.php?id2=<?php echo $data . "&id3=" . $proces . "&id4=" . $grup; ?>'>E</a>
                        </td>
                        <td align="center" class='cos'><a  class="link"
                                href='totalfactura.php?id=<?php echo $data . "&id2=" . $proces . "&id3=" . $grup; ?>'>P</a>
                        </td>
                    </tr>

                    <?php
                    $i++;
                }
                echo '</table></div>';

                if ($rnum > $cont) {
                    $id = $cont + 20;
                    ?>

                    <div class="u-text-center">
                        <button name="mes" type="button" class="button button--animated"
                               onClick="javascript:window.location = 'grups_comandes.php?id=<?php echo $id; ?>';">+20</button>
                    </div>

                    <?php
                }
                ?>
            </form>
        </div>
    </div>
    </body>
    </html>

    <?php
    include 'config/disconect.php';
}
} else {
    header("Location: index.php");
}
?>
