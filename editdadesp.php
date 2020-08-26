<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];
    $superuser = strtoupper($_SESSION['user']);

    $nom = $_GET['id'];

    $modcdp = '';
    $hidden = "";
    $hidden2 = "";
    $hidden3 = "";
    $new = "";

    if ($nom == "" OR $nom == $user) {
        $nom = $user;
        $link = ">><a href='families.php'>llistat famílies</a>";
        $modcdp = '<button class="button button--animated"  onclick="javascript:window.location = \'editpass.php\';">Modificar contraseña <i class="fa fa-lock" aria-hidden="true"></i></button>';
    } else {
        $link = ">><a href='editfamilies3.php'>crear y editar socios/as</a>";
    }

    $supernom = strtoupper($nom);

    $p_tip = $_POST['tipus'];
    $p_tip2 = $_POST['tip2'];
    $p_dia = $_POST['dia'];
    $p_comp = $_POST['comp'];
    $p_tlf1 = $_POST['tlf1'];
    $p_tlf2 = $_POST['tlf2'];
    $p_email1 = $_POST['email1'];
    $p_email2 = $_POST['email2'];
    $p_nomf = $_POST['nomf'];
    $p_adressf = $_POST['adressf'];
    $p_niff = $_POST['niff'];
    $p_nota = $_POST['nota'];
    $p_kuota = $_POST['kuota'];
    $p_IBAN = $_POST['IBAN'];
    $p_domiciliacion = $_POST['domiciliacion'];
    $p_fechaalta = $_POST['fechaalta'];

    include('config/configuracio.php');

    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <title>aplicoop - editar familia</title>
    </head>

    <body>
    <?php include 'menu.php'; ?>
    <div class="page">
        <div class="container">


            <div class="u-cf">
                <h1 class="pull-left"> Editar familia <?php echo $supernom; ?> </h1>

                <div class="pull-right u-mt-1 u-mb-1">
                    <?php echo $modcdp; ?>
                </div>
            </div>



            <div class="box">
                <form action="editdadesp.php?id=<?php echo $nom; ?>" method="post" name="frmeditdadesp"
                      id="frmeditdadesp" class="form-horizontal">


                    <?php
                    if ($p_tip2 != "" OR $p_dia != "" OR $p_comp != "" OR $p_tip != "") {
                        $query2 = "UPDATE usuaris
	SET tipus='" . $p_tip . "', tipus2='" . $p_tip2 . "', dia='" . $p_dia . "', components='" . $p_comp . "',
	tel1='" . $p_tlf1 . "', tel2='" . $p_tlf2 . "', email1='" . $p_email1 . "', email2='" . $p_email2 . "',
	nomf='" . $p_nomf . "', adressf='" . $p_adressf . "', niff='" . $p_niff . "',
	nota='" . $p_nota . "', kuota='" . $p_kuota . "', IBAN='" . $p_IBAN . "', domiciliacion='" . $p_domiciliacion . "', fechaalta='" . $p_fechaalta . "'
 	WHERE nom='" . $nom . "' ";

                        mysqli_query($conn,$query2) or die('Error, insert query2 failed');

                        echo "<p class='alert alert--info'>Los cambios de la familia " . $supernom . " se han guardado correctamente.</p>";

                    }
                    ?>

                    <?php

                    $select = "SELECT nom,tipus,tipus2,dia,components,tel1,tel2,email1,email2,nomf,adressf,niff,nota,kuota,IBAN,domiciliacion,fechaalta
FROM usuaris WHERE nom='$nom'";

                    $query = mysqli_query($conn,$select);

                    if (!$query) {
                        die('Invalid query: ' . mysqli_error($conn));
                    }

                    list($nom, $tip, $tip2, $dia, $comp, $tlf1, $tlf2, $email1, $email2, $nomf, $adressf, $niff, $nota, $kuota, $IBAN, $domiciliacion,$fechaalta) = mysqli_fetch_row($query);

                    if ($nom == "" OR $nom == $user) {
                        $hidden = '<input type="hidden" name="tip2" id="tip2" value="' . $tip2 . '">';
                        $hidden2 = '<input type="hidden" name="dia" id="dia" value="' . $dia . '">';
                        $hidden3 = '<input type="hidden" name="tipus" id="tipus" value="' . $tip . '">';
                        $new = "2";
                    }

                    ?>

                    <h2 class="box-subtitle u-text-center u-mb-1">Datos personales</h2>


                    <div class="form-group">
                        <label for="tip2<?php echo $new; ?>" class="col-sm-3 control-label">Activo/Baja</label>
                        <div class="col-sm-9">
                            <SELECT name="tip2<?php echo $new; ?>" id="tip2<?php echo $new; ?>" size="1"
                                    maxlength="5">

                                <?php
                                if ($tip2 == 'actiu') {
                                    $checked1 = 'selected';
                                    $checked2 = "";
                                }
                                if ($tip2 == 'baixa') {
                                    $checked2 = 'selected';
                                    $checked1 = "";
                                }
                                ?>

                                <option value="actiu" <?php echo $checked1; ?>>activo</option>
                                <option value="baixa" <?php echo $checked2; ?>>baja</option>
                            </select>
                            <?php echo $hidden; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dia<?php echo $new; ?>" class="col-sm-3 control-label">Grupo</label>
                        <div class="col-sm-9">
                            <SELECT name="dia<?php echo $new; ?>" id="dia<?php echo $new; ?>">

                                <?php
                                $select3 = "SELECT nom FROM grups ORDER BY nom";
                                $query3 = mysqli_query($conn,$select3);
                                if (!$query3) {
                                    die('Invalid query3: ' . mysqli_error($conn));
                                }

                                while (list($sgrup) = mysqli_fetch_row($query3)) {
                                    if ($dia == $sgrup) {
                                        echo '<option value="' . $sgrup . '" selected>' . $sgrup . '</option>';
                                    } else {
                                        echo '<option value="' . $sgrup . '">' . $sgrup . '</option>';
                                    }
                                }
                                ?>

                            </select>
                            <?php echo $hidden2; ?>
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="tipus<?php echo $new; ?>" class="col-sm-3 control-label">Tipo de usuario
                            (permisos)</label>
                        <div class="col-sm-9">
                            <SELECT name="tipus<?php echo $new; ?>" id="tipus<?php echo $new; ?>" size="1"
                                    maxlength="10">
                                <?php
                                if ($tip == 'user') {
                                    $checked3 = 'selected';
                                    $checked4 = "";
                                    $checked5 = "";
                                    $checked6 = "";
                                    $checked7 = "";
                                    $checked8 = "";
                                } elseif ($tip == 'admin') {
                                    $checked3 = '';
                                    $checked4 = "selected";
                                    $checked5 = "";
                                    $checked6 = "";
                                    $checked7 = "";
                                    $checked8 = "";
                                } elseif ($tip == 'eco') {
                                    $checked3 = '';
                                    $checked4 = "";
                                    $checked5 = "selected";
                                    $checked6 = "";
                                    $checked7 = "";
                                    $checked8 = "";
                                } elseif ($tip == 'prov') {
                                    $checked3 = '';
                                    $checked4 = "";
                                    $checked5 = "";
                                    $checked6 = "selected";
                                    $checked7 = "";
                                    $checked8 = "";
                                } elseif ($tip == 'cist') {
                                    $checked3 = '';
                                    $checked4 = "";
                                    $checked5 = "";
                                    $checked6 = "";
                                    $checked7 = "selected";
                                    $checked8 = "";
                                } elseif ($tip == 'super') {
                                    $checked3 = '';
                                    $checked4 = "";
                                    $checked5 = "";
                                    $checked6 = "";
                                    $checked7 = "";
                                    $checked8 = "selected";
                                }
                                echo '
