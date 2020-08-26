<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];
    $superuser = strtoupper($_SESSION['user']);

    $pfam = $_POST['fam'];
    $pdatas = $_POST['datas'];
    $pdatai = $_POST['datai'];

    $gcont = $_GET['id2'];
    $gfam = $_GET['id3'];
    $gpfam = $_GET['id4'];
    $gpdatas = $_GET['id5'];
    $gpdatai = $_GET['id6'];

    if ($gpfam != "") {
        $pfam = $gpfam;
    }
    if ($gcont != "") {
        $pdatas = $gpdatas;
        $pdatai = $gpdatai;
    }

    $superpfam = strtoupper($pfam);

    include 'config/configuracio.php';
    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <title>aplicoop - mis cuentas</title>

        <script type="text/javascript" src="calendar/calendar.js"></script>
        <script type="text/javascript" src="calendar/lang/calendar-es.js"></script>
        <script type="text/javascript" src="calendar/calendar-setup.js"></script>

    </head>

    <body>
    <?php include 'menu.php'; ?>
    <div class="page">

        <?php
        if ($gfam != "") {
            $title1 = 'Mis cuentas';
            $cap = 'Mis cuentas';
            $cap_link = 'comptes.php?id3=' . $user;
            $pfam = $gfam;
            $superpfam = strtoupper($gfam);

            //calcula realmente el total del moneder
            $select = "SELECT SUM(valor) AS total FROM moneder WHERE familia='" . $gfam . "'";
            $query = mysqli_query($conn,$select);
            if (!$query) {
                die('Invalid query: ' . mysqli_error($conn));
            }
            list($mone) = mysqli_fetch_row($query);
            $monea = "Monedero actual: " . $mone . "&#8364;";
        } else {
            $title1 = 'Listado de movimientos';
            $cap = 'Listado de movimientos';
            $cap_link = 'comptes.php';
        }
        ?>

        <div class="container">

            <h1><?php echo $title1; ?></h1>

            <form action="<?php echo $cap_link; ?>" method="post" name="prod" id="prod">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fam">Socio/a</label>
                            <div>
                                <?php
                                if ($gfam != "")
                                {
                                    ?>
                                    <input type="text" value="<?php echo $gfam; ?>" name="fam" id="fam" size="10"
                                           maxlength="30" readonly/>
                                    <?php
                                }
                                else
                                {
                                ?>
                                <select name="fam" id="fam" size="1" maxlength="30" onChange="this.form.submit()">
                                    <option value="">-- Seleccionar --</option>
                                    <?php
                                    $select3 = "SELECT nom FROM usuaris ORDER BY nom";
                                    $query3 = mysqli_query($conn,$select3);
                                    if (!$query3) {
                                        die('Invalid query3: ' . mysqli_error($conn));
                                    }
                                    while (list($sfam) = mysqli_fetch_row($query3)) {
                                        if ($pfam == $sfam) {
                                            echo '<option value="' . $sfam . '" selected>' . $sfam . '</option>';
                                            $select = "SELECT SUM(valor) AS total FROM moneder WHERE familia='" . $sfam . "'";
                                            $query = mysqli_query($conn,$select);
                                            if (!$query) {
                                                die('Invalid query: ' . mysqli_error($conn));
                                            }
                                            list($mone) = mysqli_fetch_row($query);
                                            $monea = "Monedero actual: " . $mone . "&#8364;";
                                        } else {
                                            echo '<option value="' . $sfam . '">' . $sfam . '</option>';
                                        }
                                    }
                                    echo '</select>';
                                    }
                                    ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="f_date_a">Superior a</label>
                            <input type="date" value="<?php echo $pdatas; ?>" name="datas" id="f_date_a"
                                   onChange="this.form.submit()" placeholder="dd/mm/aaaa"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="f_date_b">Inferior a</label>
                            <input type="date" value="<?php echo $pdatai; ?>" name="datai" id="f_date_b"
                                   onChange="this.form.submit()" placeholder="dd/mm/aaaa"/>
                        </div>
                    </div>
                </div>

            </form>

            <div class="box">

                <?php
                if ($pfam != "" OR $pdatas != "" OR $pdatai != "") {
                    $datas2 = explode("/", $pdatas);
                    $datai2 = explode("/", $pdatai);
                    $datasup = $datas2[0];// . "-" . $datas2[1] . "-" . $datas2[0];
                    $datainf = $datai2[0];// . "-" . $datai2[1] . "-" . $datai2[0];
                    if ($pfam != "" AND $pdatas == "" AND $pdatai == "") {
                        $where = "WHERE familia='" . $pfam . "'";
                        $title = "B&uacute;squeda por socio/a: " . $superpfam;
                    } elseif ($pfam != "" AND $pdatas != "" AND $pdatai == "") {
                        $where = "WHERE familia='" . $pfam . "' AND data>='" . $datasup . "'";
                        $title = "B&uacute;squeda por socio/a: " . $superpfam . " y por fecha superior a " . $pdatas;
                    } elseif ($pfam != "" AND $pdatas == "" AND $pdatai != "") {
                        $where = "WHERE familia='" . $pfam . "' AND data<='" . $datainf . "'";
                        $title = "B&uacute;squeda por socio/a: " . $superpfam . " y por fecha inferior a " . $pdatai;
                    } elseif ($pfam != "" AND $pdatas != "" AND $pdatai != "") {
                        $where = "WHERE familia='" . $pfam . "' AND  data>='" . $datasup . "' AND data<='" . $datainf . "'";
                        $title = "Búsqueda por família " . $superpfam . " por fecha entre " . $pdatas . " y " . $pdatai;
                    } elseif ($pfam == "" AND $pdatas != "" AND $pdatai == "") {
                        $where = "WHERE data>='" . $datasup . "'";
                        $title = "B&uacute;squeda por fecha superior a " . $pdatas;
                    } elseif ($pfam == "" AND $pdatas != "" AND $pdatai != "") {
                        $where = "WHERE data>='" . $datasup . "' AND data<='" . $datainf . "'";
                        $title = "B&uacute;squeda por fecha entre " . $pdatas . " y " . $pdatai;
                    } elseif ($pfam == "" AND $pdatas == "" AND $pdatai != "") {
                        $where = "WHERE data<='" . $datainf . "'";
                        $title = "B&uacute;squeda por fecha inferior a " . $pdatai;
                    }

                } else {
                    $where = "";
                    $title = "Movimientos ordenados por fecha descendente";
                }

                print ('
        <div class="alert alert--info clearfix u-mb-1">
            <span class="pull-left">' . $title . '</span>
            <span class="pull-right" style="text-align: right;">' . $monea . '	</span>
		</div>');

                print('<div class="table-responsive ">
        <table class="table table-condensed">
		<tr>
		<td style="font-weight: 600" width="20%">FECHA</td>
		<td style="font-weight: 600" width="20%">SOCIO/A</td>
		<td style="font-weight: 600" width="40%">CONCEPTO</td>
		<td style="font-weight: 600; text-align: right" width="20%">VALOR</td>');
                print('</tr>');

                $sel = "SELECT data FROM moneder " . $where;
                $result = mysqli_query($conn,$sel);
                if (!$result) {
                    die('Invalid query: ' . mysqli_error($conn));
                }
                $rnum = mysqli_num_rows($result);

                if (!$gcont) {
                    $cont = 100;
                } else {
                    $cont = $gcont;
                }

                $sel2 = "SELECT data,familia,concepte,valor	FROM moneder " . $where . "	ORDER BY data DESC LIMIT " . $cont;
                $result2 = mysqli_query($conn,$sel2);
                if (!$result2) {
                    die('Invalid query2: ' . mysqli_error($conn));
                }

                $k = 0;
                while (list($data, $fam, $concepte, $valor) = mysqli_fetch_row($result2)) {
                    $datarc = explode("-", $data);
                    $datavis = $datarc[2] . '-' . $datarc[1] . '-' . $datarc[0];
                    if ($valor > 0) {
                        $colin = "";
                    } else {
                        $colin = " class='danger' ";
                    }
                    print('<tr' . $colin . '>
				<td>' . $datavis . '</td>
				<td>' . $fam . '</td>
				<td>' . $concepte . '</td>
				<td style="text-align:right">' . $valor . '</td></tr>');
                    $k++;
                }

                echo "</table></div>";

                if ($rnum > $cont) {
                    $id = $cont + 100;
                    echo '<div class="u-text-center"><button class="button button--animated"  name="mes"
		onClick="javascript:window.location = \'comptes.php?id2=' . $id . '&id4=' . $pfam . '&id5=' . $pdatas . '&id6=' . $pdatai . '\'">100+</button></div>';
                }
                ?>
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
