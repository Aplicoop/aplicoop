<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];
    $superuser = strtoupper($_SESSION['user']);

    $click = $_POST['click'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    include 'config/configuracio.php';

    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <title>aplicoop - editar contraseña</title>
    </head>

    <body>
    <?php include 'menu.php'; ?>
    <div class="page">
        <div class="container">


            <h1>
                Modificar contraseña familia <?php echo $superuser; ?>
            </h1>



            <div class="box">

                <?php
                if (isset($click) and $click == "change-password") {
                    $password = mysqli_real_escape_string($password);

                    //Setting flags for checking
                    $status = "OK";
                    $msg = "";

                    if ($password == "") {
                        echo "<p class='alert alert--error'>La contraseña no puede estar vacia.</p>";
                    } else {
                        if (strlen($password) > 10) {
                            echo "<p  class='alert alert--error'>La contraseña ha de ser inferior a 10 dígitos.</p>";
                        } else {
                            if ($password <> $password2) {
                                echo "<p  class='alert alert--error'>Las contraseñas no coinciden.</p>";
                            } else {
                                // if all validations are passed.
                                $md5pass = md5($password);
                                $query2 = "update usuaris set claudepas='$md5pass' where nom='$user'";
                                mysqli_query($conn,$query2) or die('Error, insert query2 failed');
                                echo "<p  class='alert alert--info'>La contraseña ha sido cambiada correctamente.</p>";
                            }
                        }
                    }
                }
                ?>

                <form action="editpass.php" method="post" name="frmeditpass" id="frmeditpass"  class="form-horizontal">
                    <input type=hidden name=click value=change-password>


                    <div class="form-group">
                        <label for="pass" class="col-sm-3 control-label">Contraseña</label>
                        <div class="col-sm-9">
                            <input type='password' id="pass" name='password'>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pass2" class="col-sm-3 control-label">Repetir contraseña</label>
                        <div class="col-sm-9">
                            <input type='password' id="pass2" name='password2'>
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
