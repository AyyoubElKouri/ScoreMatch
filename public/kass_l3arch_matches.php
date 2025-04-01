<?php
session_start();
require_once '../config/database.php';

// Vérification du rôle admin_global
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin_tournoi') {
//     header("Location: index.php");
//     exit();
// }

// Récupérer l'ID du tournoi depuis l'URL
$tournoi_id = isset($_GET['tournoi_id']) ? $_GET['tournoi_id'] : 1;

// Récupérer les matchs du tournoi
$matchs = $pdo->prepare("
    SELECT m.id, m.equipe1_id, m.equipe2_id, m.score_equipe1, m.score_equipe2, 
           e1.nom AS equipe1, e2.nom AS equipe2, m.date_match, m.stade_id, m.arbitre_id, m.statut
    FROM matches m
    JOIN equipes e1 ON m.equipe1_id = e1.id
    JOIN equipes e2 ON m.equipe2_id = e2.id
    WHERE m.tournoi_id = ?
    ORDER BY m.date_match DESC
");
$matchs->execute([$tournoi_id]);
$matchs = $matchs->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour afficher l'arbre des matchs
function buildMatchTree($matches) {
    $treeData = [];
    foreach ($matches as $match) {
        $treeData[] = [
            'id' => $match['id'],
            'parent' => '#', // Définir un parent pour le match
            'text' => $match['equipe1'] . " vs " . $match['equipe2'] . " (" . $match['score_equipe1'] . " - " . $match['score_equipe2'] . ")",
            'state' => ['opened' => true],
            'data' => [
                'equipe1' => $match['equipe1'],
                'equipe2' => $match['equipe2'],
                'score_equipe1' => $match['score_equipe1'],
                'score_equipe2' => $match['score_equipe2']
            ]
        ];
    }
    return json_encode($treeData);
}

$treeData = buildMatchTree($matchs);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Matches Tournoi Kass L3arch</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>
    <style>
        .tournoi-card {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .tournoi-card h3 {
            color: #ff6f00;
        }
        .tournoi-card .date {
            font-size: 14px;
            color: #6c757d;
        }
        .tournoi-card .btn-primary {
            background-color: #ff6f00;
            border: none;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">🏆 Matchs Tournoi Kass L3arch</h2>

    <!-- Affichage de l'arbre des matchs -->
    <div id="matchTree"></div>

    <!-- Modal pour saisir les résultats d'un match -->
    <div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resultModalLabel">Saisir les résultats du match</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="update_results.php">
                    <div class="modal-body">
                        <input type="hidden" name="match_id" id="match_id" />
                        <label for="score_equipe1">Score Équipe 1</label>
                        <input type="number" name="score_equipe1" id="score_equipe1" class="form-control" required />
                        <label for="score_equipe2">Score Équipe 2</label>
                        <input type="number" name="score_equipe2" id="score_equipe2" class="form-control" required />
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Récupérer les données PHP pour afficher les matchs dans un arbre
var treeData = <?php echo $treeData; ?>;

// Initialiser l'arbre avec les données
$(document).ready(function () {
    $('#matchTree').jstree({
        'core': {
            'data': treeData
        }
    });

    // Gérer le clic sur un match pour saisir les résultats
    $('#matchTree').on('select_node.jstree', function (e, data) {
        var matchData = data.node.data;
        $('#match_id').val(matchData.id);
        $('#score_equipe1').val(matchData.score_equipe1);
        $('#score_equipe2').val(matchData.score_equipe2);
        $('#resultModal').modal('show');
    });
});
</script>

</body>
</html>
