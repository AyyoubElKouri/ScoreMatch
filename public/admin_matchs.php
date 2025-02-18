<?php
session_start();
require_once '../config/database.php';

// Vérification du rôle admin_global
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin_global') {
    header("Location: index.php");
    exit();
}

// Récupération des équipes pour le formulaire
$equipes = $pdo->query("SELECT id, nom FROM equipes ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

// Récupération des matchs pour affichage
$matchs = $pdo->query("
    SELECT m.id, m.equipe1_id, m.equipe2_id, m.stade_id, e1.nom AS equipe1, e2.nom AS equipe2, s.nom AS stade, m.date_match, m.heure
    FROM matches m
    JOIN equipes e1 ON m.equipe1_id = e1.id
    JOIN equipes e2 ON m.equipe2_id = e2.id
    LEFT JOIN stades s ON m.stade_id = s.id
    ORDER BY m.date_match DESC
")->fetchAll(PDO::FETCH_ASSOC);



// Ajouter un match
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouter_match'])) {
  $equipe1_id = $_POST['equipe1'];
  $equipe2_id = $_POST['equipe2'];
  $date_match = $_POST['date_match'];
  $heure_match = $_POST['heure'];
  $stade_id = $_POST['stade'];

  if ($equipe1_id == $equipe2_id) {
      $error = "Une équipe ne peut pas jouer contre elle-même.";
  } elseif (empty($heure_match) || empty($stade_id)) {
      $error = "L'heure et le stade du match sont obligatoires.";
  } else {
      $stmt = $pdo->prepare("INSERT INTO matches (equipe1_id, equipe2_id, date_match, heure, stade_id) VALUES (?, ?, ?, ?, ?)");
      if ($stmt->execute([$equipe1_id, $equipe2_id, $date_match, $heure_match, $stade_id])) {
          header("Location: admin_matchs.php");
          exit();
      } else {
          $error = "Erreur lors de l'ajout du match.";
      }
  }
}


// Supprimer un match
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['supprimer_match'])) {
    $stmt = $pdo->prepare("DELETE FROM matches WHERE id = ?");
    $stmt->execute([$_POST['match_id']]);
    header("Location: admin_matchs.php");
    exit();
}

