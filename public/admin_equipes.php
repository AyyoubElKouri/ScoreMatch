<?php
session_start();
require_once '../config/database.php';

// Vérification du rôle admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin_global') {
    header("Location: index.php");
    exit();
}

// Récupérer les équipes
$equipes = $pdo->query("SELECT * FROM equipes ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

// Ajouter une équipe
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouter_equipe'])) {
    $nom = $_POST['nom'];
    $entraineur = $_POST['entraineur'];
    $description = $_POST['description'];
    $logo = $_POST['logo'];  // Stocke le chemin du logo

    $stmt = $pdo->prepare("INSERT INTO equipes (nom, entraineur, description, logo) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nom, $entraineur, $description, $logo]);

    header("Location: admin_equipes.php");
    exit();
}

// Supprimer une équipe
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['supprimer_equipe'])) {
    $stmt = $pdo->prepare("DELETE FROM equipes WHERE id = ?");
    $stmt->execute([$_POST['equipe_id']]);
    header("Location: admin_equipes.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Équipes</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Gestion des Équipes</h2>

    <!-- Bouton Ajouter -->
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addEquipeModal">+ Ajouter une Équipe</button>

    <!-- Tableau des équipes -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Logo</th>
                <th>Nom</th>
                <th>Entraîneur</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equipes as $index => $equipe) : ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><img src="<?= htmlspecialchars($equipe['logo']) ?>" width="50" height="50" alt="Logo"></td>
                    <td><?= htmlspecialchars($equipe['nom']) ?></td>
                    <td><?= htmlspecialchars($equipe['entraineur']) ?></td>
                    <td><?= htmlspecialchars($equipe['description']) ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editEquipeModal<?= $equipe['id'] ?>">Modifier</button>

                        <form method="post" class="d-inline">
                            <input type="hidden" name="equipe_id" value="<?= $equipe['id'] ?>">
                            <button type="submit" name="supprimer_equipe" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette équipe ?');">Supprimer</button>
                        </form>
                    </td>
                </tr>

                <!-- Modal Modifier -->
                <div class="modal fade" id="editEquipeModal<?= $equipe['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Modifier l'Équipe</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="post">
                                <div class="modal-body">
                                    <input type="hidden" name="equipe_id" value="<?= $equipe['id'] ?>">
                                    <label>Nom</label>
                                    <input type="text" name="nom" value="<?= htmlspecialchars($equipe['nom']) ?>" class="form-control" required>

                                    <label>Entraîneur</label>
                                    <input type="text" name="entraineur" value="<?= htmlspecialchars($equipe['entraineur']) ?>" class="form-control" required>

                                    <label>Description</label>
                                    <textarea name="description" class="form-control"><?= htmlspecialchars($equipe['description']) ?></textarea>

                                    <label>Logo</label>
                                    <input type="text" name="logo" value="<?= htmlspecialchars($equipe['logo']) ?>" class="form-control">
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="modifier_equipe" class="btn btn-primary">Enregistrer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Ajouter -->
<div class="modal fade" id="addEquipeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter une Équipe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <label>Nom</label>
                    <input type="text" name="nom" class="form-control" required>

                    <label>Entraîneur</label>
                    <input type="text" name="entraineur" class="form-control" required>

                    <label>Description</label>
                    <textarea name="description" class="form-control"></textarea>

                    <label>Logo</label>
                    <input type="text" name="logo" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="submit" name="ajouter_equipe" class="btn btn-success">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
