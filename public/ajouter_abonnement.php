<?php
session_start();
require_once '../config/database.php'; // Connexion à la base de données

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Vous devez être connecté.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$type = $_POST['type']; // "match", "joueur", ou "equipe"
$id = $_POST['id']; // ID du match, du joueur ou de l'équipe

// Debug
var_dump($_POST); // Pour vérifier les données envoyées
error_log(print_r($_POST, true)); // Log les données

// Vérifier si l'abonnement existe déjà
 if ($type == 'joueur') {
    // Vérifier si l'utilisateur est déjà abonné à ce joueur
    $stmt = $pdo->prepare("SELECT * FROM abonnements WHERE user_id = ? AND joueur_id = ?");
    $stmt->execute([$user_id, $id]);

    if ($stmt->rowCount() == 0) {
        // Ajouter l'abonnement pour le joueur
        $stmt = $pdo->prepare("INSERT INTO abonnements (user_id, joueur_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $id]);

        echo json_encode(['status' => 'success', 'message' => '✅ Vous êtes maintenant abonné au joueur.']);
    } else {
        echo json_encode(['status' => 'warning', 'message' => '⚠️ Vous êtes déjà abonné à ce joueur.']);
    }
} elseif ($type == 'equipe') {
    // Vérifier si l'utilisateur est déjà abonné à cette équipe
    $stmt = $pdo->prepare("SELECT * FROM abonnements WHERE user_id = ? AND equipe_id = ?");
    $stmt->execute([$user_id, $id]);

    if ($stmt->rowCount() == 0) {
        // Ajouter l'abonnement pour l'équipe
        $stmt = $pdo->prepare("INSERT INTO abonnements (user_id, equipe_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $id]);

        echo json_encode(['status' => 'success', 'message' => '✅ Vous êtes maintenant abonné à l\'équipe.']);
    } else {
        echo json_encode(['status' => 'warning', 'message' => '⚠️ Vous êtes déjà abonné à cette équipe.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Type d\'abonnement invalide.']);
}

exit();
