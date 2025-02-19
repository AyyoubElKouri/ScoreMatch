<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin_global') {
    header("Location: index.php");
    exit();
}

require_once '../config/database.php';

// **Initialiser les variables**
$nom = $prenom = $age = $position = $equipe_id = "";
$edit_mode = false;

// **Récupérer les équipes pour la sélection**
$equipes = $pdo->query("SELECT id, nom FROM equipes")->fetchAll(PDO::FETCH_ASSOC);

// **Vérifier si un joueur doit être modifié**
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM joueurs WHERE id = ?");
    $stmt->execute([$id]);
    $joueur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($joueur) {
        $nom = $joueur['nom'];
        $prenom = $joueur['prenom'];
        $age = $joueur['age'];
        $position = $joueur['position'];
        $equipe_id = $joueur['equipe_id'];
    }
}

// **Ajouter ou modifier un joueur**
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $age = $_POST['age'];
    $position = $_POST['position'];
    $equipe_id = $_POST['equipe_id'];

    if (isset($_POST['update'])) {  // **Modifier un joueur**
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE joueurs SET nom = ?, prenom = ?, age = ?, position = ?, equipe_id = ? WHERE id = ?");
        $stmt->execute([$nom, $prenom, $age, $position, $equipe_id, $id]);
    } else {  // **Ajouter un joueur**
        $stmt = $pdo->prepare("INSERT INTO joueurs (nom, prenom, age, position, equipe_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $age, $position, $equipe_id]);
    }

    header("Location: admin_joueurs.php");
    exit();
}

// **Supprimer un joueur**
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM joueurs WHERE id = ?")->execute([$id]);
    header("Location: admin_joueurs.php");
    exit();
}

// **Récupérer tous les joueurs**
$query = "
    SELECT j.id, j.nom, j.prenom, j.age, j.position, e.nom AS equipe
    FROM joueurs j
    JOIN equipes e ON j.equipe_id = e.id
    ORDER BY e.nom, j.nom
";
$stmt = $pdo->query($query);
$joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des Joueurs</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center"><?= $edit_mode ? "Modifier un Joueur" : "Ajouter un Joueur" ?></h2>

    <!-- Formulaire d'Ajout et de Modification -->
    <form method="POST">
        <?php if ($edit_mode) : ?>
            <input type="hidden" name="id" value="<?= $id ?>">
        <?php endif; ?>
        
        <div class="mb-3">
            <label class="form-label">Nom :</label>
            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($nom) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Prénom :</label>
            <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($prenom) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Âge :</label>
            <input type="number" name="age" class="form-control" value="<?= htmlspecialchars($age) ?>" min="16" max="40" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Position :</label>
            <select name="position" class="form-control" required>
                <option value="Gardien" <?= ($position == "Gardien") ? "selected" : "" ?>>Gardien</option>
                <option value="Défenseur" <?= ($position == "Défenseur") ? "selected" : "" ?>>Défenseur</option>
                <option value="Milieu" <?= ($position == "Milieu") ? "selected" : "" ?>>Milieu</option>
                <option value="Attaquant" <?= ($position == "Attaquant") ? "selected" : "" ?>>Attaquant</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Équipe :</label>
            <select name="equipe_id" class="form-control" required>
                <?php foreach ($equipes as $equipe) : ?>
                    <option value="<?= $equipe['id'] ?>" <?= ($equipe_id == $equipe['id']) ? "selected" : "" ?>>
                        <?= htmlspecialchars($equipe['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" name="<?= $edit_mode ? "update" : "add" ?>" class="btn btn-success">
            <?= $edit_mode ? "Mettre à jour" : "Ajouter" ?>
        </button>
        <a href="admin_joueurs.php" class="btn btn-secondary">Annuler</a>
    </form>

    <hr>

    <!-- Liste des joueurs -->
    <h2 class="text-center mt-4">Liste des Joueurs</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Âge</th>
                <th>Position</th>
                <th>Équipe</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($joueurs as $joueur) : ?>
                <tr>
                    <td><?= htmlspecialchars($joueur['nom']) ?></td>
                    <td><?= htmlspecialchars($joueur['prenom']) ?></td>
                    <td><?= htmlspecialchars($joueur['age']) ?></td>
                    <td><?= htmlspecialchars($joueur['position']) ?></td>
                    <td><?= htmlspecialchars($joueur['equipe']) ?></td>
                    <td>
                        <a href="admin_joueurs.php?edit=<?= $joueur['id'] ?>" class="btn btn-primary btn-sm">Modifier</a>
                        <a href="admin_joueurs.php?delete=<?= $joueur['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer ce joueur ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
