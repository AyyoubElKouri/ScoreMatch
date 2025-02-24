<?php
session_start();
require_once '../config/database.php';

// Vérification du rôle admin_tournoi
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin_tournoi') {
    header("Location: index.php");
    exit();
}

// Récupérer les matchs
$query = "SELECT m.*, e1.nom AS equipe1, e2.nom AS equipe2, e1.id AS equipe1_id, e2.id AS equipe2_id
          FROM matches m
          JOIN equipes e1 ON m.equipe1_id = e1.id
          JOIN equipes e2 ON m.equipe2_id = e2.id
          ORDER BY m.date_match ASC";
$matchs = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour récupérer les joueurs des deux équipes
function getJoueursParEquipe($pdo, $equipe1_id, $equipe2_id) {
    $stmt = $pdo->prepare("SELECT id, nom FROM joueurs WHERE equipe_id = ? OR equipe_id = ?");
    $stmt->execute([$equipe1_id, $equipe2_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Ajouter un score
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouter_score'])) {
    $match_id = $_POST['match_id'];
    $score_equipe1 = $_POST['score_equipe1'];
    $score_equipe2 = $_POST['score_equipe2'];

    $stmt = $pdo->prepare("UPDATE matches SET score_equipe1 = ?, score_equipe2 = ? WHERE id = ?");
    $stmt->execute([$score_equipe1, $score_equipe2, $match_id]);

    header("Location: admin_resultat.php");
    exit();
}

// Ajouter un but ou un carton
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouter_evenement'])) {
    $match_id = $_POST['match_id'];

    function getEquipeId($pdo, $joueur_id) {
        if (!$joueur_id) {
            return NULL;
        }
        $stmt = $pdo->prepare("SELECT equipe_id FROM joueurs WHERE id = ?");
        $stmt->execute([$joueur_id]);
        return $stmt->fetchColumn() ?: NULL;
    }

    $joueur_but_id = !empty($_POST['joueur_but_id']) ? $_POST['joueur_but_id'] : NULL;
    $minute_but = !empty($_POST['minute_but']) ? $_POST['minute_but'] : NULL;
    $equipe_but_id = getEquipeId($pdo, $joueur_but_id);

    $joueur_carton_id = !empty($_POST['joueur_carton_id']) ? $_POST['joueur_carton_id'] : NULL;
    $minute_carton = !empty($_POST['minute_carton']) ? $_POST['minute_carton'] : NULL;
    $carton = !empty($_POST['carton']) ? $_POST['carton'] : NULL;
    $equipe_carton_id = getEquipeId($pdo, $joueur_carton_id);

    if ($joueur_but_id && $minute_but && $equipe_but_id) {
        $stmt = $pdo->prepare("INSERT INTO match_events (match_id, joueur_id, equipe_id, type_event, minute_but) VALUES (?, ?, ?, 'but', ?)");
        $stmt->execute([$match_id, $joueur_but_id, $equipe_but_id, $minute_but]);
    }

    if ($joueur_carton_id && $minute_carton && $carton && $equipe_carton_id) {
        $stmt = $pdo->prepare("INSERT INTO match_events (match_id, joueur_id, equipe_id, type_event, minute_carton, carton) VALUES (?, ?, ?, 'carton', ?, ?)");
        $stmt->execute([$match_id, $joueur_carton_id, $equipe_carton_id, $minute_carton, $carton]);
    }

    header("Location: admin_resultat.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Résultats des Matchs</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 10px;
        }
        .table-events {
            background: #fff;
            border-radius: 10px;
        }
        .btn-success {
            width: 100%;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">🏆 Gestion des Résultats des Matchs ⚽</h2>

    <?php foreach ($matchs as $match) : ?>
        <div class="card mb-4 shadow">
            <div class="card-header bg-dark text-white">
                <h4><?= htmlspecialchars($match['equipe1']) ?> 🆚 <?= htmlspecialchars($match['equipe2']) ?></h4>
                <small>📅 Match ID: <?= $match['id'] ?> | 🕒 <?= $match['date_match'] ?></small>
            </div>
            <div class="card-body">

                <!-- Ajout du score -->
                <form method="post" class="mb-4">
                    <input type="hidden" name="match_id" value="<?= $match['id'] ?>">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="number" class="form-control" name="score_equipe1" placeholder="Score équipe 1" required>
                        </div>
                        <div class="col-md-4">
                            <input type="number" class="form-control" name="score_equipe2" placeholder="Score équipe 2" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" name="ajouter_score" class="btn btn-primary">📝 Ajouter Score</button>
                        </div>
                    </div>
                </form>

                <h5 class="text-success">⚽ Ajouter un But</h5>
<form method="post">
    <input type="hidden" name="match_id" value="<?= $match['id'] ?>">

    <div class="mb-3">
        <label>Joueur qui a marqué :</label>
        <select class="form-select" name="joueur_but_id">
            <option value="">Sélectionner un joueur</option>

            <!-- Joueurs de l'équipe 1 -->
            <optgroup label="<?= htmlspecialchars($match['equipe1']) ?>">
                <?php
                $joueurs_equipe1 = getJoueursParEquipe($pdo, $match['equipe1_id'], $match['equipe1_id']);
                foreach ($joueurs_equipe1 as $joueur) {
                    echo "<option value='{$joueur['id']}'>{$joueur['nom']}</option>";
                }
                ?>
            </optgroup>

            <!-- Joueurs de l'équipe 2 -->
            <optgroup label="<?= htmlspecialchars($match['equipe2']) ?>">
                <?php
                $joueurs_equipe2 = getJoueursParEquipe($pdo, $match['equipe2_id'], $match['equipe2_id']);
                foreach ($joueurs_equipe2 as $joueur) {
                    echo "<option value='{$joueur['id']}'>{$joueur['nom']}</option>";
                }
                ?>
            </optgroup>
        </select>
    </div>

    <div class="mb-3">
        <label>Minute du but :</label>
        <input type="number" class="form-control" name="minute_but" min="1" max="120" required>
    </div>

    <button type="submit" name="ajouter_evenement" class="btn btn-success">✔ Enregistrer le But</button>
</form>





<h5 class="text-danger">🚨 Ajouter un Carton</h5>
<form method="post">
    <input type="hidden" name="match_id" value="<?= $match['id'] ?>">

    <div class="mb-3">
        <label>Joueur sanctionné :</label>
        <select class="form-select" name="joueur_carton_id">
            <option value="">Sélectionner un joueur</option>

            <!-- Joueurs de l'équipe 1 -->
            <optgroup label="<?= htmlspecialchars($match['equipe1']) ?>">
                <?php
                $joueurs_equipe1 = getJoueursParEquipe($pdo, $match['equipe1_id'], $match['equipe1_id']);
                foreach ($joueurs_equipe1 as $joueur) {
                    echo "<option value='{$joueur['id']}'>{$joueur['nom']}</option>";
                }
                ?>
            </optgroup>

            <!-- Joueurs de l'équipe 2 -->
            <optgroup label="<?= htmlspecialchars($match['equipe2']) ?>">
                <?php
                $joueurs_equipe2 = getJoueursParEquipe($pdo, $match['equipe2_id'], $match['equipe2_id']);
                foreach ($joueurs_equipe2 as $joueur) {
                    echo "<option value='{$joueur['id']}'>{$joueur['nom']}</option>";
                }
                ?>
            </optgroup>
        </select>
    </div>

    <div class="mb-3">
        <label>Minute du carton :</label>
        <input type="number" class="form-control" name="minute_carton" min="1" max="120" required>
    </div>

    <div class="mb-3">
        <label>Type de Carton :</label>
        <select class="form-select" name="carton">
            <option value="jaune">🟨 Jaune</option>
            <option value="rouge">🟥 Rouge</option>
        </select>
    </div>

    <button type="submit" name="ajouter_evenement" class="btn btn-warning">✔ Enregistrer le Carton</button>
</form>




            </div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
