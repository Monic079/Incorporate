<?php
session_start();

//DEBUGGGGGGGGGGGGGGGGGGGGGGGGGGGGGGGGGGGG
/*
echo "<pre>";
echo "SESSION en form:\n";
print_r($_SESSION);
exit;*/

// VALIDACIÓN GLOBAL
if(!isset($_SESSION['user_id'])){
    header("Location: login_view.php");
    exit();
}

?>
<!DOCTYPE html>
 <html lang="es">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incorpórate</title>
    <link rel="stylesheet" href="../assets/css/estilo_form.css">
 </head>
 <body>
      <div class="navbar">
         <img src="../assets/img/icons/logo_inc.png" alt="Logo">
         <a href="home_view.php"> <button> <img src="../assets/img/icons/home_w.png" alt="Home"> Home </button></a>
         <a href="cvs_view.php"><button class="active"> <img src="../assets/img/icons/briefcase_b.png" alt="Oportunidades"> Cvs </button></a>
         <a href="feed_view.php"><button> <img src="../assets/img/icons/users_w.png" alt="Feed"> Feed </button></a>    
         <a href="../controllers/AuthController.php?action=logout"><button> <img src="../assets/img/icons/exit.png" alt="Feed"> Salir </button></a>      
      </div>

      <div class="content">

         <div class="label">
            <h1>¡Muestra tu talento, [user]!</h1>
         </div>

<!---------------------INDICE----------------------------------------------------------------->
         <div class="linea_de_contenido">            
            <div id="contacto">
                <div class="circle"></div>
                <div class="category"><p>Contacto</p></div>
            </div>
            <hr>
            <div id="general">
                <div class="circle"></div>
                <div class="category"><p></p>General</div>
            </div>
            <hr>
            <div id="detalles">
                <div class="circle"></div>
                <div class="category"><p>Detalles</p></div>
            </div>
         </div>
<!---------------------INDICE----------------------------------------------------------------->

<!---------------------FORM----------------------------------------------------------------->
<?php
require_once "../controllers/CvController.php";

$controller = new CvController();

//MODO UPDATE
$cv = null;
if(isset($_GET['id'])){
    $cv = $controller->show();
}

// traer data del form
$formData = $controller->getFormData();

//$company = $formData['company'];
$careers = $formData['careers'];
$skills = $formData['skills'];

?>

