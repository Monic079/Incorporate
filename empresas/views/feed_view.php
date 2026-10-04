<?php
require_once "../controllers/CvController.php";

$controller = new CvController();
$cvs = $controller->allCvs();
$f       = $controller->getFilters();
$options = $controller->filterOptions();

// cantidad de filtros activos
$active = 0;
foreach(['career_category','career','level','age_min','age_max'] as $k){
    if(!empty($f[$k])) $active++;
}
if(!empty($f['skills'])) $active++;
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
         <a href="oportunidades_view.php"> <button> <img src="../assets/img/icons/briefcase_w.png" alt="Oportunidades"> Oportunidades </button></a>
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
        <button type="button" class="filter" id="toggleFilters" title="Filtros">
               <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#274193"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 5h18M6 12h12M10 19h4"/>
               </svg>
               
               <!--Si hay filtros activos-->
               <?php if($active): ?>
                  <span class="filter_badge"><?= $active ?></span>
               <?php endif; ?>
         </button>
    </div>

         <!-- FILTROS -->
         <form method="GET" class="filters" id="filtersPanel"  hidden>

            <div class="filters_grid">

               <div class="filter_group">
                  <label>Categoría</label>
                  <select name="career_category" id="selCategory" class="filter_select">
                     <option value="0">Todas</option>
                     <?php foreach($options['career_categories'] as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $f['career_category']==$c['id'] ? 'selected' : '' ?>>
                           <?= htmlspecialchars($c['name']) ?>
                        </option>
                     <?php endforeach; ?>
                  </select>
               </div>

               <div class="filter_group">
                  <label>Carrera</label>
                  <select name="career" id="selCareer" class="filter_select">
                     <option value="0">Todas</option>
                     <?php foreach($options['careers'] as $c): ?>
                        <option value="<?= $c['id'] ?>" data-cat="<?= $c['category_id'] ?>"
                           <?= $f['career']==$c['id'] ? 'selected' : '' ?>>
                           <?= htmlspecialchars($c['name']) ?>
                        </option>
                     <?php endforeach; ?>
                  </select>
               </div>

               <div class="filter_group">
                  <label>Nivel</label>
                  <select name="level" class="filter_select">
                     <option value="">Todos</option>
                     <option value="estudiante" <?= $f['level']==='estudiante' ? 'selected' : '' ?>>Estudiante</option>
                     <option value="egresado"   <?= $f['level']==='egresado'   ? 'selected' : '' ?>>Egresado</option>
                  </select>
               </div>

               <div class="filter_group">
                  <label>Edad</label>
                  <div class="range">
                     <input type="number" name="age_min" min="15" class="filter_select"
                            value="<?= $f['age_min'] ?: '' ?>" placeholder="Mín">
                     <input type="number" name="age_max" min="15" class="filter_select"
                            value="<?= $f['age_max'] ?: '' ?>" placeholder="Máx">
                  </div>
               </div>

               <div class="filter_group">
                  <label>Ordenar</label>
                  <select name="order" class="filter_select">
                     <option value="recent" <?= $f['order']==='recent' ? 'selected' : '' ?>>Más recientes</option>
                     <option value="old"    <?= $f['order']==='old'    ? 'selected' : '' ?>>Más antiguos</option>
                  </select>
               </div>
            </div>

            <!-- SKILLS -->
            <details class="filter_skills" <?= !empty($f['skills']) ? 'open' : '' ?>>
               <summary>
                  Skills
                  <span class="skills_count" id="skillsCount"><?= count($f['skills']) ?></span>
               </summary>

               <div class="skills_tools">
                  <input type="text" id="skillSearch" class="filter_select" placeholder="Buscar skill...">
                  <div class="mode_toggle">
                     <label><input type="radio" name="skills_mode" value="all" <?= $f['skills_mode']==='all' ? 'checked' : '' ?>> Todas</label>
                     <label><input type="radio" name="skills_mode" value="any" <?= $f['skills_mode']==='any' ? 'checked' : '' ?>> Cualquiera</label>
                  </div>
               </div>

                              <?php
               $grouped = [];
               foreach($options['skills'] as $s){ $grouped[$s['category'] ?? 'Otras'][] = $s; }
               ?>
               <?php foreach($grouped as $cat => $list): ?>
                  <div class="skill_group">
                     <p class="skill_group_title"><?= htmlspecialchars($cat) ?></p>
                     <div class="chip_list">
                        <?php foreach($list as $s): ?>
                           <label class="chip" data-name="<?= htmlspecialchars(mb_strtolower($s['name'])) ?>">
                              <input type="checkbox" name="skills[]" value="<?= $s['id'] ?>"
                                 <?= in_array($s['id'], $f['skills']) ? 'checked' : '' ?>>
                              <?= htmlspecialchars($s['name']) ?>
                           </label>
                        <?php endforeach; ?>
                     </div>
                  </div>
               <?php endforeach; ?>
            </details>

            <div class="filters_actions">
               <button type="submit" class="btn_filter">Aplicar filtros</button>
               <a href="feed_view.php" class="btn_filter_clear">Limpiar<?= $active ? " ($active)" : "" ?></a>
               <span class="results_count"><?= count($cvs) ?> resultado<?= count($cvs)===1 ? '' : 's' ?></span>
            </div>
         </form>

         <!-- CARDS -->
         <?php if(empty($cvs)): ?>
            <p style="grid-column:1/-1"><?= $active ? 'Ningún CV coincide con los filtros.' : 'No hay CVs aún.' ?></p>
         <?php else: ?>


         <?php foreach($cvs as $cv): ?>
               <div class="card">

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

      </div>
   <script src="../assets/js/filters.js"></script>
 </body>
 </html>