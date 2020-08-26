<!--
Apartado economia Kidekoop. Este script esta diseniado para la gestion de economia de Kidekoop
kidekoop.org

Autor: Leonidas Ioannidis
Contacto: info@kidekoop.org
-->


<?php
session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];
    $pactiu = $_POST['actiu'];
    $where = '';
    print_r($query);
    include 'config/configuracio.php';

    $sel = "SELECT tipus FROM usuaris WHERE nom='$user'";
    $query = mysqli_query($conn,$sel) or die ('query failed: ' . mysqli_error($conn));
    list($priv) = mysqli_fetch_row($query);

///sólo entramos si somos "super"////

        if ($priv == 'super') {

        ?>

        <html lang="es">
        <head>
            <?php include 'head.php'; ?>
            <title>Balances de Soci@s</title>
        </head>

        <body>
        <?php include 'menu.php'; ?>
        <div class="page">
        	<div class="container">
                <h1>Balances de Soci@s</h1>
                <p>Hoy <span style="font-weight: bold;"><?php echo date("Y/m/d"); ?> </span>los balances de las socias de kidekoop son :</p>
                <form action="balances_socios.php" method="post">
                    <select name="actiu" id="actiu" size="1" maxlength="5" onChange="this.form.submit()">
                        <option value="">Tots</option>
                        <option value="actiu" <?php if ($pactiu == 'actiu') {echo 'selected';} ?>>Activos</option>
                        <option value="baixa" <?php if ($pactiu == 'baixa') {echo 'selected';} ?>>Baja</option>
                    </select>
                </form>
                <table class="table table-condensed table-striped" >
                    <tr>
                        <td width="25%" class="u-text-semibold">Soci@</td>
                        <td width="45%" class="u-text-semibold">Nombre</td>
                        <td width="30%" class="u-text-semibold u-text-right">Monedero</td>
                    </tr>
                    <?php
                    if ($pactiu == 'actiu') {
                        $where = "WHERE usuaris.tipus2 = 'actiu'";
                    }
                    elseif ($pactiu == 'baixa') {
                        $where = 'WHERE usuaris.tipus2 = "baixa"';
                    }
                    $query = "SELECT familia, usuaris.components, SUM(valor) as total FROM moneder JOIN usuaris ON usuaris.nom = moneder.familia ". $where ." GROUP BY familia ORDER BY total";
                    $result = mysqli_query($conn,$query);
                    if (!$result) {
                        die('Invalid query: ' . mysqli_error($conn));
                    }
                    while (list($socio, $nom, $total) = mysqli_fetch_row($result)) {
                     ?>
                     <tr>
                        <td><?php echo $socio; ?></td>
                        <td><?php echo $nom; ?></td>
                        <td class="u-text-right"><?php echo $total; ?></td>
                    </tr>
                    <?php
                    }
                    print ('</table>');
                    ?>
                </table>
            </div>
        </body>
        </html>
    <?php
        include 'config/disconect.php';
    } else {
        header("Location: escriptori2.php");
    }
} else {
    header("Location: index.php");
}
?>