<option value="user" ' . $checked3 . '>user</option>
<option value="admin" ' . $checked4 . '>admin</option>
<option value="eco" ' . $checked5 . '>eco</option>
<option value="prov" ' . $checked6 . '>prov</option>
<option value="cist" ' . $checked7 . '>cist</option>
<option value="super" ' . $checked8 . '>super</option>';
                                ?>

                            </select>
                            <?php echo $hidden3; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="comp" class="col-sm-3 control-label">Componentes de la familia</label>
                        <div class="col-sm-9">
                            <input type="text" id="comp" name="comp" value="<?php echo $comp; ?>" maxlength="100">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tlf1" class="col-sm-3 control-label">Teléfono</label>
                        <div class="col-sm-9">
                            <input type="tel" id="tlf1" name="tlf1" value="<?php echo $tlf1; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tlf2" class="col-sm-3 control-label">Teléfono 2</label>
                        <div class="col-sm-9">
                            <input type="tel" id="tlf2" name="tlf2" value="<?php echo $tlf2; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email1" class="col-sm-3 control-label">E-mail</label>
                        <div class="col-sm-9">
                            <input type="email" id="email1" name="email1" value="<?php echo $email1; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email2" class="col-sm-3 control-label">E-mail 2</label>
                        <div class="col-sm-9">
                            <input type="email" id="email2" name="email2" value="<?php echo $email2; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nota" class="col-sm-3 control-label">Comentarios</label>
                        <div class="col-sm-9">
                            <textarea name="nota" cols="35" rows="4" id="nota"><?php echo $nota; ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="kuota" class="col-sm-3 control-label">Kuota</label>
                        <div class="col-sm-9">
                            <input type="number" name="kuota" min="0" max="10" step="0.01" value="<?php echo $kuota; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="IBAN" class="col-sm-3 control-label">IBAN</label>
                        <div class="col-sm-9">
                            <input type="text" id="IBAN" name="IBAN" value="<?php echo $IBAN; ?>" size="24" maxlength="24">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="domiciliacion" class="col-sm-3 control-label">Domiciliacion</label>
                        <div class="col-sm-9">
                            <select name="domiciliacion" id="domiciliacion">
                                <option value="1" <?php if ($domiciliacion == 1) {echo "selected";} ?>>Si</option>
                                <option value="0" <?php if ($domiciliacion == 0) {echo "selected";} ?>>No</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="fechaalta" class="col-sm-3 control-label">Fecha de Alta</label>
                        <div class="col-sm-9">
                            <input type="text" name="fechaalta" value="<?php echo $fechaalta; ?>">
                        </div>
                    </div>
                    <hr class="box-separator"/>

                    <h2 class="box-subtitle u-text-center  u-mb-1">Factura</h2>

                    <div class="form-group">
                        <label for="nomf" class="col-sm-3 control-label">Nombre</label>
                        <div class="col-sm-9">
                            <input type="text" id="nomf" name="nomf" value="<?php echo $nomf; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="adressf" class="col-sm-3 control-label">Dirección</label>
                        <div class="col-sm-9">
                            <input type="text" id="adressf" name="adressf" value="<?php echo $adressf; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="niff" class="col-sm-3 control-label">NIF</label>
                        <div class="col-sm-9">
                            <input type="text" id="niff" name="niff" value="<?php echo $niff; ?>">
                        </div>
                    </div>



                    <div class="u-text-center u-mt-1">
                        <button class="button button--animated button--save" type="submit">Guardar <i
                                class="fa fa-floppy-o" aria-hidden="true"></i></button>
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
