<?php

function Conectarse(){
	$servername 	= "localhost";
	$db 			= "subastas";
	$username 		= "sena123";
	$password 		= "sena123";
	$conEctar = mysqli_connect($servername, $username, $password, $db);
	if (!$conEctar) {die("Error de Conexion: " . mysqli_connect_error());	}
	else		{  return $conEctar;										} 
}
?>

