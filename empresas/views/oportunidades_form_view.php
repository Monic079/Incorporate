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

// SOLO EMPRESAS
if($_SESSION['role'] != 'company'){
    echo "Acceso denegado";
    exit();
}

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];

// limpiar después de usarlos
unset($_SESSION['errors']);
unset($_SESSION['old']);
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
         <a href="oportunidades_view.php"><button class="active"> <img src="../assets/img/icons/briefcase_b.png" alt="Oportunidades"> Oportunidades </button></a>
         <a href="feed_view.php"><button> <img src="../assets/img/icons/users_w.png" alt="Feed"> Feed </button>      </a>
         <a href="../controllers/AuthController.php?action=logout"><button> <img src="../assets/img/icons/exit.png" alt="Feed"> Salir </button></a>      
      </div>

      <div class="content">

         <div class="label">
            <h1>¡Promueve el talento, [user]!</h1>
         </div>

<!---------------------INDICE----------------------------------------------------------------->
         <div class="linea_de_contenido">            
            <div id="general">
                <div class="circle"></div>
                <div class="category"><p>General</p></div>
            </div>
            <hr>
            <div id="detalles">
                <div class="circle"></div>
                <div class="category"><p>Detalles</p></div>
            </div>
            <hr>
            <div id="contacto">
                <div class="circle"></div>
                <div class="category"><p>Contacto</p></div>
            </div>
         </div>
<!---------------------INDICE----------------------------------------------------------------->

<!---------------------FORM----------------------------------------------------------------->
<?php
require_once "../controllers/OpportunityController.php";

$controller = new OpportunityController();

//MODO UPDATE
$op = null;
if(isset($_GET['id'])){
    $op = $controller->show();
}

// traer data del form
$formData = $controller->getFormData();

$company = $formData['company'];
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

<form id="formOportunidad" method="POST" action="../controllers/OpportunityController.php?action=save">
    <input type="hidden" name="id" value="<?= $op['id'] ?? '' ?>">

        <!-- ===================== -->
        <!-- GENERAL -->
        <!-- ===================== -->
        <div class="form-step" id="general_f">

            <input type="text" placeholder="Título de la oportunidad" name="title" value="<?= $op['title'] ?? '' ?>" required>

            <select id="tipo" name="type_opor" required>
                <option value="">Tipo de oportunidad</option>
                <option value="pasantia" <?= ($op['type_opor'] ?? '')=='pasantia'?'selected':'' ?>>Pasantía</option>
                <option value="trabajo" <?= ($op['type_opor'] ?? '')=='trabajo'?'selected':'' ?>>Trabajo</option>
            </select>

            <!-- PASANTIA -->
            <div id="campo_remuneracion" style="display:none;">
                <input type="number" placeholder="Remuneración" name="remuneration" value="<?= $op['remuneration'] ?? '' ?>">
            </div>

            <!-- TRABAJO -->
            <div id="campo_salario" style="display:none;">
                <input type="number" name="salary_min" placeholder="Salario Mín" value="<?= $op['salary_min'] ?? '' ?>">
                <input type="number" name="salary_max" placeholder="Salario Max" value="<?= $op['salary_max'] ?? '' ?>">
            </div>

            <!-- VISIBILIDAD -->
            <div id="campo_visibilidad" style="display:none;">
                <label>
                    <input type="checkbox" name="salary_visible" <?= !empty($op['salary_visible'])?'checked':'' ?>>
                    Mostrar salario/remuneración
                </label>
            </div>

            <input type="number" placeholder="Número de vacantes" name="vacancies" value="<?= $op['vacancies'] ?? '' ?>" required>
            <input type="date" name="deadline" value="<?= $op['deadline'] ?? '' ?>" required>

        </div>

        <!-- ===================== -->
        <!-- DETALLES -->
        <!-- ===================== -->
        <div class="form-step" id="detalles_f" style="display:none;">

            <!-- Carreras -->


            <label>Carreras</label>
            <select name="careers[]" multiple required>
                <?php foreach($careers as $c): ?>
                    <option value="<?= $c['id'] ?>"
                        <?= (isset($op['careers_ids']) && in_array($c['id'], $op['careers_ids'])) ? 'selected' : '' ?>>
                        <?= $c['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            

            

            <!-- Niveles -->
            <label>Nivel</label>
            <select id="nivel" name="level">
                <option value="">Seleccionar</option>
                <option value="estudiante"
                    <?= ($op['level_data']['level'] ?? '') == 'estudiante' ? 'selected' : '' ?>>
                    Estudiante
                </option>

                <option value="egresado"
                    <?= ($op['level_data']['level'] ?? '') == 'egresado' ? 'selected' : '' ?>>
                    Egresado
                </option>
            </select> 


            <!-- Año -->
            <div id="campo_anio" style="display:none;">
                <input type="number" placeholder="Año que cursa" name="year" value="<?= $op['level_data']['year'] ?? '' ?>"> 
            </div>

            <textarea name="functions" placeholder="Funciones"><?= $op['functions'] ?? '' ?></textarea>        

            <label>Skills</label>
            <select name="skills[]" multiple required>
                <?php foreach($skills as $s): ?>
                <option value="<?= $s['id'] ?>"
                    <?= (isset($op['skills']) && in_array($s['id'], $op['skills'])) ? 'selected' : '' ?>>
                    <?= $s['name'] ?>
                </option>
                <?php endforeach; ?>
            </select>


            <select name="modality">
                <option value="">Modalidad</option>
                <option value="presencial"
                    <?= ($op['modality'] ?? '') == 'presencial' ? 'selected' : '' ?>>
                    Presencial
                </option>

                <option value="semi"
                    <?= ($op['modality'] ?? '') == 'semi' ? 'selected' : '' ?>>
                    Semi
                </option>

                <option value="remoto"
                    <?= ($op['modality'] ?? '') == 'remoto' ? 'selected' : '' ?>>
                    Remoto
                </option>
            </select>

            <label>Detalles del horario</label>
            <textarea name="schedule" rows="2" placeholder="Ej: Lunes a Viernes de 8:00 am a 5:00 pm. Sábados media jornada.">
                <?= $op['schedule'] ?? '' ?>
            </textarea>

        </div>

        <!-- ===================== -->
        <!-- CONTACTO -->
        <!-- ===================== -->
        <div class="form-step" id="contacto_f" style="display:none;">

            <input type="text" name="contact_name"
                value="<?= $company['contact_name'] ?? '' ?>">

            <input type="text" name="contact_position"
                value="<?= $company['contact_position'] ?? '' ?>">

            <input type="email" name="contact_email"
                value="<?= $company['contact_email'] ?? '' ?>">

            <input type="text" name="contact_phone"
                value="<?= $company['contact_phone'] ?? '' ?>">

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
      <script>
        const today = new Date().toISOString().split("T")[0];

const deadlineInput = document.querySelector('input[name="deadline"]');

if (deadlineInput) {
    deadlineInput.min = today;
}
      </script>
      <script src="../assets/js/form.js"></script>
      <script src="../assets/js/form_view.js"></script>
 </body>
 </html>