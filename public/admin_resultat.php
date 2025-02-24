<?php
session_start();
require_once '../config/database.php';

// Vérification du rôle admin_tournoi
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin_tournoi') {
    header("Location: index.php");
    exit();
}

// Récupérer les matchs sans résultats
$query = "SELECT m.*, 
                 e1.nom AS equipe1, e2.nom AS equipe2 
          FROM matches m
          JOIN equipes e1 ON m.equipe1_id = e1.id
          JOIN equipes e2 ON m.equipe2_id = e2.id
          WHERE m.score_equipe1 IS NULL AND m.score_equipe2 IS NULL
          ORDER BY m.date_match ASC";

$matchs_a_modifier = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Supprimer les matchs terminés depuis plus de 24 heures
$delete_query = "DELETE FROM matches WHERE score_equipe1 IS NOT NULL AND score_equipe2 IS NOT NULL 
                 AND date_match < NOW() - INTERVAL 1 DAY";
$pdo->exec($delete_query);


// Traitement de l'ajout du score
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouter_resultat'])) {
    $match_id = $_POST['match_id'];
    $score_equipe1 = $_POST['score_equipe1'];
    $score_equipe2 = $_POST['score_equipe2'];

    $stmt = $pdo->prepare("UPDATE matches SET score_equipe1 = ?, score_equipe2 = ? WHERE id = ?");
    if ($stmt->execute([$score_equipe1, $score_equipe2, $match_id])) {
        header("Location: admin_resultat.php");
        exit();
    } else {
        $error = "Erreur lors de l'ajout du résultat.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Ajouter Résultats</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.css">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">Ajouter Résultats des Matchs</h2>

    <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th>Match</th>
                <th>Score</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($matchs_a_modifier as $match) : ?>
                <tr>
                    <td><?= htmlspecialchars($match['equipe1']) ?> VS <?= htmlspecialchars($match['equipe2']) ?></td>
                    <td>
                        <form method="post" class="d-flex">
                            <input type="hidden" name="match_id" value="<?= $match['id'] ?>">
                            <input type="number" name="score_equipe1" class="form-control mx-1" required>
                            <span class="mx-1">-</span>
                            <input type="number" name="score_equipe2" class="form-control mx-1" required>
                    </td>
                    <td>
                            <button type="submit" name="ajouter_resultat" class="btn btn-success">Enregistrer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
