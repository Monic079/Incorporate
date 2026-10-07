  <?php
require_once "../controllers/OpportunityController.php";

$controller = new OpportunityController();
$ops = $controller->index();

$nombre = $_SESSION['user_name'] ?? 'Empresa';
?>
 <!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incorpórate</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
 </head>
 <body>
      <div class="navbar">
         <img src="../assets/img/icons/logo_inc.png" alt="Logo">
         <a href="home_view.php"> <button> <img src="../assets/img/icons/home_w.png" alt="Home"> Home </button></a>
         <a href="oportunidades_view.php"><button class="active"> <img src="../assets/img/icons/briefcase_b.png" alt="Oportunidades"> Oportunidades </button></a>
         <a href="feed_view.php"><button> <img src="../assets/img/icons/users_w.png" alt="Feed"> Feed </button></a>      
         <a href="../controllers/AuthController.php?action=logout"><button> <img src="../assets/img/icons/exit.png" alt="Feed"> Salir </button></a>      
      </div>
      <div class="content">
         <div class="label">
            <h1>¡Promueve el talento, <?= htmlspecialchars($nombre) ?>!</h1>
         </div>
         <a href="oportunidades_form_view.php"><button class="btn_opor"> <img src="../assets/img/icons/plus-small_w.png" alt="Crear oportunidad"> Crear oportunidad </button> </a>      
         <div class="cards_background">
            <!--Titulo y filtros-->
            <div class="header_oportunidades">
               <h2>Oportunidades publicadas</h2>

            </div>
            <!--CARD-->
<?php if(empty($ops)): ?>
    
    <div class="no-data">
        <p>No has creado ninguna oportunidad todavía.</p>
    </div>

<?php else: ?>            
            <?php foreach($ops as $op): ?>
            <!--CARD-->
               <div class="card">
                  <input type="hidden" name="id" value="<?= $op['id'] ?? '' ?>">
                  
                  <div class="category">
                     <p><?=$op['type_opor']?></p>
                  </div>
                  <h3><?= $op['title'] ?></h3>
                  <div class="info_1">
                     <img src="../assets/img/icons/user_b.png">
                     <p>Vacantes: </p>
 
                     <p><?= $op['vacancies'] - $op['accepted_count'] ?>/<?= $op['vacancies'] ?></p>
                  </div>
                  <div class="info_1">
                     <img src="../assets/img/icons/users_b.png">
                     <p>Aplicaciones: </p>
                     <p><?= $op['total_applications'] ?></p>
                  </div>
                  <div class="card_btn">
                     

                     <!-- EDIT -->
                     <a href="../views/oportunidades_detail_view.php?id=<?= $op['id'] ?>">
                        <button class="btn_card"> <img src="../assets/img/icons/arrow-right_w.png"></button> 
                     </a>

                     <a href="../views/oportunidades_detail_view.php?id=<?= $op['id'] ?>#applications_section">
                        <button class="btn_card"> <img src="../assets/img/icons/apply_w.png"></button> 
                     </a>

                     <!-- DELETE -->
                     <form method="POST" action="../controllers/OpportunityController.php?action=delete" onsubmit="return confirm('¿Eliminar?');">
                        <input type="hidden" name="id" value="<?= $op['id'] ?>">
                        <button type="submit" class="btn_card"> <img src="../assets/img/icons/trash_w.png"></button> 
                     </form>
                     

                  </div>
               </div>
            <?php endforeach; ?>

<?php endif; ?>

         </div>
      </div>
 </body>
 </html>