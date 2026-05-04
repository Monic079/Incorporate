<?php
require_once "../controllers/CvController.php";

$controller = new CvController();
$cv = $controller->show();


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle CV</title>
</head>
<body>

<h1>Detalle del CV</h1>

<!-- ========================= -->
<!-- BOTONES -->
<!-- ========================= -->

<hr>

<!-- ========================= -->
<!-- FOTO -->
<!-- ========================= -->
<?php if(!empty($cv['photo'])): ?>
    <h3>Foto</h3>
    <!--<img src="../assets/img/uploads/<?= $cv['photo'] ?>" width="150">-->
    <img src="../../alumnos/assets/img/uploads/<?= $cv['photo'] ?>" width="150">
<?php endif; ?>

<!-- ========================= -->
<!-- DATOS PERSONALES -->
<!-- ========================= -->
<h3>Datos personales</h3>

<p><strong>Nombre:</strong> <?= $cv['full_name'] ?></p>
<p><strong>Email:</strong> <?= $cv['email'] ?></p>
<p><strong>Teléfono:</strong> <?= $cv['phone'] ?></p>
<p><strong>Género:</strong> <?= $cv['gender'] ?></p>
<p><strong>Edad:</strong> <?= $cv['age'] ?></p>

<!-- ========================= -->
<!-- GENERAL -->
<!-- ========================= -->
<h3>Información general</h3>

<p><strong>Nivel:</strong> <?= $cv['level'] ?></p>

<?php if($cv['level'] == 'estudiante'): ?>
    <p><strong>Año:</strong> <?= $cv['year'] ?></p>
<?php endif; ?>

<p><strong>Resumen:</strong></p>
<p><?= nl2br($cv['resume']) ?></p>

<!-- ========================= -->
<!-- CAREERS -->
<!-- ========================= -->
<h3>Carreras</h3>

<?php if(!empty($cv['careers'])): ?>
    <ul>
        <?php foreach($cv['careers'] as $c): ?>
            <li><?= $c['name'] ?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No hay carreras</p>
<?php endif; ?>

<!-- ========================= -->
<!-- SKILLS -->
<!-- ========================= -->
<h3>Skills</h3>

<?php if(!empty($cv['skills'])): ?>
    <ul>
        <?php foreach($cv['skills'] as $s): ?>
            <li><?= $s['name'] ?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No hay skills</p>
<?php endif; ?>

<!-- ========================= -->
<!-- EXPERIENCIA -->
<!-- ========================= -->
<h3>Experiencia</h3>

<?php if(!empty($cv['experiences'])): ?>
    <?php foreach($cv['experiences'] as $exp): ?>
        <div>
            <p><strong>Empresa:</strong> <?= $exp['company_name'] ?></p>
            <p><strong>Cargo:</strong> <?= $exp['position'] ?></p>
            <p><strong>Inicio:</strong> <?= $exp['start_date'] ?></p>
            <p><strong>Fin:</strong> <?= $exp['end_date'] ?></p>
            <p><strong>Descripción:</strong> <?= $exp['description'] ?></p>
            <hr>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No hay experiencia</p>
<?php endif; ?>

<!-- ========================= -->
<!-- EDUCACIÓN -->
<!-- ========================= -->
<h3>Educación</h3>

<?php if(!empty($cv['education'])): ?>
    <?php foreach($cv['education'] as $edu): ?>
        <div>
            <p><strong>Institución:</strong> <?= $edu['institution'] ?></p>
            <p><strong>Programa:</strong> <?= $edu['program_name'] ?></p>
            <p><strong>Inicio:</strong> <?= $edu['start_date'] ?></p>
            <p><strong>Fin:</strong> <?= $edu['end_date'] ?></p>
            <hr>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No hay educación</p>
<?php endif; ?>

<!-- ========================= -->
<!-- LINKS -->
<!-- ========================= -->
<h3>Links</h3>

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
<?php else: ?>
    <p>No hay links</p>
<?php endif; ?>


</body>
</html>