<?php if(!empty($errors)): ?>
    <div style="color: white; background-color: red; padding: 10px; border-radius: 5px;">
        <strong>Advertencias:</strong>
        <ul>
            <?php foreach($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>


<form id="formCv" enctype="multipart/form-data" method="POST" action="../controllers/CvController.php?action=save">
    <input type="hidden" name="id" value="<?= $cv['id'] ?? '' ?>">

        <!-- ===================== -->
        <!-- CONTACTO -->
        <!-- ===================== -->
        <div class="form-step" id="contacto_f">
            <!-- FOTO -->
            <label>Foto</label>
            <?php if(!empty($cv['photo'])): ?>
                <div>
                    <img src="../assets/img/uploads/<?= $cv['photo'] ?>" width="120">
                    
                </div>
            <?php endif; ?>

            <!--<input type="file" name="photo" accept="image/*">-->
            <input type="hidden" name="current_photo" value="<?= $cv['photo'] ?? '' ?>">
            
            <!-- 1. Campo oculto con el nombre de la foto actual -->
            <!-- Si $cv['photo'] existe, lo envía; si no, envía vacío -->
            <input type="hidden" name="current_photo" value="<?= $cv['photo'] ?? '' ?>">

            <!-- 2. Input para subir una nueva foto -->
            <input type="file" name="photo" accept="image/*">    
            <small>Si no seleccionas un archivo, se mantendrá la foto actual.</small>

            <!-- NOMBRE -->
             <label>Nombre</label>
            <input type="text"
                name="full_name"
                placeholder="Nombre completo"
                value="<?= $cv['full_name'] ?? '' ?>"
                required
                minlength="3"
                maxlength="150">

            <!-- TELEFONO -->
             <label>Teléfono</label>
            <input type="number"
                name="phone"
                placeholder="Teléfono (solo números)"
                value="<?= $cv['phone'] ?? '' ?>"
                pattern="[0-9]{8,15}"
                inputmode="numeric"
                title="Solo números, entre 8 y 15 dígitos">

            <!-- EMAIL -->
             <label>Email</label>
            <input type="email"
                name="email"
                placeholder="Correo"
                value="<?= $cv['email'] ?? '' ?>"
                required>

            <!-- GENERO -->
            <select name="gender" required>
                <option value="">Género</option>
                <option value="masculino" <?= ($cv['gender'] ?? '')=='masculino'?'selected':'' ?>>Masculino</option>
                <option value="femenino" <?= ($cv['gender'] ?? '')=='femenino'?'selected':'' ?>>Femenino</option>
            </select>

                        <!--EDAAD-->
             <label>Edad</label>
            <input type="number"
                name="edad"
                placeholder="Edad"
                value="<?= $cv['age'] ?? '' ?>"
                inputmode="numeric"
                title="Solo números"
                min="1">

        </div>

        <!-- ===================== -->
        <!-- GENERAL GENDDER YEAR LEVEL CAREER -->
        <!-- ===================== -->
        <div class="form-step" id="general_f" style="display:none;">

            <!-- CARRERAS -->
            <label>Carreras</label>
            <select name="careers[]" multiple required>
                <?php foreach($careers as $c): ?>
                    <option value="<?= $c['id'] ?>"
                        <?= (isset($cv['careers_ids']) && in_array($c['id'], $cv['careers_ids'])) ? 'selected' : '' ?>>
                        <?= $c['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- NIVEL -->
            <label>Nivel</label>
            <select id="nivel" name="level" required>
                <option value="">Seleccionar</option>

                <option value="estudiante"
                    <?= ($cv['level'] ?? '') == 'estudiante' ? 'selected' : '' ?>>
                    Estudiante
                </option>

                <option value="egresado"
                    <?= ($cv['level'] ?? '') == 'egresado' ? 'selected' : '' ?>>
                    Egresado
                </option>
            </select>

            <!-- AÑO -->
            <div id="campo_anio" style="display:none;">
                <input type="number" placeholder="Año que cursa"
                    name="year"
                    value="<?= $cv['year'] ?? '' ?>"  min="1"max="10">
            </div>

            <!-- RESUMEN -->
            <div id="resume">
            <label>Resume</label>
            <textarea name="resume" placeholder="Resumen profesional" maxlength="1000" required> 
                <?= $cv['resume'] ?? '' ?>
            </textarea>
            </div>
        </div>



        <!-- ===================== -->
        <!-- DETALLES: SKILLS, EDUCACION, EXPERIENCIA, ESO  -->
        <!-- ===================== -->
       <div class="form-step" id="detalles_f" style="display:none;">

        <h3>Experiencia</h3>

        <div id="experiences_container">

            <?php if(!empty($cv['experiences'])): ?>
                <?php foreach($cv['experiences'] as $exp): ?>
                    <div class="exp-item">

                        <input type="text" name="exp_company[]"
                            placeholder="Empresa"
                            value="<?= $exp['company_name'] ?>" maxlength="150" required>

                        <input type="text" name="exp_position[]"
                            placeholder="Cargo"
                            value="<?= $exp['position'] ?>"     maxlength="100" required>

                        <input type="date" name="exp_start[]"
                            value="<?= $exp['start_date'] ?>" required>

                        <input type="date" name="exp_end[]"
                            value="<?= $exp['end_date'] ?>" required>

                        <textarea name="exp_desc[]" maxlength="500"><?= $exp['description'] ?></textarea>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="exp-item">
                    <input type="text" name="exp_company[]" placeholder="Empresa">
                    <input type="text" name="exp_position[]" placeholder="Cargo">
                    <label>Fecha de inicio</label>
                    <input type="date" name="exp_start[]">
                    <label>Fecha de finalización</label>
                    <input type="date" name="exp_end[]">
                    <label>Descripción</label>
                    <textarea name="exp_desc[]" placeholder="Descripción breve"></textarea>
                </div>
            <?php endif; ?>

        </div>

        <button type="button" id="btnAddExperience">+ Agregar experiencia</button>

            <h3>Educación</h3>

            <div id="education_container">

                <?php if(!empty($cv['education'])): ?>
                    <?php foreach($cv['education'] as $edu): ?>
                        <div class="edu-item">

                            <input type="text" name="edu_institution[]"
                                value="<?= $edu['institution'] ?>"
                                placeholder="Institución"     maxlength="150" required> 

                            <input type="text" name="edu_program[]"
                                value="<?= $edu['program_name'] ?>"
                                placeholder="Programa" maxlength="150" required>

                            <input type="date" name="edu_start[]"
                                value="<?= $edu['start_date'] ?>" required>

                            <input type="date" name="edu_end[]"
                                value="<?= $edu['end_date'] ?>" required>

                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="edu-item">
                        <input type="text" name="edu_institution[]" placeholder="Institución">
                        <input type="text" name="edu_program[]" placeholder="Programa">
                        <label>Fecha de inicio</label>
                        <input type="date" name="edu_start[]">
                        <label>Fecha de finalización</label>
                        <input type="date" name="edu_end[]">
                    </div>
                <?php endif; ?>

            </div>

            <button type="button" id="btnAddEdu">+ Agregar educación</button>

                <h3>Skills</h3>

            <select name="skills[]" multiple>
                <?php foreach($skills as $s): ?>
                    <option value="<?= $s['id'] ?>"
                        <?= (isset($cv['skills_ids']) && in_array($s['id'], $cv['skills_ids'])) ? 'selected' : '' ?>>
                        <?= $s['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

                <h3>Links</h3>

            <div id="links_container">

                <?php if(!empty($cv['links'])): ?>
                    <?php foreach($cv['links'] as $l): ?>
                        <div class="link-item">

                        
                            <select name="link_type[]">
                                <option value="linkedin" <?= $l['type']=='linkedin'?'selected':'' ?>>LinkedIn</option>
                                <option value="github" <?= $l['type']=='github'?'selected':'' ?>>GitHub</option>
                                <option value="portfolio" <?= $l['type']=='portfolio'?'selected':'' ?>>Portfolio</option>
                                <option value="website" <?= $l['type']=='website'?'selected':'' ?>>Website</option>
                                <option value="other" <?= $l['type']=='other'?'selected':'' ?>>Otro</option>
                            </select>

                            <input type="url" name="link_url[]"
                                value="<?= $l['url'] ?>"
                                    placeholder="https://ejemplo.com"
                                    pattern="https?://.+"
                                    title="Debe ser una URL válida (https://...)">

                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="link-item">
                        <label>Tipo de link</label>
                        <select name="link_type[]">
                            <option value="linkedin">LinkedIn</option>
                            <option value="github">GitHub</option>
                            <option value="portfolio">Portfolio</option>
                            <option value="website">Website</option>
                            <option value="other">Otro</option>
                        </select>
                        <label>URL</label>
                        <input type="text" name="link_url[]" placeholder="URL">
                    </div>
                <?php endif; ?>

            </div>

            <button type="button" id="btnAddLink">+ Agregar link</button>

        </div>

        <div class="form-navigation">
            <button type="button" class="btn_opor" id="prevBtn" style="display:none;">
                Volver
            </button>

            <button type="button" class="btn_opor" id="nextBtn">
                <span id="btnText">Next</span>
            </button>
        </div>

        <!--<button type="button" class="btn_opor" id="btn_send"> Send </button> -->

    </form>

<!---------------------FORM----------------------------------------------------------------->


            
    </div>


         

      </div>
      <script src="../assets/js/form_view.js"></script>
      <script src="../assets/js/form.js"></script>
 </body>
 </html>