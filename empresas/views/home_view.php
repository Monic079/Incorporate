 <?php
require_once "../controllers/OpportunityController.php";

$controller = new OpportunityController();
$ops = $controller->index();
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
         <a href="home_view.php"> <button class="active"> <img src="../assets/img/icons/home_b.png" alt="Home"> Home </button></a>
         <a href="oportunidades_view.php"><button> <img src="../assets/img/icons/briefcase_w.png" alt="Oportunidades"> Oportunidades </button></a>
         <a href="feed_view.php"><button> <img src="../assets/img/icons/users_w.png" alt="Feed"> Feed </button>      </a>
         <a href="../controllers/AuthController.php?action=logout"><button> <img src="../assets/img/icons/exit.png" alt="Feed"> Salir </button></a>      
      </div>
      <div class="content">
         <div class="label">
            <h1>¡Promueve el talento, [user]!</h1>
         </div>
<div id="texto_Info">
               <p>Incorpórate acompaña a las empresas en la busqueda de talento profesional </p>
               <p>facilitando la conexión entre múltiples estudiantes y egresados</p>
               <p>siendo una conexión escencial entre talento y oportunidad</p>
</div>

         <div class="cards_background">
            <!--Titulo y filtros-->
            <div class="header_oportunidades">
               <h2>Oportunidades publicadas recientemente</h2>

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