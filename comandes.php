<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];
    $superuser = strtoupper($_SESSION['user']);
    $_SESSION['codi_cistella'] = 'in';

    $pfam = $_POST['fam'];
    $pdatas = $_POST['datas'];
    $pdatai = $_POST['datai'];

    $gcont = $_GET['id2'];
    $gfam = $_GET['id3'];
    $gpfam = $_GET['id4'];
    $gpdatas = $_GET['id5'];
    $gpdatai = $_GET['id6'];

    if ($gcont != "") {
        $pfam = $gpfam;
        $pdatas = $gpdatas;
        $pdatai = $gpdatai;
    }

    $superpfam = strtoupper($pfam);

    include 'config/configuracio.php';
    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <title>aplicoop - pedidos</title>
    </head>

    <body>
    <?php include 'menu.php'; ?>
    <div class="page">

        <?php
        if ($gfam != "") {
            $title1 = 'Mis Pedidos';
            $cap = 'Mis Pedidos';
            $cap_link = 'comandes.php?id3=' . $user;
            $pfam = $gfam;
        } else {
            $title1 = 'Listado de Pedidos';
            $cap = 'Listado de Pedidos';
            $cap_link = 'comandes.php';
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
                                <SELECT name="fam" id="fam" size="1" maxlength="30" onChange="this.form.submit()">
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
                        $where = "WHERE usuari='" . $pfam . "'";
                        $title = "B&uacute;squeda por Socio/a " . $superpfam;
                    } elseif ($pfam != "" AND $pdatas != "" AND $pdatai == "") {
                        $where = "WHERE usuari='" . $pfam . "' AND data>='" . $datasup . "'";
                        $title = "B&uacute;squeda por Socio/a" . $superpfam . " i per data superior a " . $pdatas;
                    } elseif ($pfam != "" AND $pdatas == "" AND $pdatai != "") {
                        $where = "WHERE usuari='" . $pfam . "' AND data<='" . $datainf . "'";
                        $title = "B&uacute;squeda por Socio/a" . $superpfam . " i per data inferior a " . $pdatai;
                    } elseif ($pfam != "" AND $pdatas != "" AND $pdatai != "") {
                        $where = "WHERE usuari='" . $pfam . "' AND  data>='" . $datasup . "' AND data<='" . $datainf . "'";
                        $title = "" . $superpfam . " per data entre " . $pdatas . " i " . $pdatai;
                    } elseif ($pfam == "" AND $pdatas != "" AND $pdatai == "") {
                        $where = "WHERE data>='" . $datasup . "'";
                        $title = "B&uacute;squeda por fecha superior a " . $pdatas;
                    } elseif ($pfam == "" AND $pdatas != "" AND $pdatai != "") {
                        $where = "WHERE data>='" . $datasup . "' AND data<='" . $datainf . "'";
                        $title = "Recerca per data entre " . $pdatas . " i " . $pdatai;
                    } elseif ($pfam == "" AND $pdatas == "" AND $pdatai != "") {
                        $where = "WHERE data<='" . $datainf . "'";
                        $title = "B&uacute;squeda por fecha inferior a " . $pdatai;
                    }
                } else {
                    $where = "";
                    $title = "Ordernado por número de comanda descendente";
                }

                print ('
        <div class="alert alert--info clearfix u-mb-1">
            <span>' . $title . '</span>
		</div>');


                print('
            <div class="table-responsive ">
                <table class="table table-condensed table-striped">
                    <tr>
                        <td style="font-weight: 600" width="5%">Nº</td>
                        <td style="font-weight: 600" width="20%">USUARIO</td>
                        <td style="font-weight: 600" width="20%">FECHA RECOGIDA / FIN PERIODO</td>
                        <td style="font-weight: 600" width="20%">FACTURA</td>
                        <td style="font-weight: 600" width="15%">FECHA CESTA</td>
                        <td style="font-weight: 600" width="10%">VALIDO FAMILIA</td>
                        <td style="font-weight: 600" width="10%">VALIDO ECONOMIA</td>
                    </tr>
                 ');


                $sel = "SELECT numero FROM comanda " . $where;
                $result = mysqli_query($conn,$sel);
                if (!$result) {
                    die('Invalid query: ' . mysqli_error($conn));
                }
                $rnum = mysqli_num_rows($result);

                if (!$gcont) {
                    $cont = 50;
                } else {
                    $cont = $gcont;
                }

                $ordre = 'DESC';

                $sel2 = "SELECT numero,usuari,proces,grup,data,check0,report0,data2,check1,check2,notes
	FROM comanda " . $where . "
	ORDER BY numero " . $ordre . " LIMIT " . $cont;
                $result2 = mysqli_query($conn,$sel2);
                if (!$result2) {
                    die('Invalid query2: ' . mysqli_error($conn));
                }

                $k = 0;
                while (list($numero, $fam, $proces, $grup, $data, $check0, $report0, $data2, $check1, $check2, $notes) = mysqli_fetch_row($result2)) {
                    $datarc = explode("-", $data);
                    $datavis = $datarc[2] . '-' . $datarc[1] . '-' . $datarc[0];
                    $data_c = explode("-", $data2);
                    $data_c_vis = $data_c[2] . '-' . $data_c[1] . '-' . $data_c[0];
                    if ($data_c_vis == "00/00/0000") $data_c_vis = "";
                    print('
                <tr>
                    <td>
                        <a href="cmda2.php?id=' . $proces . '&id2=' . $numero . '&id4=vis" class="link link--highlight link--visitable">' . $numero . '</a>
                    </td>
                    <td>' . $fam . '</td>
                    <td>' . $datavis . '</td>
                   ');

                    $accept0 = "";
                    $accept1 = "";
                    $accept2 = "";
                    if ($check0 == 0) {
                        $accept0 = "Pendiente";
                    } else {
                        $accept0 = '<a href="factura.php?id=' . $numero . '"  class="link">ver <i class="fa fa-eye" aria-hidden="true"></i></a>';
                        if ($check1 == '0') {
                            if ($fam == $user) {
                                //$accept1="<a href='factura.php?id=".$numero."&id2=".$report0."&id3=1'>validar</a>";
                            } else {
                                //$accept1="Pendent";
                            }
                        } else {
                            $accept1 = "<i class=\"fa fa-check-circle\" aria-hidden=\"true\"></i>";
                            if ($check2 == '0') {
                                $accept2 = "Pendiente";
                            } else {
                                $accept2 = "<i class=\"fa fa-check-circle\" aria-hidden=\"true\"></i>";
                            }
                        }
                    }
                    print('
                        <td>' . $accept0 . '</td>
                        <td>' . $data_c_vis . '</td>
                        <td>' . $accept1 . '</td>
                        <td>' . $accept2 . '</td>
                    </tr>
				    ');
                    $k++;
                }
                print ('</table></div>');

                if ($rnum > $cont) {
                    $id = $cont + 50;
                    echo '<div class="u-text-center"><button class="button button--animated" name="mes"
			onClick="javascript:window.location = \'comandes.php?id2=' . $id . '&id4=' . $pfam . '&id5=' . $pdatas . '&id6=' . $pdatai . '\'">50+</button>
			</div>';
                }

                ?>
            </div>
    </body>
    </html>


    <?php
    include 'config/disconect.php';
} else {
    header("Location: index.php");
}
?>
