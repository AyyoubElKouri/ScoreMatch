<?php
require 'simple_html_dom.php'; // Inclure la librairie
require_once '../config/database.php'; // Connexion DB

// URL du site à scraper (Remplace par l'URL réelle)
$html = file_get_html('https://www.api-football.com/coverage');

if (!$html) {
    die('❌ Impossible de charger la page');
}

foreach ($html->find('.player-row') as $element) {
    $nom = trim($element->find('.nom-joueur', 0)->plaintext);
    $prenom = trim($element->find('.prenom-joueur', 0)->plaintext);
    $age = trim($element->find('.age-joueur', 0)->plaintext);
    $position = trim($element->find('.poste-joueur', 0)->plaintext);
    $equipe = trim($element->find('.nom-equipe', 0)->plaintext);

    echo "🔎 Joueur : $nom $prenom, Âge : $age, Position : $position, Équipe : $equipe<br>";

    // Vérifier si l'équipe existe
    $stmt = $pdo->prepare("SELECT id FROM equipes WHERE nom = ?");
    $stmt->execute([$equipe]);
    $equipe_id = $stmt->fetchColumn();

    // Si l'équipe n'existe pas, l'ajouter
    if (!$equipe_id) {
        $stmt = $pdo->prepare("INSERT INTO equipes (nom) VALUES (?)");
        $stmt->execute([$equipe]);
        $equipe_id = $pdo->lastInsertId();
    }

    // Insérer le joueur dans la base
    $stmt = $pdo->prepare("INSERT INTO joueurs (nom, prenom, age, position, equipe_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $prenom, $age, $position, $equipe_id]);

    echo "✅ Joueur ajouté : $nom $prenom ($position, $equipe)<br>";
}

echo "<br>✅ Scraping terminé !";
?>
