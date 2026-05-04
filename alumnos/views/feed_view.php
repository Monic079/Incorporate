 <?php
require_once "../controllers/CvController.php";
require_once "../controllers/OpportunityController.php";

$cvController = new CvController();
$cvs = $cvController->getUserCvs();

$controller = new CvController();
$ops = $controller->feed();
?>
 <!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incorpórate</title>
    <link rel="stylesheet" href="../assets/css/estilo_home.css">
 </head>
 <body>
      <div class="navbar">
         <img src="../assets/img/icons/logo_inc.png" alt="Logo">
         <a href="home_view.php"> <button> <img src="../assets/img/icons/home_w.png" alt="Home"> Home </button></a>
         <a href="cvs_view.php"><button> <img src="../assets/img/icons/briefcase_w.png" alt="Oportunidades"> Cvs </button></a>
         <a href="feed_view.php"><button class="active"> <img src="../assets/img/icons/users_b.png" alt="Feed"> Feed </button>      </a>
         <a href="../controllers/AuthController.php?action=logout"><button> <img src="../assets/img/icons/exit.png" alt="Feed"> Salir </button></a>      
      </div>
      <div class="content">
         <div class="label">
            <h1>¡Muestra tu talento, [user]!</h1>
         </div>
         <div class="cards_background">
            <!--Titulo y filtros-->
            <div class="header_oportunidades">
               <h2>Oportunidades publicadas</h2>
            </div>

            <?php foreach($ops as $op): ?>
            <!--CARD-->
            <div class="card">
               <div class="category">
                  <p><?= $op['type_opor'] ?></p>
               </div>
               <h3><?= $op['title'] ?></h3>
               <div class="info_1">
                  <img src="../assets/img/icons/users_b.png">
                  <p>Vacantes: </p>
                  <p><?= $op['vacancies'] - $op['accepted_count'] ?>/<?= $op['vacancies'] ?></p>
               </div>
               <div class="info_1">            
                  <p>Modaliad:</p>
                  <p><?= $op['modality'] ?> </p>

               </div>

               <div class="info_1">
                  <p>Fecha límite: </p>
                  <p> <?= $op['deadline'] ?></p>
               </div>
               <div class="card_btn">


               <a href="oportunidades_detail_view.php?id=<?= $op['id'] ?>">
                  <button class="btn_card">
                     <img src="../assets/img/icons/arrow-right_w.png">
                  </button>
               </a>

                  
                  <form method="GET" action="../controllers/ApplicationController.php">

                  <input type="hidden" name="action" value="apply">
                  <input type="hidden" name="id" value="<?= $op['id'] ?>">

                  <select name="cv_id" required>
                     <option value="">Selecciona CV</option>
                     <?php foreach($cvs as $cv): ?>
                           <option value="<?= $cv['id'] ?>">
                              <?= $cv['full_name'] ?>
                           </option>
                     <?php endforeach; ?>
                  </select>

                  <button class="btn_card">
                     <img src="../assets/img/icons/apply_w.png">
                  </button>

               </form>

               </div>
            </div>
            <?php endforeach; ?>

         </div>

      </div>
 </body>
 </html>