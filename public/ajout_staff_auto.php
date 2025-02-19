<?php
require_once '../config/database.php';

// Liste des rôles possibles dans un staff
$roles = ["Entraîneur", "Assistant coach", "Préparateur physique", "Médecin", "Analyste vidéo", "Responsable équipement"];

// Récupérer toutes les équipes
$equipes = $pdo->query("SELECT id FROM equipes")->fetchAll(PDO::FETCH_ASSOC);

foreach ($equipes as $equipe) {
    $equipe_id = $equipe['id'];

    // Vérifier si l'équipe a déjà au moins 4 membres de staff
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM staff WHERE equipe_id = ?");
    $stmt->execute([$equipe_id]);
    $nb_staff = $stmt->fetchColumn();

    if ($nb_staff < 4) {
        $membres_a_ajouter = 4 - $nb_staff;

        for ($i = 0; $i < $membres_a_ajouter; $i++) {
            $nom = "Staff_" . rand(100, 999);
            $prenom = "Membre_" . rand(100, 999);
            $role = $roles[array_rand($roles)];

            // Ajouter le membre du staff à la base de données
            $stmt = $pdo->prepare("INSERT INTO staff (nom, prenom, role, equipe_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $role, $equipe_id]);
        }
    }
}

echo "✅ Staff généré avec succès pour chaque équipe !";
?>
