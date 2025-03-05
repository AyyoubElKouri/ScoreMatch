<?php
session_start();
require_once '../config/database.php';

// Vérification du rôle admin_tournoi
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin_tournoi') {
    header("Location: index.php");
    exit();
}

// Récupérer les matchs avec les équipes
$query = "SELECT m.*, e1.nom AS equipe1, e2.nom AS equipe2, e1.id AS equipe1_id, e2.id AS equipe2_id
          FROM matches m
          JOIN equipes e1 ON m.equipe1_id = e1.id
          JOIN equipes e2 ON m.equipe2_id = e2.id
          WHERE m.statut = 'en cours'
          ORDER BY m.date_match ASC";

$matchs = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour récupérer les joueurs d'une équipe spécifique
function getJoueursParEquipe($pdo, $equipe_id) {
    $stmt = $pdo->prepare("SELECT id, nom FROM joueurs WHERE equipe_id = ?");
    $stmt->execute([$equipe_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Ajouter un score
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouter_score'])) {
    $match_id = $_POST['match_id'];
    $score_equipe1 = $_POST['score_equipe1'];
    $score_equipe2 = $_POST['score_equipe2'];

    $stmt = $pdo->prepare("UPDATE matches SET score_equipe1 = ?, score_equipe2 = ? WHERE id = ?");
    $stmt->execute([$score_equipe1, $score_equipe2, $match_id]);

    echo "<script>setTimeout(() => { window.location.href = 'admin_resultat.php'; }, 500);</script>";
    exit();
}

// Ajouter un événement (but ou carton)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouter_evenement'])) {
    $match_id = $_POST['match_id'];
    $joueur_id = $_POST['joueur_id'] ?? NULL;
    $minute = $_POST['minute'] ?? NULL;
    $type_event = $_POST['type_event'] ?? NULL;
    $carton = $_POST['carton'] ?? NULL;
    $minute_carton = $_POST['minute_carton'] ?? NULL;

    // Vérifier que le joueur est bien sélectionné
    if (!$joueur_id) {
        echo "<script>alert('Veuillez sélectionner un joueur');</script>";
        exit();
    }

    // Récupérer l'équipe du joueur
    $stmt = $pdo->prepare("SELECT equipe_id FROM joueurs WHERE id = ?");
    $stmt->execute([$joueur_id]);
    $equipe_id = $stmt->fetchColumn() ?: NULL;

    if ($joueur_id && $minute && $equipe_id && $type_event) {
        $stmt = $pdo->prepare("INSERT INTO match_events (match_id, joueur_id, equipe_id, type_event, minute_but, carton, minute_carton) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$match_id, $joueur_id, $equipe_id, $type_event, $minute, $carton, $minute_carton]);

        echo "<script>setTimeout(() => { window.location.href = 'admin_resultat.php'; }, 500);</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Résultats</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f4f4;
        }
        .container {
            max-width: 900px;
        }
        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            font-size: 18px;
            font-weight: bold;
        }
        .btn {
            width: 100%;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center mb-4">🏆 Gestion des Résultats ⚽</h2>

    <?php foreach ($matchs as $match) : ?>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <?= htmlspecialchars($match['equipe1']) ?> 🆚 <?= htmlspecialchars($match['equipe2']) ?>
                <br><small>📅 <?= $match['date_match'] ?></small>
            </div>
            <div class="card-body">
                
                <!-- Ajouter un Score -->
                <form method="post" class="row g-2 mb-3">
                    <input type="hidden" name="match_id" value="<?= $match['id'] ?>">
                    <div class="col">
                        <input type="number" class="form-control" name="score_equipe1" placeholder="Score <?= $match['equipe1'] ?>" required>
                    </div>
                    <div class="col">
                        <input type="number" class="form-control" name="score_equipe2" placeholder="Score <?= $match['equipe2'] ?>" required>
                    </div>
                    <div class="col">
                        <button type="submit" name="ajouter_score" class="btn btn-success">✔ Ajouter Score</button>
                    </div>
                </form>

                <!-- Ajouter un Événement -->
                <h5 class="text-info">📋 Ajouter un Événement</h5>
                <form method="post" class="ajouter_evenement">

                    <input type="hidden" name="match_id" value="<?= $match['id'] ?>">

                    <label class="fw-bold">Joueur :</label>
                    <select class="form-select mb-2" name="joueur_id">
                        <optgroup label="<?= htmlspecialchars($match['equipe1']) ?>">
                            <?php foreach (getJoueursParEquipe($pdo, $match['equipe1_id']) as $joueur) : ?>
                                <option value="<?= $joueur['id'] ?>"><?= $joueur['nom'] ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                        <optgroup label="<?= htmlspecialchars($match['equipe2']) ?>">
                            <?php foreach (getJoueursParEquipe($pdo, $match['equipe2_id']) as $joueur) : ?>
                                <option value="<?= $joueur['id'] ?>"><?= $joueur['nom'] ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>

                    <label class="fw-bold">Type d'événement :</label>
                    <select class="form-select mb-2" name="type_event">
                        <option value="but">⚽ But</option>
                        <option value="carton">🚨 Carton</option>
                    </select>

                    <label class="fw-bold">Minute :</label>
                    <input type="number" class="form-control mb-2" name="minute" placeholder="Minute" min="1" max="120" required>

                    <label class="fw-bold">Type de Carton :</label>
                    <select class="form-select mb-2" name="carton">
                        <option value="">Aucun</option>
                        <option value="jaune">🟨 Jaune</option>
                        <option value="rouge">🟥 Rouge</option>
                    </select>

                    <label class="fw-bold">Minute :</label>
                    <input type="number" class="form-control mb-2" name="minute" placeholder="Minute" min="1" max="120" >

                    

                    <button type="submit" name="ajouter_evenement" class="btn btn-warning">Ajouter</button>
                </form>

                <form method="post" class="form_enregistrer">
               <input type="hidden" name="match_id" value="<?= $match['id'] ?>">
               <button type="submit" name="enregistrer_match" class="btn btn-danger mt-3">📌 Enregistrer</button>
           </form>


            </div>
        </div>
    <?php endforeach; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $("form.ajouter_evenement").submit(function(event) {
        event.preventDefault(); // Empêcher le rechargement de la page

        var formData = $(this).serialize(); // Récupérer les données du formulaire

        $.ajax({
            type: "POST",
            url: "ajouter_evenement.php",
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    alert(response.message);
                    location.reload(); // Recharger uniquement les données et non toute la page
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert("Erreur de connexion au serveur.");
            }
        });
    });
});

//script 2 pour marque rune marche comme terminé


$(document).ready(function() {
    $(".form_enregistrer").submit(function(event) {
        event.preventDefault(); // Empêcher le rechargement de la page

        var formData = $(this).serialize(); // Récupérer les données du formulaire
        var matchCard = $(this).closest('.card'); // Sélectionner la carte du match

        $.ajax({
            type: "POST",
            url: "enregistrer_match.php",
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    alert(response.message);
                    matchCard.fadeOut(); // Masquer le match enregistré
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert("Erreur de connexion au serveur.");
            }
        });
    });
});

</script>




</body>
</html>
