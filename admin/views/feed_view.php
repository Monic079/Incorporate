  <?php
require_once "../controllers/CvController.php";

$controller = new CvController();
$cvs = $controller->allCvs();

require_once "../controllers/OpportunityController.php";

$controller = new CvController();
$ops = $controller->feed();
?>
 <!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incorpórate</title>
    <link rel="stylesheet" href="../assets/css/estilo_feed.css">
 </head>
 <body>
      <div class="navbar">
         <img src="../assets/img/icons/logo_inc.png" alt="Logo">
         <a href="home_view.php"><button> <img src="../assets/img/icons/home_w.png" alt="Home"> Home </button></a>         
         <a href="feed_view.php"> <button class="active"> <img src="../assets/img/icons/users_b.png" alt="Feed"> Feed </button>      </a>
         <a href="../controllers/AuthController.php?action=logout"><button> <img src="../assets/img/icons/exit.png" alt="Feed"> Salir </button></a>      
      </div>
      <div class="content">
         <div class="label">
            <h1>¡Promueve el talento, [user]!</h1>
         </div>
         
<!--CARD-->
<div class="cards_background">

    <!--Titulo-->
    <div class="header_oportunidades">
        <h2>Talento</h2>
    </div>

      <?php if(empty($cvs)): ?>
         <p>No has creado CVs aún.</p>
      <?php else: ?>

         <?php foreach($cvs as $cv): ?>
               
               <div class="card" id="talento_cards_background" >

                  <!-- Carrera -->
                  <div class="category">
                     <p><?= $cv['career'] ?? 'Sin carrera' ?></p>
                  </div>

                  <!-- Nombre -->
                  <h3><?= htmlspecialchars($cv['full_name']) ?></h3>

                                    <!-- Skills -->
                  <?php 
                  $skills = !empty($cv['skills']) ? explode('||', $cv['skills']) : [];
                  ?>

                  <div class="info_1">
                     <img src="../assets/img/icons/skills_ b.png">
                     
                     <?php foreach($skills as $skill): ?>
                        <span class="skill"><?= htmlspecialchars($skill) ?></span>
                     <?php endforeach; ?>

                  </div>

                                    <!-- Links -->
                  <?php 
                  $links = !empty($cv['links']) ? explode('||', $cv['links']) : [];
                  ?>

                  <div class="info_1">
                     <img src="../assets/img/icons/link.png">

                     <?php foreach($links as $link): 
                        list($type, $url) = explode('::', $link);
                     ?>
                        <a href="<?= htmlspecialchars($url) ?>" target="_blank">
                              <?= htmlspecialchars($type) ?>
                        </a>
                     <?php endforeach; ?>

                  </div>

                                    <!-- Botón -->
                                    <div class="card_btn">
                                       <a href="cv_detail_view.php?id=<?= $cv['id'] ?>">
                                             <button class="btn_card">
                                                <img src="../assets/img/icons/arrow-right_w.png">
                                             </button>
                                       </a>
                                    </div>

               </div>
         <?php endforeach; ?>
      <?php endif; ?>

   </div>



   <div class="cards_background"> 
            <div class="header_oportunidades">
               <h2>Oportunidades publicadas</h2>

            </div>

                  <?php foreach($ops as $op): ?>
            <!--CARD-->
               <div class="card">
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
                                          <a href="../views/oportunidades_detail_view.php?id=<?= $op['id'] ?>">
                        <button class="btn_card"> <img src="../assets/img/icons/arrow-right_w.png"></button> 
                     </a>
                  </div>
               </div>
            <?php endforeach; ?>

   </div>

      </div>
 </body>
 </html>