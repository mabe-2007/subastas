<?php
session_start();

echo json_encode([
  'registrado' => isset($_SESSION['usuarioRegistrado']) && $_SESSION['usuarioRegistrado']
]);
?>