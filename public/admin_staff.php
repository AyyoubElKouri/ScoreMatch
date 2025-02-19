<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin_global') {
    header("Location: index.php");
    exit();
}

require_once '../config/database.php';

// **Initialisation des variables**
$nom = $prenom = $role = $equipe_id = "";
$edit_mode = false;

// **Récupérer les équipes pour la sélection**
$equipes = $pdo->query("SELECT id, nom FROM equipes")->fetchAll(PDO::FETCH_ASSOC);
$roles = ["Entraîneur", "Assistant coach", "Préparateur physique", "Médecin", "Analyste vidéo", "Responsable équipement"];

// **Vérifier si un staff doit être modifié**
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM staff WHERE id = ?");
    $stmt->execute([$id]);
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($staff) {
        $nom = $staff['nom'];
        $prenom = $staff['prenom'];
        $role = $staff['role'];
        $equipe_id = $staff['equipe_id'];
    }
}

// **Ajouter ou modifier un staff**
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $role = $_POST['role'];
    $equipe_id = $_POST['equipe_id'];

    if (isset($_POST['update'])) {  // **Modifier un staff**
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE staff SET nom = ?, prenom = ?, role = ?, equipe_id = ? WHERE id = ?");
        $stmt->execute([$nom, $prenom, $role, $equipe_id, $id]);
    } else {  // **Ajouter un staff**
        $stmt = $pdo->prepare("INSERT INTO staff (nom, prenom, role, equipe_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $role, $equipe_id]);
    }

    header("Location: admin_staff.php");
    exit();
}

// **Supprimer un staff**
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM staff WHERE id = ?")->execute([$id]);
    header("Location: admin_staff.php");
    exit();
}

// **Récupérer tous les membres du staff**
$query = "
    SELECT s.id, s.nom, s.prenom, s.role, e.nom AS equipe
    FROM staff s
    JOIN equipes e ON s.equipe_id = e.id
    ORDER BY e.nom, s.role
";
$stmt = $pdo->query($query);
$staffs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion du Staff</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center"><?= $edit_mode ? "Modifier un Membre du Staff" : "Ajouter un Membre du Staff" ?></h2>

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
            <label class="form-label">Rôle :</label>
            <select name="role" class="form-control" required>
                <?php foreach ($roles as $r) : ?>
                    <option value="<?= $r ?>" <?= ($role == $r) ? "selected" : "" ?>><?= $r ?></option>
                <?php endforeach; ?>
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
        <a href="admin_staff.php" class="btn btn-secondary">Annuler</a>
    </form>

</div>
</body>
</html>
