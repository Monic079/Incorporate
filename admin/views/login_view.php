<?php
require_once "../models/login_model.php";
$model = new LoginModel();
$companies = $model->getCompanies();
?>

<!DOCTYPE html>
 <html lang="es">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incorpórate</title>
    <link rel="stylesheet" href="../assets/css/estilo_login.css">
 </head>
 <body>
      <div class="content">    
         <img src="../assets/img/icons/logo_inc.png" class="logo"> 

                     <!-- MENSAJE DE BIENVENIDA -->
        <div class="welcome-box">
            ¡Bienvenido a Incorpórate!
        </div>

            <!-- BOTONES -->
        <div id="containerButtons">
            <button id="btnLogin">Iniciar sesión</button>
        </div>

        <!-- LOGIN -->
        <form id="loginForm" class="hidden" method="POST" action="../controllers/login_controller.php">
            <input type="hidden" name="action" value="login">

            <h3>Iniciar sesión</h3>

            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Contraseña" required>

            <button type="submit">Entrar</button>
        </form>

        <!-- REGISTER -->

      </div>

<script src="../assets/js/login_view.js"></script>
<script>
    <?php if(isset($_GET['error'])): ?>

    <?php if($_GET['error']=="invalid"): ?>
        showNotification("Credenciales incorrectas", "error");
    <?php endif; ?>

    <?php if($_GET['error']=="not_admin"): ?>
        showNotification("No tienes acceso a esta sección", "error");
    <?php endif; ?>

<?php endif; ?>
</script>

    
 </body>
 </html>