// Modifier un match

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['modifier_match'])) {
  $match_id = $_POST['match_id'];
  $equipe1_id = $_POST['equipe1'];
  $equipe2_id = $_POST['equipe2'];
  $date_match = $_POST['date_match'];
  $heure_match = $_POST['heure'];
  $stade_id = $_POST['stade'];

  if ($equipe1_id == $equipe2_id) {
      $error = "Une équipe ne peut pas jouer contre elle-même.";
  } elseif (empty($heure_match) || empty($stade_id)) {
      $error = "L'heure et le stade du match sont obligatoires.";
  } else {
      $stmt = $pdo->prepare("
          UPDATE matches 
          SET equipe1_id = ?, equipe2_id = ?, date_match = ?, heure = ?, stade_id = ?
          WHERE id = ?
      ");
      if ($stmt->execute([$equipe1_id, $equipe2_id, $date_match, $heure_match, $stade_id, $match_id])) {
          header("Location: admin_matchs.php");
          exit();
      } else {
          $error = "Erreur lors de la modification du match.";
      }
  }
}


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Matchs</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
  
<div class="container mt-5">
    <h2 class="text-center">Gestion des Matchs</h2>

    <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Bouton Ajouter -->
    <div class="d-flex justify-content-between mb-3">
        <h4>Liste des Matchs</h4>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addMatchModal">+ Ajouter un Match</button>
    </div>

    <!-- Tableau des matchs -->
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Équipe 1</th>
                <th>Équipe 2</th>
                <th>Date & Heure</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($matchs as $index => $match) : ?>
              <tr>
                  <td><?= $index + 1 ?></td>
                  <td><?= htmlspecialchars($match['equipe1']) ?></td>
                  <td><?= htmlspecialchars($match['equipe2']) ?></td>
                  <td><?= htmlspecialchars($match['date_match'] . ' ' . $match['heure']) ?></td>
                  <td>
                       <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editMatchModal<?= $match['id'] ?>">Modifier</button>
                   <form method="post" class="d-inline">
                     <input type="hidden" name="match_id" value="<?= $match['id'] ?>">
                      <button type="submit" name="supprimer_match" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer ce match ?');">Supprimer</button>
                  </form>
             </td>
         </tr>
             
         <!-- Modal Modifier Match -->
    <div class="modal fade" id="editMatchModal<?= $match['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier le Match</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <input type="hidden" name="match_id" value="<?= $match['id'] ?>">

                        <label>Équipe 1</label>
                    <select name="equipe1" class="form-control equipe1" data-match-id="<?= $match['id'] ?>" required>
                                <option value="">-- Sélectionner --</option>
                   <?php foreach ($equipes as $equipe) : ?>
                               <option value="<?= $equipe['id'] ?>" <?= ($equipe['id'] == $match['equipe1_id']) ? 'selected' : '' ?>>
                               <?= htmlspecialchars($equipe['nom']) ?>
                               </option>
                   <?php endforeach; ?>
                   </select>

                   <label>Équipe 2</label>
                 <select name="equipe2" class="form-control equipe2" data-match-id="<?= $match['id'] ?>" required>
                      <option value="">-- Sélectionner --</option>
                     <?php foreach ($equipes as $equipe) : ?>
                        <option value="<?= $equipe['id'] ?>" <?= ($equipe['id'] == $match['equipe2_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($equipe['nom']) ?>
                        </option>
                     <?php endforeach; ?>
               </select>

              <label>Date du Match</label>
                <input type="date" name="date_match" class="form-control" value="<?= $match['date_match'] ?>" required>

              <label>Heure du Match</label>
                 <input type="time" name="heure" class="form-control" value="<?= $match['heure'] ?>" required>

             <label>Stade</label>
                 <select name="stade" class="form-control" required>
                     <option value="">-- Sélectionner --</option>
                 <?php
                   $stades = $pdo->query("SELECT id, nom FROM stades ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
               foreach ($stades as $stade) : ?>
                 <option value="<?= $stade['id'] ?>"><?= htmlspecialchars($stade['nom']) ?></option>
               <?php endforeach; ?>
               </select>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="modifier_match" class="btn btn-success">Enregistrer</button>
                    </div>
                </form>
             </div>
         </div>
      </div>
           <?php endforeach; ?>

        </tbody>
    </table>
</div>


         <!-- Modal Ajouter un match -->
<div class="modal fade" id="addMatchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un Match</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <label>Équipe 1</label>
                    <select name="equipe1" id="equipe1" class="form-control" required>
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($equipes as $equipe) : ?>
                            <option value="<?= $equipe['id'] ?>"><?= htmlspecialchars($equipe['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>Équipe 2</label>
                    <select name="equipe2" id="equipe2" class="form-control" required>
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($equipes as $equipe) : ?>
                            <option value="<?= $equipe['id'] ?>"><?= htmlspecialchars($equipe['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>Date du Match</label>
                    <input type="date" name="date_match" class="form-control" required>

                    <label>Heure du Match</label>
                    <input type="time" name="heure" class="form-control" required>

                    <label>Stade</label>
                    <select name="stade" id="stade" class="form-control" required>
                        <option value="">-- Sélectionner --</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="ajouter_match" class="btn btn-success">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {
    function chargerStades() {
        let equipe1 = document.getElementById("equipe1").value;
        let equipe2 = document.getElementById("equipe2").value;
        let stadeSelect = document.getElementById("stade");

        if (equipe1 && equipe2) {
            fetch("get_stades.php?equipe1=" + equipe1 + "&equipe2=" + equipe2)
                .then(response => response.json())
                .then(data => {
                    stadeSelect.innerHTML = '<option value="">-- Sélectionner --</option>';
                    data.forEach(stade => {
                        stadeSelect.innerHTML += `<option value="${stade.id}">${stade.nom}</option>`;
                    });
                })
                .catch(error => console.error("Erreur de chargement des stades:", error));
        }
    }

    document.getElementById("equipe1").addEventListener("change", chargerStades);
    document.getElementById("equipe2").addEventListener("change", chargerStades);
});
</script>




</body>
</html>
