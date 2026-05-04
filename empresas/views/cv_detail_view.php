<?php
require_once "../controllers/CvController.php";

$controller = new CvController();
$cv = $controller->show();


?>

<!DOCTYPE html>
 <html lang="es">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incorpórate</title>
    <link rel="stylesheet" href="../assets/css/estilo_form.css">
    <link rel="stylesheet" href="../assets/css/estilo_cv_details.css">
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
            <h1>¡Muestra tu talento, [user]!</h1>
        </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 class="titulo_oportunidad">Nivel: <?= $cv['level'] ?></h2>               
                
 
            </div>

            <div class="info_oportunidad">

                <div class="Organizacion">

                    <div>
                        <div class= "contenedores-img" style="align-items: center;">
                            <?php if(!empty($cv['photo'])): ?>
                                <img src="../../alumnos/assets/img/uploads/<?= $cv['photo'] ?>" width="150" alt="Foto de perfil" class="img-perfil">
                            <?php endif; ?>
                        </div>
                        <div class="contenedores">
                            <h3 class="titulo-contenedor"><strong>Datos generales:</strong></h3>
                            <h3>Nombre: </h3> <p><?= $cv['full_name'] ?></p>
                            <h3>Email </h3> <p><?= $cv['email'] ?></p>
                            <h3>Teléfono: </h3> <p><?= $cv['phone'] ?></p>
                            <h3>Género: </h3> <p><?= $cv['gender'] ?></p>
                            <h3>Edad: </h3> <p><?= $cv['age'] ?></p>
                        </div>

                        <div class="contenedores">
                            <h3 class="titulo-contenedor"><strong>Información general:</strong></h3>
                            <h3>Nivel: </h3> <p><?= $cv['level'] ?></p>
                            <?php if($cv['level'] == 'estudiante'): ?>
                                <h3>Año: </h3> <p><?= $cv['year'] ?></p>
                            <?php endif; ?>
                            <h3>Resumen: </h3> <p><?= nl2br($cv['resume']) ?></p>
                        </div>
                    </div>
                    
                    <div>
                        <div class="contenedores">
                                <div class="Organizacion">
                                    <h3 class="titulo-contenedor"><strong>Carreras que aplican: </strong></h3>
                                    <?php if(!empty($cv['careers'])): ?>
                                            <ul>
                                                <?php foreach($cv['careers'] as $c): ?>
                                                    <li><?= $c['name'] ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p>No hay carreras</p>
                                        <?php endif; ?>
                                </div>
                        </div>

                        <div class="contenedores">
                        <h3 class="titulo-contenedor"><strong>Skills requeridas: </strong></h3>
                            <?php if(!empty($cv['skills'])): ?>
                                <ul>
                                    <?php foreach($cv['skills'] as $s): ?>
                                        <li><?= $s['name'] ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p>No hay skills</p>
                            <?php endif; ?>
                        </div>

                        <div class="contenedores">
                                <?php if(!empty($cv['links'])): ?>
                                    <ul>
                                        <?php foreach($cv['links'] as $l): ?>
                                            <li>
                                                <strong><?= ucfirst($l['type']) ?>:</strong>
                                                <a href="<?= $l['url'] ?>" target="_blank">
                                                    <?= $l['url'] ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <hr>
                                <?php else: ?>
                                    <p>No hay links</p>
                                <?php endif; ?>
                        </div>

                        <div class="Organizacion">
                            <div class="contenedores">
                                <h3 class="titulo-contenedor"><strong>Educación: </strong></h3>
                                    <?php if(!empty($cv['education'])): ?>
                                        <?php foreach($cv['education'] as $edu): ?>
                                            <div>
                                                <h3>Institución:</h3><p><?= $edu['institution'] ?></p>
                                                <h3>Programa:</h3><p><?= $edu['program_name'] ?></p>
                                                <h3>Inicio:</h3><p><?= $edu['start_date'] ?></p>
                                                <h3>Fin:</h3><p><?= $edu['end_date'] ?></p>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p>No hay educación</p>
                                    <?php endif; ?>
                            </div>
                        </div>

                </div>

                <div class = "contenedores">
                    <div class="contenedores">
                        <h3 class="titulo-contenedor"><strong>Experiencia: </strong></h3>
                            <?php if(!empty($cv['experiences'])): ?>
                                <?php foreach($cv['experiences'] as $exp): ?>
                                    <div>
                                        <h3>Empresa:</h3><p><?= $exp['company_name'] ?></p>
                                        <h3>Cargo: </h3><p><?= $exp['position'] ?></p>
                                        <h3>Inicio: </h3><p><?= $exp['start_date'] ?></p>
                                        <h3>Fin: </h3><p><?= $exp['end_date'] ?></p>
                                        <h3>Descripción: </h3><p><?= $exp['description'] ?></p>
                                    </div>
                                <?php endforeach; ?>
                             <?php else: ?>
                                <p>No hay experiencia</p>
                            <?php endif; ?>
                    </div>  
                </div>

    

            </div>

        </div>
    </body>
</html>