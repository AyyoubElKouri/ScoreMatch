<?php
session_start();

//verifier si l'utilisateur est connecté   
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest'; // 'guest' par défaut

require_once '../config/database.php';

// Récupérer les matchs du jour
$today = date('Y-m-d');
$query = "SELECT m.*, 
                 e1.nom AS equipe1, e2.nom AS equipe2, 
                 e1.logo AS logo1, e2.logo AS logo2 
          FROM matches m
          JOIN equipes e1 ON m.equipe1_id = e1.id
          JOIN equipes e2 ON m.equipe2_id = e2.id
          WHERE DATE(m.date_match) = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$today]);
$matchs_du_jour = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>scores_matches</title>

    <!-- Lien Bootstrap CSS -->
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.css">
    <style>
    .match-container {
        display: flex;
        overflow-x: auto; /* Permet le défilement horizontal */
        white-space: nowrap;
        gap: 15px;
        padding: 10px;
        scroll-snap-type: x mandatory;
    }

    .match-card {
        flex: 0 0 auto; /* Empêche les cartes de se réduire */
        width: 310px; /* Taille de chaque carte */
        height: 320px;
        display: flex;
        align-items: center;
        justify-content: center;
        scroll-snap-align: start;
    }

    /* Cache la barre de scroll sur certains navigateurs */
    .match-container::-webkit-scrollbar {
        display: none;
    }

    .match-container {
        -ms-overflow-style: none; /* IE et Edge */
        scrollbar-width: none; /* Firefox */
    }
</style>

</head>
<body>

    <!-- Barre de navigation Bootstrap -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">⚽ Gestion des Matchs</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
               <ul class="navbar-nav ms-auto">
    <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
    <li class="nav-item"><a class="nav-link" href="matches.php">Matchs</a></li>
    <li class="nav-item"><a class="nav-link" href="teams.php">Équipes</a></li>
    <li class="nav-item"><a class="nav-link" href="tournaments.php">Tournois</a></li>
    <li class="nav-item"><a class="nav-link" href="classment.php">Classement</a></li>

    <?php if ($isLoggedIn): ?>
        <?php if ($userRole === 'user'): ?> 
            <li class="nav-item"><a class="nav-link" href="vote_match.php">Voter un match</a></li>
            <li class="nav-item"><a class="nav-link" href="discussion.php">Discuter un match</a></li>
            <li class="nav-item"><a class="nav-link" href="profile.php">Mon Profil</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link btn btn-danger text-white" href="logout.php">Déconnexion</a></li>
    <?php else: ?>
        <li class="nav-item"><a class="nav-link btn btn-primary text-white" href="login.php">Connexion</a></li>
        <li class="nav-item"><a class="nav-link btn btn-success text-white" href="register.php">Inscription</a></li>
    <?php endif; ?>
</ul>

            </div>
        </div>
    </nav>

    <!-- Section principale -->
    <?php if (!empty($matchs_du_jour)): ?>
        <?php if (!empty($matchs_du_jour)): ?>
    <div class="row justify-content-center">
        <?php foreach ($matchs_du_jour as $match): ?>
            <div class="col-md-4">
                <div class="card mb-3 shadow" style="width: 310px; height: 320px; display: flex; align-items: center; justify-content: center;">
                    <div class="card-body text-center">
                        <p class="text-muted"><strong>Heure:</strong> <?= date('H:i', strtotime($match['heure'])) ?></p> 
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="text-center me-3">
                                <img src="<?= htmlspecialchars($match['logo1']) ?>" alt="<?= htmlspecialchars($match['equipe1']) ?>" width="70">
                                <p class="mt-2"><strong><?= htmlspecialchars($match['equipe1']) ?></strong></p>
                            </div>
                            <h3 class="mx-3">VS</h3>
                            <div class="text-center ms-3">
                                <img src="<?= htmlspecialchars($match['logo2']) ?>" alt="<?= htmlspecialchars($match['equipe2']) ?>" width="70">
                                <p class="mt-2"><strong><?= htmlspecialchars($match['equipe2']) ?></strong></p>
                            </div>
                        </div>
                        <a href="match_details.php?id=<?= $match['id'] ?>" class="btn btn-primary btn-sm mt-3">Voir Détails</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="text-muted">Aucun match prévu pour aujourd'hui.</p>
<?php endif; ?>

<?php else: ?>
    <p class="text-muted">Aucun match prévu pour aujourd'hui.</p>
