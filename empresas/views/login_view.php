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
            <button id="btnRegister">Crear cuenta</button>
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
        <form id="registerForm" class="hidden" method="POST" action="../controllers/login_controller.php">
            <input type="hidden" name="action" value="register">

            <h3>Crear cuenta (Empresa)</h3>

            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Contraseña" required>

            <!-- EMPRESA -->
            <select name="company_id" id="companySelect" onchange="checkCompany()">
                <option value="">Seleccione empresa</option>
                <?php foreach($companies as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                <?php endforeach; ?>
                <option value="new">Otra (Agregar)</option>
            </select>

            <input type="text" name="new_company" id="newCompany" class="hidden" placeholder="Nueva empresa">

            <input type="text" name="contact_name" placeholder="Nombre contacto" required>
            <input type="text" name="contact_position" placeholder="Cargo" required>
            <input type="text" name="contact_phone" placeholder="Teléfono" required>

            <button type="submit">Crear cuenta</button>
        </form>
      </div>



    <script src="../assets/js/login_view.js"></script>
 </body>
 </html>