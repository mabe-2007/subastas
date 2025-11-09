<?php
include("../include/conex.php");
$conex = Conectarse();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Menú con Modal</title>

    <!-- Bootstrap CSS (estable) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- (Opcional) íconos de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <style>
    /* Sidebar similar a tu imagen */
    .sidebar {
        width: 250px;
        background-color: #293e4f; /* tono oscuro similar a la imagen */
        color: #fff;
        min-height: 100vh;
        padding: 20px 15px;
        box-sizing: border-box;
    }
    .sidebar .profile {
        text-align: center;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        margin-bottom: 15px;
    }
    .sidebar .profile img {
        width: 90px;
        height: 90px;
        object-fit: cover;
        border-radius: 50%;
        background: #fff;
        padding: 6px;
    }
    .sidebar .logo-text {
        font-size: 1.25rem;
        font-weight: 700;
        margin-top: 8px;
        color: #f5f7f9;
    }

    /* Menú */
    .sidebar ul.menu {
        list-style: none;
        padding: 0;
        margin: 10px 0;
    }
    .sidebar ul.menu > li {
        margin-bottom: 8px;
    }
    .sidebar ul.menu a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        color: #f5f7f9;
        text-decoration: none;
        border-radius: 6px;
    }
    .sidebar ul.menu a:hover {
        background-color: #2ea0e6; /* azul claro al hover */
        color: #fff;
    }

    /* Submenu (el <ul> que genera tu PHP) */
    .sidebar ul.menu ul {
        list-style: none;
        padding-left: 12px;
        margin-top: 6px;
    }
    .sidebar ul.menu ul li a {
        padding: 8px 10px;
        font-size: 0.95rem;
        opacity: 0.95;
    }

    /* Botón salir abajo */
    .sidebar .logout-btn {
        display: block;
        margin-top: 18px;
        background-color: #2ea0e6;
        color: #fff;
        text-decoration: none;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
    }
    .sidebar .logout-btn:hover {
        background-color: #2287c3;
    }

    /* Adaptación para pantallas pequeñas */
    @media (max-width: 768px) {
        .sidebar { width: 100%; min-height: auto; padding-bottom: 30px; }
    }

    /* ESTILOS NUEVOS PARA QUE SE APLIQUE BIEN EL LAYOUT */
    html, body {
        margin: 0; padding: 0; height: 100%;
    }
    body {
        display: flex;
    }
    .sidebar {
        flex-shrink: 0;
        height: 100vh;
    }
    
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="profile">
            <!-- Cambia la ruta de la imagen por la tuya -->
            <img src="../estilo/img/dd.png" alt="Logo" />
            <div class="logo-text">SubastasOnline</div>
        </div>

        <!-- Aquí va tu PHP exactamente igual (no cambié la estructura) -->
        <ul class="menu">

        <?php
            $sql="SELECT idmenu, nombremenu, ordenmenu FROM menu ORDER BY ordenmenu ASC";
            $result = mysqli_query($conex, $sql);
            while($menu = mysqli_fetch_array($result)){
                echo "<li><a href=\"#\">".$menu['nombremenu']."</a>";

                // Obtener los submenu de este menu
                $sqlsub = "SELECT idsubmenu, nombresubmenu, ordenSubMenu, idmenu FROM submenu WHERE idmenu = " . $menu['idmenu'];
                $resultsub = mysqli_query($conex, $sqlsub);
                if (mysqli_num_rows($resultsub) > 0) {
                    echo "<ul class='submenu'>";
                    while ($submenu = mysqli_fetch_array($resultsub)) {
                        echo "<li><a href=\"#\">".$submenu['nombresubmenu']. "</a></li>";
                    }
                    echo "</ul>";
                }
                echo "</li>";
            }
        ?>

        </ul>

       
    </div>

    <!-- JS de Bootstrap (opcional para modales/funciones) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