<?php endif; ?>



    <!-- Section Publications -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="mb-4">Publications sur botola inwi pro</h2>
        <div class="row">
            <?php
            // Tableau de publications statiques
            $publications = [
                [
                    "titre" => "Wydad Casablanca en tête du championnat",
                    "contenu" => "Le Wydad continue de dominer la Botola Pro Inwi avec une série impressionnante de victoires.",
                    "image" => "../public/assets/images/waydad.png", // Remplace avec une image valide
                    "lien" => "#"
                ],
                [
                    "titre" => "Raja Casablanca prêt pour le sacre ?",
                    "contenu" => "Le Raja Club Athletic montre une belle forme cette saison et se positionne comme un sérieux prétendant au titre.",
                    "image" => "../public/assets/images/raja.png",
                    "lien" => "#"
                ],
                [
                    "titre" => "RS Berkane impressionne en championnat",
                    "contenu" => "La Renaissance Sportive de Berkane réalise une excellente saison et vise les premières places du classement.",
                    "image" => "../public/assets/images/barkan.png",
                    "lien" => "#"
                ],
                [
                    "titre" => "FUS Rabat surprend ses adversaires",
                    "contenu" => "Le Fath Union Sport de Rabat s'impose comme un sérieux challenger cette saison.",
                    "image" => "../public/assets/images/fus.png",
                    "lien" => "#"
                ],
                [
                    "titre" => "Moghreb Tétouan de retour en force",
                    "contenu" => "Le MAT affiche de belles performances et espère se maintenir dans le haut du tableau.",
                    "image" => "../public/assets/images/tetouan.png",
                    "lien" => "#"
                ],
                [
                    "titre" => "HUSA Agadir vise le haut du classement",
                    "contenu" => "Hassania Agadir mise sur une dynamique positive pour s’imposer cette saison.",
                    "image" => "../public/assets/images/agadir.png",
                    "lien" => "#"
                ]
            ];
            

            // Affichage des publications
            foreach ($publications as $publication) {
                echo '<div class="col-md-4 mb-4">';
                echo '<div class="card">';
                echo '<img src="' . $publication['image'] . '" class="card-img-top" alt="Image">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . htmlspecialchars($publication['titre']) . '</h5>';
                echo '<p class="card-text">' . htmlspecialchars($publication['contenu']) . '</p>';
                echo '<a href="' . $publication['lien'] . '" class="btn btn-primary">Lire la suite</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</section>


    <!-- Section des matchs récents -->
    <section class="py-5">
        <div class="container">
            <h2 class="mb-4">Derniers Matchs</h2>
            <div class="row">
                <?php
                require_once '../config/database.php';

                // Récupérer les derniers matchs
                $query = "SELECT * FROM matchs ORDER BY date_match DESC LIMIT 5"; 

                $result = $pdo->query($query);

                while ($match = $result->fetch(PDO::FETCH_ASSOC)) {
                    echo "<div class='col-md-4 mb-3'>";
                    echo "<div class='card'>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>Match ID: " . $match['id'] . "</h5>";
                    echo "<p class='card-text'>Date: " . $match['date'] . "</p>";
                    echo "<a href='match_details.php?id=" . $match['id'] . "' class='btn btn-primary'>Voir Détails</a>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Pied de page -->
  <!-- Pied de page -->
<footer class="bg-dark text-white pt-5 pb-3">
    <div class="container">
        <div class="row">
            <!-- Section Tournois -->
            <div class="col-md-3">
                <h5 class="text-uppercase">Tournois</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white text-decoration-none">Premier League</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Classement</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Résultats</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Statistiques</a></li>
                </ul>
            </div>

            <!-- Section Matchs -->
            <div class="col-md-3">
                <h5 class="text-uppercase">Matchs</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white text-decoration-none">Matchs en direct</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Matchs d'aujourd'hui</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Matchs à venir</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Matchs d'hier</a></li>
                </ul>
            </div>

            <!-- Section Équipes -->
            <div class="col-md-3">
                <h5 class="text-uppercase">Équipes</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white text-decoration-none">Manchester City</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Arsenal</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Liverpool</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Manchester United</a></li>
                </ul>
            </div>

            <!-- Section Contact & Réseaux sociaux -->
            <div class="col-md-3">
                <h5 class="text-uppercase">Contact</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white text-decoration-none">À propos</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Politique de confidentialité</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Conditions d'utilisation</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Contactez-nous</a></li>
                </ul>
                <div class="mt-3">
                    <a href="#" class="text-white me-2"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-white me-2"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-white me-2"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="text-white me-2"><i class="fab fa-youtube fa-lg"></i></a>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="text-center mt-4">
            <p class="mb-0">&copy; 2025 Gestion des Matchs - Tous droits réservés.</p>
        </div>
    </div>
</footer>

<!-- Font Awesome pour les icônes -->
<script src="https://kit.fontawesome.com/yourkitid.js" crossorigin="anonymous"></script>


    <!-- Lien vers jQuery -->
    <script src="../bootstrap-5.3.3-dist/js/jquery-3.7.1.min.js"></script>
    <!-- Lien vers Popper.js -->
    <script src="../bootstrap-5.3.3-dist/js/popper.min.js"></script>
    <!-- Lien vers Bootstrap JS -->
    <script src="../bootstrap-5.3.3-dist/js/bootstrap.js"></script>

</body>
</html>
