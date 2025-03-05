<?php
session_start();
require_once '../config/database.php'; // Connexion à la base de données

// Vérifier si une recherche a été effectuée
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Requête SQL pour récupérer les joueurs et les équipes selon la recherche
try {
    // Recherche dans les joueurs
    if (!empty($search)) {
        $stmtJoueurs = $pdo->prepare("SELECT id, nom, prenom FROM joueurs WHERE nom LIKE ? OR prenom LIKE ? ORDER BY nom");
        $stmtJoueurs->execute(["%" . $search . "%", "%" . $search . "%"]);
    } else {
        $stmtJoueurs = $pdo->query("SELECT id, nom, prenom FROM joueurs ORDER BY nom");
    }
    $joueurs = $stmtJoueurs->fetchAll(PDO::FETCH_ASSOC);

    // Recherche dans les équipes
    if (!empty($search)) {
        $stmtEquipes = $pdo->prepare("SELECT id, nom FROM equipes WHERE nom LIKE ? ORDER BY nom");
        $stmtEquipes->execute(["%" . $search . "%"]);
    } else {
        $stmtEquipes = $pdo->query("SELECT id, nom FROM equipes ORDER BY nom");
    }
    $equipes = $stmtEquipes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recherche</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.css">
</head>
<body class="<?= isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'dark-mode' : '' ?>">

<!-- Inclure la barre de navigation -->
<?php include 'navbar.php'; ?>

<!-- Section de recherche -->
<section class="py-5">
    <div class="container">
        <h2 class="mb-4 text-center">Rechercher Joueurs & Équipes</h2>

        <!-- Barre de recherche -->
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Rechercher un joueur ou une équipe..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </div>
        </form>

        <div class="row">
            <!-- Liste des joueurs -->
            <div class="col-md-6">
                <h4 class="text-center">Joueurs</h4>
                <ul class="list-group">
                    <?php
                    if (!empty($joueurs)) {
                        foreach ($joueurs as $joueur) {
                            echo '<li class="list-group-item">';
                            echo '<a href="joueur_details.php?id=' . $joueur["id"] . '" class="text-decoration-none">' . htmlspecialchars($joueur["nom"]) . ' ' . htmlspecialchars($joueur["prenom"]) . '</a>';
                            echo '</li>';
                        }
                    } else {
                        echo '<li class="list-group-item text-center">Aucun joueur trouvé.</li>';
                    }
                    ?>
                </ul>
            </div>

            <!-- Liste des équipes -->
            <div class="col-md-6">
                <h4 class="text-center">Équipes</h4>
                <ul class="list-group">
                    <?php
                    if (!empty($equipes)) {
                        foreach ($equipes as $equipe) {
                            echo '<li class="list-group-item">';
                            echo '<a href="team_details.php?id=' . $equipe["id"] . '" class="text-decoration-none">' . htmlspecialchars($equipe["nom"]) . '</a>';
                            echo '</li>';
                        }
                    } else {
                        echo '<li class="list-group-item text-center">Aucune équipe trouvée.</li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</section>

<script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
