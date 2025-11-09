<?php
include("../include/conex.php");
$conexion=  Conectarse();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $('ul li:has(ul)').hover(function(e) {
            $(this).find('ul').css({display: "block"});
        },
        function(e) {
            $(this).find('ul').css({display: "none"});
        });
    });
</script>
<style>
    ul.menu {
 float:left;
 display:block;
 margin-top: 38px;
 list-style-type:none;
 }
 .menu li {
 line-height:18px;
 font-size:13px;
 position:relative;
 float:left;
 }
 .menu li a {
 color: #000;
 text-transform:uppercase;
 padding: 5px 20px;
 text-decoration:none;
 }
 .menu li a:hover {
 background: #9c0101;
 color: white;
 }
 .menu li ul {
 display:none;
 position:absolute;
 top:20px;
 width: 240px;
 background-color: #f4f4f4;
 padding:0;
 list-style-type:none;
 }
 .menu li ul li {
 width: 200px;
 border: 1px solid #9c0101;
 border-top:none;
 padding: 10px 20px;
 }
 .menu li ul li:first-child {
 border-top: 1px solid #9c0101;
 }
.menu li ul li a {
 width: 240px;
 margin: 0;
 padding:0;
 }
.menu li ul li a:hover {
 width: 240px;
 margin: 0;
 color: #9c0101;
 background:none;
 }
</style>
</head>
<body>
    <ul class="menu">
    <li><a href="#"></a>
    <?php
        $sql="SELECT idMenu, nombreMenu, ordenMenu FROM menu ORDER BY ordenMenu ASC ";
        $result = mysqli_query($conexion, $sql);
        while($menu = mysqli_fetch_array($result)){
            echo "<li><a href=\"#\">".$menu['nombreMenu']."</a></li>";
        }
    ?>
    </li>
    </ul> 
<!-- <ul class="menu">
    
     <li><a href="#">Servicios</a>
        <ul>
             <li><a href="#">Jubilaci&oacute;n</a></li>
             
         </ul>
    </li>
    
</ul> -->
</body>
</html>