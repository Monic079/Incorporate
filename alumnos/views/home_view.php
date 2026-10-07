 <?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nombre = $_SESSION['user_name'] ?? 'Estudiante';
?>
 
 
 <!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incorpórate Alumnos</title>
    <link rel="stylesheet" href="../assets/css/estilo_feed.css">
 </head>
 <body>
      <div class="navbar">
         <img src="../assets/img/icons/logo_inc.png" alt="Logo">
         <a href="home_view.php"> <button class="active">   <img src="../assets/img/icons/home_b.png" alt="Home"> Home </button></a>
         <a href="cvs_view.php"> <button> <img src="../assets/img/icons/briefcase_w.png" alt="Oportunidades"> Cvs </button></a>
         <a href="feed_view.php"><button> <img src="../assets/img/icons/users_w.png" alt="Feed"> Feed </button></a>      
         <a href="../controllers/AuthController.php?action=logout"><button> <img src="../assets/img/icons/exit.png" alt="Feed"> Salir </button></a>      
      </div>
      <div class="content">
         <div class="label">
            <h1>¡Muestra tu talento, <?= htmlspecialchars($nombre) ?>!</h1>
         </div>
         
         <div class="cards_background">
            <!--Titulo y filtros-->
            <div class="header_oportunidades">
               <h2>Talento</h2>
            </div>
            <!--CARD-->
               <p>Incorpórate te acompaña en el proceso profesional </p>
               <p>brindándote competencias que complementan: </p>
               <p>tu formación académica y facilitando tu inserción al mercado laboral.</p>
         </div>
      </div>
 </body>
 </html>