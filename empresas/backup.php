<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<!----------------------------------------------------------FORM--------------------------------------------->
    <form id="formOportunidad">

        <!-- ===================== -->
        <!-- GENERAL -->
        <!-- ===================== -->
        <div class="form-step" id="general_f">

            <input type="text" placeholder="Título de la oportunidad" name="title" required>

            <select id="tipo" name="type_opor" required>
                <option value="">Tipo de oportunidad</option>
                <option value="pasantia">Pasantía</option>
                <option value="trabajo">Trabajo</option>
            </select>

            <!-- PASANTIA -->
            <div id="campo_remuneracion" style="display:none;">
                <input type="number" placeholder="Remuneración" name="remuneration">
            </div>

            <!-- TRABAJO -->
            <div id="campo_salario" style="display:none;">
                <input type="number" placeholder="Salario mínimo" name="salary_min">
                <input type="number" placeholder="Salario máximo" name="salary_max">
            </div>

            <!-- VISIBILIDAD -->
            <div id="campo_visibilidad" style="display:none;">
                <label>
                    <input type="checkbox" name="salary_visible">
                    Mostrar salario/remuneración
                </label>
            </div>

            <input type="number" placeholder="Número de vacantes" name="vacancies" required>

            <input type="date" name="deadline" required>

        </div>

        <!-- ===================== -->
        <!-- DETALLES -->
        <!-- ===================== -->
        <div class="form-step" id="detalles_f" style="display:none;">

            <!-- Carreras -->
            <label>Carreras</label>
            <select name="careers[]" multiple>
                <!-- dinámico desde DB -->
                <option value="1">Ingeniería Sistemas</option>
                <option value="2">Administración</option>
            </select>

            <!-- Niveles -->
            <label>Nivel</label>
            <select id="nivel" name="level">
                <option value="">Seleccionar</option>
                <option value="estudiante">Estudiante</option>
                <option value="egresado">Egresado</option>
            </select>

            <!-- Año -->
            <div id="campo_anio" style="display:none;">
                <input type="number" placeholder="Año que cursa" name="year">
            </div>

            <textarea name="functions" placeholder="Funciones"></textarea>

            <!-- Skills -->
            <label>Skills</label>
            <select name="skills[]" multiple>
                <option value="1">PHP</option>
                <option value="2">MySQL</option>
            </select>

            <select name="modality">
                <option value="">Modalidad</option>
                <option value="presencial">Presencial</option>
                <option value="semi">Semi</option>
                <option value="remoto">Remoto</option>
            </select>

            <input type="text" name="schedule" placeholder="Horario">

            <label>Detalles del horario</label>
            <textarea name="schedule" rows="2" placeholder="Ej: Lunes a Viernes de 8:00 am a 5:00 pm. Sábados media jornada."></textarea>

        </div>

        <!-- ===================== -->
        <!-- CONTACTO -->
        <!-- ===================== -->
        <div class="form-step" id="contacto_f" style="display:none;">

            <input type="text" name="contact_name" value="Juan Pérez">
            <input type="text" name="contact_position" value="RRHH">
            <input type="email" name="contact_email" value="empresa@email.com">
            <input type="text" name="contact_phone" value="7777-7777">

        </div>

        <div class="form-navigation">
            <button type="button" class="btn_opor" id="prevBtn" style="display:none;">
                Volver
            </button>

            <button type="button" class="btn_opor" id="nextBtn">
                <span id="btnText">Next</span>
            </button>
        </div>

        <button class="btn_opor" id="btn_send"> Send </button> 

    </form>
    

            <!-- Skills 
            <label>Skills</label>
            <select name="skills[]" multiple>
                <?php foreach($skills as $s): ?>
                <option value="<?= $s['id'] ?>"
                    <?= (isset($op['skills']) && in_array($s['id'], $op['skills'])) ? 'selected' : '' ?>>
                    <?= $s['name'] ?>
                </option>
                <?php endforeach; ?>
            </select>


                        <!--CARD-->
            <div class="card">
               <div class="category">
                  <p>Pasantia</p>
               </div>
               <h3>Nombre de la oportunidad</h3>
               <div class="info_1">
                  <img src="../assets/img/icons/user_b.png">
                  <p>Vacantes: </p>
                  <p>4/5</p>
               </div>
               <div class="info_1">
                  <img src="../assets/img/icons/users_b.png">
                  <p>Aplicaciones: </p>
                  <p>12</p>
               </div>
               <div class="card_btn">
                  <button class="btn_card"> <img src="../assets/img/icons/arrow-right_w.png"></button> 
                  <button class="btn_card"> <img src="../assets/img/icons/handshake_w.png"></button> 
                  <button class="btn_card"> <img src="../assets/img/icons/trash_w.png"></button> 
               </div>
            </div>

            
</body>
</html>
