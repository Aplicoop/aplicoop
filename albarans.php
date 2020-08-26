<?php
// albaranes
session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];

    $pprov = $_POST['prov'];
    $pdatas = $_POST['datas'];
    $pdatai = $_POST['datai'];

    $gcont = $_GET['id'];

    include 'config/configuracio.php';
    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <link rel="stylesheet" type="text/css" href="coope.css"/>
        <title>albarans ::: la coope</title>

        <!-- calendar stylesheet -->
        <link rel="stylesheet" type="text/css" media="all" href="calendar/calendar-win2k-1.css" title="win2k-1"/>

        <!-- main calendar program -->
        <script type="text/javascript" src="calendar/calendar.js"></script>

        <!-- language for the calendar -->
        <script type="text/javascript" src="calendar/lang/calendar-cat.js"></script>

        <!-- the following script defines the Calendar.setup helper function, which makes
             adding a calendar a matter of 1 or 2 lines of code. -->
        <script type="text/javascript" src="calendar/calendar-setup.js"></script>
    </head>

    <style type="text/css">
        a#color:link, a#color:active {
            color: black;
        }

        a#color:hover {
            color: green;
            border: 1px solid #9cff00;
            -moz-border-radius: 10%;
        }

        a#color:visited {
            color: green;
        }
    </style>

    <body>
    <?php include 'menu.php'; ?>
    <div class="pagina" style="margin-top: 10px;">
        <div class="contenidor_1" style="border: 1px solid #990000;">
            <p class='path'>
                ><a href='admint.php'>administració</a>
                >><a href='albarans.php'>llistat d'albarans</a>
            </p>
            <p class="h1" style="background: #990000; text-align: left; padding-left: 20px;">
                Llistat d'albarans
<span style="display: inline; float: right; text-align: center; vertical-align: middle;
padding: 2px 50px 2px 0px;">
<input class="button2" style="width: 150px;" type="button" value="CREAR NOU ALBARÀ"
       onClick="javascript:window.location = 'create_alb.php'">
