<?php
require_once '../config/database.php';

$nb_matchs = $pdo->query("SELECT COUNT(*) FROM matchs")->fetchColumn();

echo json_encode(['count' => $nb_matchs]);
?>
