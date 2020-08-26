<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];

    include 'config/configuracio.php';

    $sel = "SELECT tipus FROM usuaris WHERE nom='$user'";
    $query = mysqli_query($conn,$sel) or die ('query failed: ' . mysqli_error($conn));
    list($priv) = mysqli_fetch_row($query);

///sólo entramos si somos "super"////

    if ($priv == 'super') {

        if ($priv != 'user') {
            $h1 = "";
        } else {
            $h1 = "href=''";
        }

        if ($priv == 'admin' OR $priv == 'super') {
            $h2 = "href='editfamilies3.php'";
        } else {
            $h2 = "href=''";
        }

        if ($priv == 'eco' OR $priv == 'super') {
            $h4 = "href='moneder_linia.php'";
            $h5 = "href='devolucions.php'";
        } else {
            $h4 = "href=''";
            $h5 = "href=''";
        }

        ?>

        <html lang="es">
        <head>
            <?php include 'head.php'; ?>
            <title>aplicoop - admin</title>
        </head>

        <body>
        <?php include 'menu.php'; ?>
        <div class="page">
            <div class="container">
                <h1>Administración</h1>

                <div class="box">

                    <div class="row">
                        <div class="col-md-4">
                            <h2 class="box-subtitle">Pedidos</h2>
                            <ul type="circle">
                                <li><a class="link" href='grups_comandes.php'>Grupos de pedidos y cestas</a></li>
                                <li><a class="link" href='comandes.php'>Lista de pedidos y facturas</a></li>
                                <li><a class="link" <?php echo $h5; ?>>Devoluciones y facturas fuera de proceso</a></li>
                            </ul>

                            <h2 class="box-subtitle">Socios/as-Familias</h2>
                            <ul type="circle">
                                <li><a class="link" href='families.php'>Lista de Socios/as</a></li>
                                <li><a class="link" <?php echo $h2; ?>>Crear y editar Socios/as</a></li>
                            </ul>

                            <h2 class="box-subtitle">Comunicaciones</h2>
                            <ul type="circle">
                                <li><a class="link" href='notes.php'>Introducir notas en el escritorio</a></li>
                                <li><a class="link" href='cistella_incidencia.php'>Comunicaci&oacute;n de incidencias</a>
                                </li>
                            </ul>

                            <h2 class="box-subtitle">Monedero</h2>
                            <ul type="circle">
                                <li><a class="link" <?php echo $h4; ?>>Introducir línea</a></li>
                                <li><a class="link" href="comptes.php">Historia de movimientos</a></li>
                                <li><a class="link" href="moneder_usuari.php">Lista monedero de socios/as</a></li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h2 class="box-subtitle">Procesos (de pedido)</h2>
                            <ul type="circle">
                                <li><a class="link" href='editprocessos.php'>Crear, editar, eliminar procesos (de
                                        pedido)</a></li>
                                <li><a class="link" href='associar.php'>Asociar procesos, grupos y categor&iacute;as</a>
                                </li>
                            </ul>
                            <h2 class="box-subtitle">Grupos</h2>
                            <ul type="circle">
                                <li><a class="link" href='editgrups.php'>Crear, editar, eliminar grupos</a></li>
                            </ul>
                            <h2 class="box-subtitle">Categor&iacute;as y subcategor&iacute;as</h2>
                            <ul type="circle">
                                <li><a class="link" href='categories.php'>Crear, editar, eliminar categor&iacute;as y
                                        subcategor&iacute;as</a></li>
                            </ul>
                            <h2 class="box-subtitle">Estad&iacute;stica</h2>
                            <ul type="circle">
                                <li><a class="link" href='estat_consum.php'>Estad&iacute;stica de consumo</a></li>
                                <li><a class="link" href='estat_iva.php'>Consumo IVA</a></li>
                                <li><a class="link" href='estat_iva_prov.php'>Consumo IVA (prov.)</a></li>
                            </ul>
                            <h2 class="box-subtitle">Econom&iacute;a Kidekoop</h2>
                            <ul type="circle">
                                <li><a class="link" href='kidekoop.php'>Cierre de Més</a></li>
                                <li><a class="link" href='balances_socios.php'>Balances de Socios</a></li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h2 class="box-subtitle">Productos</h2>
                            <ul type="circle">
                                <li><a class="link" href='baixa_productes.php'>Activar/desactivar productos</a></li>
                                <li><a class="link" href='productes.php'>Crear, editar, eliminar productos</a></li>
                                <li><a class="link" href='canvi_massiu_productes.php'>Cambiar precios, IVA y margen en
                                        listado</a></li>
                            </ul>
                            <h2 class="box-subtitle">Proveedores</h2>
                            <ul type="circle">
                                <li><a class="link" href='proveidores.php'>Crear, editar, eliminar proveedores</a></li>

                            </ul>
                            <h2 class="box-subtitle">Albaranes</h2>
                            <ul type="circle">
                                <li><a class="link" href='albarans.php'>Crear, editar, eliminar albaranes</a></li>
                            </ul>

                            <h2 class="box-subtitle">Stock</h2>
                            <ul type="circle">
                                <li><a class="link" href='inventari2.php'>Ver stock actual</a></li>
                            </ul>
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
        header("Location: escriptori2.php");
    }

} else {
    header("Location: index.php");
}
?>