</span>
            </p>

            <table width="80%" align="center">
                <form action="albarans.php" method="post" name="prod" id="prod">
                    <tr style="padding-top: 10px;">
                        <td width="33%" align="center" class="cos_majus">Proveidores</td>
                        <td width="33%" align="center" class="cos_majus">Superior a la data</td>
                        <td width="33%" align="center" class="cos_majus">Inferior a la data</td>
                    </tr>

                    <tr style="padding-bottom: 10px;">
                        <td align="center">
                            <SELECT name="prov" id="prov" size="1" maxlength="30" onChange="this.form.submit()">
                                <option value="">elegeix una proveïdora</option>
                                <?php
                                $select3 = "SELECT nom FROM proveidores ORDER BY nom";
                                $query3 = mysqli_query($conn,$select3);
                                if (!$query3) {
                                    die('Invalid query3: ' . mysqli_error($conn));
                                }

                                while (list($sprov) = mysqli_fetch_row($query3)) {
                                    if ($pprov == $sprov) {
                                        echo '<option value="' . $sprov . '" selected>' . $sprov . '</option>';
                                    } else {
                                        echo '<option value="' . $sprov . '">' . $sprov . '</option>';
                                    }

                                }
                                ?>
                        </td>

                        <td align="center">
                            <input type="text" value="<?php echo $pdatas; ?>" name="datas" id="f_date_a" size="8"
                                   maxlength="10" readonly/>
                            <button type="text" name="budi" id="f_trigger_a">...</button>
                            <button type="submit" name="okds" id="okds">ok</button>
                            <script type="text/javascript">
                                Calendar.setup({
                                    inputField: "f_date_a",     // id of the input field
                                    ifFormat: "%d/%m/%Y",     // format of the input field (even if hidden, this format will be honored)
                                    button: "f_trigger_a",  // trigger for the calendar (button ID)
                                    singleClick: true
                                });
                            </script>
                        </td>

                        <td align="center">
                            <input type="text" value="<?php echo $pdatai; ?>" name="datai" id="f_date_b" size="8"
                                   maxlength="10" readonly/>
                            <button type="text" name="budf" id="f_trigger_b">...</button>
                            <button type="submit" name="okdi" id="okdi">ok</button>
                            <script type="text/javascript">
                                Calendar.setup({
                                    inputField: "f_date_b",     // id of the input field
                                    ifFormat: "%d/%m/%Y",     // format of the input field (even if hidden, this format will be honored)
                                    button: "f_trigger_b",  // trigger for the calendar (button ID)
                                    singleClick: true
                                });
                            </script>
                        </td>

                </form>
                </tr></table>

            <div class="contenidor_fac" style="border: 0px solid green; padding-bottom: 20px;">


                <?php
                if ($pprov != "" OR $pdatas != "" OR $pdatai != "") {
                    $datas2 = explode("/", $pdatas);
                    $datai2 = explode("/", $pdatai);
                    $datasup = $datas2[2] . "-" . $datas2[1] . "-" . $datas2[0];
                    $datainf = $datai2[2] . "-" . $datai2[1] . "-" . $datai2[0];

                    if ($pprov != "" AND $pdatas == "" AND $pdatai == "") {
                        $where = "WHERE proveidora='" . $pprov . "'";
                        $title = "Recerca per proveïdora " . $pprov;
                    } elseif ($pprov != "" AND $pdatas != "" AND $pdatai == "") {
                        $where = "WHERE proveidora='" . $pprov . "' AND data>='" . $datasup . "'";
                        $title = "Recerca per proveïdora " . $pprov . " i per data superior a " . $pdatas;
                    } elseif ($pprov != "" AND $pdatas == "" AND $pdatai != "") {
                        $where = "WHERE proveidora='" . $pprov . "' AND data<='" . $datainf . "'";
                        $title = "Recerca per proveïdora " . $pprov . " i per data inferior a " . $pdatai;
                    } elseif ($pprov != "" AND $pdatas != "" AND $pdatai != "") {
                        $where = "WHERE proveidora='" . $pprov . "' AND  data>='" . $datasup . "' AND data<='" . $datainf . "'";
                        $title = "Recerca per proveïdora " . $pprov . " per data entre " . $pdatas . " i " . $pdatai;
                    } elseif ($pprov == "" AND $pdatas != "" AND $pdatai == "") {
                        $where = "WHERE data>='" . $datasup . "'";
                        $title = "Recerca per data superior a " . $pdatas;
                    } elseif ($pprov == "" AND $pdatas != "" AND $pdatai != "") {
                        $where = "WHERE data>='" . $datasup . "' AND data<='" . $datainf . "'";
                        $title = "Recerca per data entre " . $pdatas . " i " . $pdatai;
                    } elseif ($pprov == "" AND $pdatas == "" AND $pdatai != "") {
                        $where = "WHERE data<='" . $datainf . "'";
                        $title = "Recerca er data inferior a " . $pdatai;
                    }
                } else {
                    $where = "";
                    $title = "Ordernació per numero d'albarà descendent";
                }

                print ('<p class="h1"
		style="background: #990000; font-size:14px; text-align: left;
		height: 20px; padding-left: 20px;">' . $title . '</p>');

                print('<table width="100%" align="center" cellspading="5" cellspacing="5">');

                $sel = "SELECT numero FROM albara " . $where;
                $result = mysqli_query($conn,$sel);
                if (!$result) {
                    die('Invalid quer2: ' . mysqli_error($conn));
                }
                $rnum = mysqli_num_rows($result);

                if (!$gcont) {
                    $cont = 20;
                } else {
                    $cont = $gcont;
                }

                $sel2 = "SELECT numero,proveidora,data FROM albara " . $where . "
	ORDER BY numero DESC LIMIT " . $cont;
                $result2 = mysqli_query($conn,$sel2);
                if (!$result2) {
                    die('Invalid query2: ' . mysqli_error($conn));
                }

                $i = 0;
                $k = 0;
                while (list($numero, $nomprov, $data) = mysqli_fetch_row($result2)) {
                    $data2 = explode("-", $data);
                    $datavis = $data2[2] . '/' . $data2[1] . '/' . $data2[0];
                    if ($i == 0) {
                        print ('<tr>');
                    }
                    print('<td class="cos" width="33%" align="center"><a id="color" href="edit_alb.php?id=' . $numero . '" class="Estilo4">' . $numero . ' - ' . $nomprov . ' - ' . $datavis . '</a></td>');
                    $i++;
                    $k++;
                    if ($i == 3) {
                        print ('</tr>');
                        $i = 0;
                    }
                }

                print ('</table></div></div>');

                if ($rnum > $cont) {
                    $id = $cont + 20;
                    echo '<p><input type="button" name="mes" value= "20+"
		onClick="javascript:window.location = \'albarans.php?id=' . $id . '\'"></p>';
                }
                ?>

                <p class="cos2" style="clear: both; text-align: center;">Per crear un nou albarà clica sobre el botó.
                    Per fer una recerca fes servir els quadres de proveïdora i data superior o inferior.
                    Per editar o eliminar un albarà clicka sobre el seu numero i t'apareixerà la seva fitxa</p>

            </div>
    </body>
    </html>


    <?php
    include 'config/disconect.php';
} else {
    header("Location: index.php");
}
?>
