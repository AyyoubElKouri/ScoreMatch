<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Gestion des Matchs</title>
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Lien vers le CSS -->
</head>
<body>

    <!-- Barre de navigation -->
    <nav>
        <div class="logo">⚽ Gestion des Matchs</div>
        <ul>
            <li><a href="index.php">Accueil</a></li>
            <li><a href="matches.php">Matchs</a></li>
            <li><a href="teams.php">Équipes</a></li>
            <li><a href="tournaments.php">Tournois</a></li>
            <?php if ($isLoggedIn): ?>
                <li><a href="dashboard.php">Tableau de Bord</a></li>
                <li><a href="logout.php" class="btn-logout">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="login.php" class="btn-login">Connexion</a></li>
                <li><a href="register.php" class="btn-signup">Inscription</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Section principale -->
    <header class="hero">
        <h1>Bienvenue sur l'application de gestion des matchs de football</h1>
        <p>Suivez vos équipes préférées, consultez les matchs et recevez des notifications en temps réel !</p>
        <?php if (!$isLoggedIn): ?>
            <a href="register.php" class="btn-main">Créer un compte</a>
        <?php endif; ?>
    </header>

    <!-- Section des matchs récents -->
    <section class="recent-matches">
        <h2>Derniers Matchs</h2>
        <div class="match-list">
            <?php
            require_once '../config/database.php';

            // Récupérer les derniers matchs
            $query = "SELECT * FROM matchs ORDER BY date DESC LIMIT 5";
            $result = $pdo->query($query);

            while ($match = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='match'>";
                echo "<h3>Match ID: " . $match['id'] . "</h3>";
                echo "<p>Date: " . $match['date'] . "</p>";
                echo "<a href='match_details.php?id=" . $match['id'] . "' class='btn-details'>Voir Détails</a>";
                echo "</div>";
            }
            ?>
        </div>
    </section>

    <!-- Pied de page -->
    <footer>
        <p>&copy; 2024 Gestion des Matchs - Tous droits réservés.</p>
    </footer>

</body>
</html>
