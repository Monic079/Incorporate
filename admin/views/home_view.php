
<?php
require_once "../controllers/AdminController.php";

$controller = new AdminController();
$data = $controller->getData();

$career_categories = $data['career_categories'];
$skill_categories = $data['skill_categories'];
?>

 <!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incorpórate Admin</title>
    <link rel="stylesheet" href="../assets/css/estilo_home.css">
 </head>
 <body>
      <div class="navbar">
         <img src="../assets/img/icons/logo_inc.png" alt="Logo">
         <a href="home_view.php"> <button class="active"> <img src="../assets/img/icons/home_b.png" alt="Home"> Home </button></a>
         <a href="feed_view.php"><button> <img src="../assets/img/icons/users_w.png" alt="Feed"> Feed </button>      </a>
         <a href="../controllers/AuthController.php?action=logout"><button> <img src="../assets/img/icons/exit.png" alt="Feed"> Salir </button></a>      
      </div>
      <div class="content">
         <div class="label">
            <h1>¡Promueve el talento, [user]!</h1>
         </div>

<div class="cards_background">
                  <p>Incorpórate acompaña a las empresas en la busqueda de talento profesional </p>
               <p>facilitando la conexión entre múltiples estudiantes y egresados</p>
               <p>siendo una conexión escencial entre talento y oportunidad</p>
</div>

         <div class="cards_background">
            <!--Titulo y filtros-->
            <div class="header_oportunidades">
               <h2>Acciones que puedes hacer</h2>
            </div>



               <p>Añadir carrera:</p>
               <form method="POST" action="../controllers/AdminController.php?action=career">
                  <input type="text" name="name" placeholder="Nombre carrera" required>

                  <select name="category_id" required>
                     <option value="">Selecciona categoría</option>
                     <?php foreach($career_categories as $cat): ?>
                           <option value="<?= $cat['id'] ?>">
                              <?= $cat['name'] ?>
                           </option>
                     <?php endforeach; ?>
                  </select>

                  <button>Agregar</button>
               </form>

                              <p>Añadir skill:</p>
               <form method="POST" action="../controllers/AdminController.php?action=skill">
                  <input type="text" name="name" placeholder="Nombre skill" required>

                  <select name="category_id" required>
                     <option value="">Selecciona categoría</option>
                     <?php foreach($skill_categories as $cat): ?>
                           <option value="<?= $cat['id'] ?>">
                              <?= $cat['name'] ?>
                           </option>
                     <?php endforeach; ?>
                  </select>

                  <button>Agregar</button>
               </form>

                              <p>Añadir compañia:</p>
               <form method="POST" action="../controllers/AdminController.php?action=company">
                  <input type="text" name="name" placeholder="Empresa" required>
                  <button>Agregar</button>
               </form>

                              <p>Añadir usuarios:</p>
               <form method="POST" action="../controllers/AdminController.php?action=user">
                  <input type="email" name="email" placeholder="Correo">
                  <input type="password" placeholder="password" name="password">
                  
                  <select name="role" onchange="toggleCompany(this)">
                     <option value="admin">Admin</option>
                     <option value="student">Student</option>
                     <option value="company">Company</option>
                  </select>



                  <div id="companySelect" style="display:none;">
                                       <input type="text" name="contact_name" placeholder="Nombre contacto">
                  <input type="text" name="contact_position" placeholder="Cargo">
                  
                  <input type="text" name="contact_phone" placeholder="Teléfono">
                     <select name="company_id" required>
                        <option value="">Seleccione empresa</option>
                        <?php foreach($data['companies'] as $c): ?>
                              <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>

                  <button>Crear</button>
               </form>
            </div>

            <div class="cards_background">
               

               <h2>Usuarios</h3>

               <?php foreach($data['users'] as $u): ?>
               <form method="POST" action="../controllers/AdminController.php?action=role">
                  <p><?= $u['email'] ?></p>

                  <input type="hidden" name="user_id" value="<?= $u['id'] ?>">

                  <select name="role" onchange="toggleCompanyEdit(this, <?= $u['id'] ?>)">
                     <option value="admin" <?= $u['role']=="admin"?"selected":"" ?>>Admin</option>
                     <option value="student" <?= $u['role']=="student"?"selected":"" ?>>Student</option>
                     <option value="company" <?= $u['role']=="company"?"selected":"" ?>>Company</option>
                  </select>

                  <div id="companyEdit<?= $u['id'] ?>" style="display:none;">
                     <select name="company_id">
                        <option value="">Seleccione empresa</option>
                        <?php foreach($data['companies'] as $c): ?>
                              <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>

                  <button>Actualizar</button>
               </form>

               <form method="POST" action="../controllers/AdminController.php?action=delete">
                  <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                  <button onclick="return confirm('¿Eliminar usuario?')">Eliminar</button>
               </form>
               <?php endforeach; ?>

               

            </div>

            <div class="cards_background">

               <h2>Carreras</h3>
               <?php foreach($data['careers'] as $c): ?>
                  <p><?= $c['name'] ?></p>
               <?php endforeach; ?>

              

            </div>

            <div class="cards_background">

               <h2>Skills</h3>
               <?php foreach($data['skills'] as $s): ?>
                  <p><?= $s['name'] ?></p>
               <?php endforeach; ?>

            
            </div>

            <div class="cards_background">

               <h2>Empresas</h3>
               <?php foreach($data['companies'] as $c): ?>
                  <p><?= $c['name'] ?></p>
               <?php endforeach; ?>

             

            </div>

         </div>

      </div>
      <script src="../assets/js/home.js"></script>
      <script>
         
<?php if(isset($_GET['success'])): ?>

    <?php if($_GET['success']=="user"): ?>
        showNotification("Usuario creado correctamente");
    <?php endif; ?>

    <?php if($_GET['success']=="role"): ?>
        showNotification("Rol actualizado correctamente");
    <?php endif; ?>

    <?php if($_GET['success']=="career"): ?>
        showNotification("Carrera creada correctamente");
    <?php endif; ?>

    <?php if($_GET['success']=="skill"): ?>
        showNotification("Skill creada correctamente");
    <?php endif; ?>

    <?php if($_GET['success']=="company"): ?>
        showNotification("Empresa creada correctamente");
    <?php endif; ?>

<?php endif; ?>

<?php if(isset($_GET['error'])): ?>

    <?php if($_GET['error']=="company_required"): ?>
        showNotification("Debes seleccionar una empresa", "error");
    <?php endif; ?>

<?php endif; ?>
</script>
 </body>
 </html>