<?php
require_once "../controllers/OpportunityController.php";
require_once "../controllers/CvController.php";
require_once "../controllers/ApplicationController.php";

$controller = new OpportunityController();
$op = $controller->show();

$cvController = new CvController();
$cvs = $cvController->getUserCvs();

$appController = new ApplicationController();
$applications = $appController->getByOpportunity($op['id']);
?>


<!DOCTYPE html>
 <html lang="es">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incorpórate</title>
    <link rel="stylesheet" href="../assets/css/estilo_form.css">
    <link rel="stylesheet" href="../assets/css/estilo_details.css">
 </head>
 <body>
      <div class="navbar">
         <img src="../assets/img/icons/logo_inc.png" alt="Logo">
         <a href="home_view.php"> <button> <img src="../assets/img/icons/home_w.png" alt="Home"> Home </button></a>
         <a href="oportunidades_view.php"><button class="active"> <img src="../assets/img/icons/briefcase_b.png" alt="Oportunidades"> Oportunidades </button></a>
         <a href="feed_view.php"><button> <img src="../assets/img/icons/users_w.png" alt="Feed"> Feed </button>      </a>
         <a href="../controllers/AuthController.php?action=logout"><button> <img src="../assets/img/icons/exit.png" alt="Feed"> Salir </button></a>      
      </div>

        <div class="content">

            <div class="label">
                <h1>¡Promueve el talento, [user]!</h1>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 class="titulo_oportunidad">Oportunidad: <?= $op['title'] ?></h2>               
   
            </div>
            
            <div class="info_oportunidad">

                <div class="Organizacion">

                    <div>
                        <div class="contenedores">
                            <h3>Tipo: </h3> <p><?= $op['type_opor'] ?> </p>
                             
                            <?php if($op['salary_visible']): ?>
                                <h3>Salario/Remuneración: </h3>                                
                                <p>
                                    <?= $op['salary_min'] ?> - <?= $op['remuneration'] ?>
                                </p>
                            <?php endif; ?>
                            
                            <h3>#Vacantes: </h3> <p><?= $op['vacancies'] ?></p>
                            <h3>Fecha límite: </h3> <p><?= $op['deadline'] ?></p>
                        </div>

                        <div class="contenedores">
                            <h3 class="titulo-contenedor"><strong>Datos de contacto:</strong></h3>
                            <h3>Nombre:</h3> <p><?= $op['contact_name'] ?></p>
                            <h3>Cargo: </h3> <p><?= $op['contact_position'] ?></p>
                            <h3>Email: </h3> <p><?= $op['contact_email'] ?></p>
                            <h3>Teléfono: </h3> <p><?= $op['contact_phone'] ?></p>
                        </div>
                    </div>
                    
                    <div>
                        <div class="contenedores">
                                <div class="Organizacion">
                                    <div>
                                        <h3 class="titulo-contenedor"><strong>Carreras que aplican: </strong></h3>
                                    </div>
                                    <div>
                                        <h3>Nivel requerido: </h3>
                                        <p>
                                            <?= $op['level_data']['level'] ?? 'N/A' ?>
                                            <?php if(!empty($op['level_data']['year'])): ?>
                                                - Año <?= $op['level_data']['year'] ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <ul>
                                        <?php foreach($op['careers'] as $c): ?>
                                            <li><?= $c ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                        </div>

                        <div class="contenedores">
                        <h3 class="titulo-contenedor"><strong>Skills requeridas: </strong></h3>
                            <ul>
                                <?php foreach($op['skills'] as $s): ?>
                                    <li><?= $s ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="Organizacion">
                            <div class="contenedores">
                                <h3 class="titulo-contenedor"><strong>Funciones a ejecutar: </strong></h3>
                                <p><?= $op['functions'] ?></p>
                            </div>
                            <div class="contenedores">
                                <h3 class="titulo-contenedor"><strong>Horario: </strong></h3>
                                <p><?= $op['schedule'] ?></p>
                                <h3 class="titulo-contenedor"><strong>Modalidad: </strong></h3>
                                <p><?= $op['modality'] ?></p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        
        <div class="header_oportunidades">


         </div>





      </div>


 </body>
 </html>

