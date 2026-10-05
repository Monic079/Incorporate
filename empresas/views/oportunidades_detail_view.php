<?php
require_once "../controllers/OpportunityController.php";
require_once "../controllers/ApplicationController.php";
require_once "../controllers/MatchingController.php"; 

$controller = new OpportunityController();
$op = $controller->show();

$appController = new ApplicationController();
// Obtener el conteo real usando el ID de la oportunidad actual
$totalApplications = $appController->getCount($op['id']);
$applications = $appController->getByOpportunity($op['id']);
// Muestra los 6 cvs con los que la oportunidad hace matching
$matching = (new MatchingController())->forOpportunity((int)$op['id'], 6); 
?>


<!DOCTYPE html>
 <html lang="es">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incorpórate</title>
    <link rel="stylesheet" href="../assets/css/estilo_form.css">
    <link rel="stylesheet" href="../assets/css/estilo_details.css?v=<?= time() ?>">
    
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
                
                <div  class="actions">
                    
                    <span>Aplicaciones: <?= $totalApplications ?></span>
                    
                    
                    <!-- Boton para editar -->
                    <a href="../views/oportunidades_form_view.php?id=<?= $op['id'] ?>">
                        <button class="botones"><img class="botones_img" src="../assets/img/icons/edit_w.png" alt="Editar"></button>
                    </a>

                    <!-- Boton para eliminar -->
                    <form method="POST" action="../controllers/OpportunityController.php?action=delete" onsubmit="return confirm('¿Eliminar?');">
                        <input type="hidden" name="id" value="<?= $op['id'] ?>">
                        <button type="submit" class="botones"> <img class="botones_img" src="../assets/img/icons/trash_w.png" alt="Eliminar"> </button>
                    </form>  
                </div>      
            </div>
            
            <div class="info_oportunidad">

                <div class="Organizacion">

                    <div>
                        <div class="contenedores">
                            <h3>Tipo: </h3> <p><?= $op['type_opor'] ?> </p>
                             
                            <?php if($op['salary_visible']): ?>
                                <h3>Salario/Remuneración: </h3>                                
                                <p>
                                    <?= $op['salary_min'] ?> - <?= $op['salary_max'] ?>
                                    <?= $op['remuneration'] ?>
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
        
            <!-- apartado de matching -->
        <?php include __DIR__ . "/matching_section.php"; ?>

        <div class="header_oportunidades">


            <div id="applications_section"> 

                <h3>Cvs que se han postulado a esta oportunidad</h3>

                <?php if(!empty($applications)): ?>
                    
                    <?php foreach($applications as $app): ?>
                        
                        <div class="card">

                            <div class="category">
                                <p><?= $app['level'] ?></p>
                            </div>

                            <h3><?= $app['full_name'] ?></h3>

                            <?php if(!empty($app['photo'])): ?>
                                
                                <img src="../../alumnos/assets/img/uploads/<?= $app['photo'] ?>" width="100">
                            <?php endif; ?>

                            <div class="info_1">
                                <p><strong>Año:</strong> <?= $app['year'] ?? 'N/A' ?></p>
                            </div>

                            <div class="info_1">
                                <p><strong>Estado:</strong> <?= $app['status'] ?></p>
                            </div>

                            <div class="info_1">
                                <p><strong>Aplicado:</strong> <?= $app['applied_at'] ?></p>
                            </div>

                            <!-- OPCIONAL: ver CV -->
                            <a href="cv_detail_view.php?id=<?= $app['cv_id'] ?>">
                                <button class="btn_form">Ver CV</button>
                            </a>



                            <form method="POST" action="../controllers/ApplicationController.php?action=updateStatus">
                                
                                <input type="hidden" name="application_id" value="<?= $app['application_id'] ?>">
                                <input type="hidden" name="opportunity_id" value="<?= $_GET['id'] ?>">

                                <select name="status">
                                    <option value="aplicado" <?= $app['status']=='aplicado'?'selected':'' ?>>Aplicado</option>
                                    <option value="revision" <?= $app['status']=='revision'?'selected':'' ?>>Revisión</option>
                                    <option value="rechazado" <?= $app['status']=='rechazado'?'selected':'' ?>>Rechazado</option>
                                    <option value="aceptado" <?= $app['status']=='aceptado'?'selected':'' ?>>Aceptado</option>
                                </select>

                                <button type="submit">Actualizar</button>

                            </form>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>
                    <p>Nadie ha aplicado a esta oportunidad</p>
                <?php endif; ?>

            </div>
       </div>
</div>

<script src="../assets/js/s"></script>
 </body>
 </html>


