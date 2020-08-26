<?php
// we must never forget to start the session
session_start();

include 'config/configuracio.php';

$errorMessage = '';
if (isset($_POST['txtUserId']) && isset($_POST['txtPassword'])) {
    // first check if the number submitted is correct
    $number = $_POST['txtNumber'];

    if (md5($number) == $_SESSION['image_random_value']) {
        //include 'config/configuracio.php';

        $userId = $_POST['txtUserId'];
        $password = $_POST['txtPassword'];


        // check if the user id and password combination exist
        $sql = "SELECT nom FROM usuaris  WHERE nom = '$userId' AND claudepas ='" . md5($password) . "' AND tipus2='actiu'";

        $result = mysqli_query($conn,$sql) or
        die('Query failed. ' . mysqli_error($conn));

        if (mysqli_num_rows($result) == 1) {
            // the user id and password match,
            // check if the user is already active

            list ($nom) = mysqli_fetch_row($result);

            // set the session
            $_SESSION['image_is_logged_in'] = true;

            // remove the random value from session
            $_SESSION['image_random_value'] = '';

            // convert in minuscules
            $userId = strtolower($userId);

            // Identify user
            $_SESSION['user'] = $userId;

            //identify date
            date_default_timezone_set('Europe/Madrid');
            $_SESSION['timeinitse'] = time();
            $timeinitse = date("Y:m:d H:i:s", $_SESSION['timeinitse']);


            //keep number session
            $sql2 = "INSERT INTO session (user, date, date2)
          		 VALUES ('$userId', '$timeinitse', '$timeinitse')";
            mysqli_query($conn,$sql2) or
            die('Query2 failed. ' . mysqli_error($conn));
            $sessionid = mysqli_insert_id();
            $_SESSION['sessionid'] = $sessionid;

            // after login we move to the main page
            header('Location: main.php');
            exit;

        } else {
            $errorMessage = 'Lo sentimos, el usuario y/o password son err&oacute;neos. Prueba otra vez.';
            include 'config/disconect.php';
        }
    } else {
        $errorMessage = 'Lo sentimos, el captcha no es correcto. Prueba otra vez.';
    }
}

?>

<html lang="es">
<head>
    <title>aplicoop - login</title>
    <?php include 'head.php'; ?>
</head>

<body>
<div class="login-container page">

    <div class="u-text-center u-mb-3">
        <img class="img img--responsive" src="imatges/logo_menu.png" title="applicop">
        <h1 class="login-title">APLICOOP</h1>
    </div>

    <form action="" method="post" name="frmLogin" id="frmLogin">

        <div class="box">

            <div class="form-group">
                <label for="txtUserId">Usuario</label>
                <input type="text" id="txtUserId" name="txtUserId" class="form-control">
            </div>

            <div class="form-group">
                <label for="txtPassword">Password</label>
                <input type="password" id="txtPassword" name="txtPassword" class="form-control">
            </div>

            <div class="form-group">
                <label for="txtNumber">Captcha</label>
                <input type="number" id="txtNumber" name="txtNumber" class="form-control" autocomplete="off">
                <div class="u-text-center u-mt-1">
                    <img class="login-captcha" src="randomImage.php">
                </div>
            </div>

            <?php
            if ($errorMessage != '') {
                ?>
                <p class="alert alert--error"><?php echo $errorMessage; ?></p>
                <?php
            }
            ?>

            <div class="u-text-center">
                <button type="submit" id="btnLogin" class="button button--animated button--save" name="entrar">
                    Entrar
                </button>
            </div>

        </div>



    </form>

    <p class="u-text-center u-text-smaller u-mt-2">
        Si tienes cualquier problema notif&iacute;calo <a href="incidencia.php"  class="link link--highlight" target="_blank">aqu&iacute;</a>
    </p>

</div>
</body>
</html>
