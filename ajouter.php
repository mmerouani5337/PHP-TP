<?php
require_once 'functions.php';
exiger_connexion();

$etudiants = lire_etudiants();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$modif = $id > 0 && isset($etudiants[$id]);

$valeurs = $modif ? $etudiants[$id] : ['nom' => '', 'prenom' => '', 'filiere' => '', 'note' => '', 'photo' => ''];
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valeurs['nom'] = nettoyer($_POST['nom'] ?? '');
    $valeurs['prenom'] = nettoyer($_POST['prenom'] ?? '');
    $valeurs['filiere'] = $_POST['filiere'] ?? '';
    $valeurs['note'] = str_replace(',', '.', trim($_POST['note'] ?? ''));

    if ($valeurs['nom'] === '') $erreurs[] = 'Le nom est obligatoire.';
    if ($valeurs['prenom'] === '') $erreurs[] = 'Le prénom est obligatoire.';
    if (!in_array($valeurs['filiere'], FILIERES, true)) $erreurs[] = 'La filière est invalide.';
    if (!is_numeric($valeurs['note']) || $valeurs['note'] < 0 || $valeurs['note'] > 20) {
        $erreurs[] = 'La note doit être un nombre entre 0 et 20.';
    }

    // Doublon nom + prénom (bonus)
    foreach ($etudiants as $e) {
        if ($e['id'] !== $id && strcasecmp($e['nom'], $valeurs['nom']) === 0 && strcasecmp($e['prenom'], $valeurs['prenom']) === 0) {
            $erreurs[] = 'Cet étudiant existe déjà.';
            break;
        }
    }

    // Photo (facultative)
    $nouvellePhoto = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $f = $_FILES['photo'];
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        if ($f['error'] !== UPLOAD_ERR_OK) {
            $erreurs[] = "Erreur lors de l'envoi de la photo.";
        } elseif (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
            $erreurs[] = 'Extension de photo non autorisée (jpg, jpeg, png).';
        } elseif ($f['size'] > 1024 * 1024) {
            $erreurs[] = 'La photo dépasse 1 Mo.';
        } else {
            $nouvellePhoto = uniqid('etu_') . '.' . $ext;
        }
    }

    if (!$erreurs) {
        if ($nouvellePhoto !== '') {
            move_uploaded_file($_FILES['photo']['tmp_name'], DOSSIER_UPLOADS . $nouvellePhoto);
            if ($modif && $valeurs['photo'] !== '' && file_exists(DOSSIER_UPLOADS . $valeurs['photo'])) {
                unlink(DOSSIER_UPLOADS . $valeurs['photo']);
            }
            $valeurs['photo'] = $nouvellePhoto;
        }
        if (!$modif) {
            $id = $etudiants ? max(array_keys($etudiants)) + 1 : 1;
        }
        $etudiants[$id] = [
            'id' => $id,
            'nom' => strtolower($valeurs['nom']),
            'prenom' => strtolower($valeurs['prenom']),
            'filiere' => $valeurs['filiere'],
            'note' => (float)$valeurs['note'],
            'photo' => $valeurs['photo'],
        ];
        sauver_etudiants($etudiants);
        flash($modif ? 'Étudiant modifié avec succès.' : 'Étudiant ajouté avec succès.');
        header('Location: liste.php');
        exit;
    }
}

$titre = $modif ? "Modifier un étudiant" : "Ajouter un étudiant";
include 'header.php';
?>
<div class="carte">
    <h2><?= h($titre) ?></h2>
    <?php if ($erreurs): ?>
        <ul class="msg erreur">
            <?php foreach ($erreurs as $err): ?><li><?= h($err) ?></li><?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <label>Nom <input type="text" name="nom" value="<?= h($valeurs['nom']) ?>"></label>
        <label>Prénom <input type="text" name="prenom" value="<?= h($valeurs['prenom']) ?>"></label>
        <label>Filière
            <select name="filiere">
                <option value="">-- Choisir --</option>
                <?php foreach (FILIERES as $f): ?>
                    <option value="<?= $f ?>" <?= $valeurs['filiere'] === $f ? 'selected' : '' ?>><?= $f ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Note <input type="text" name="note" value="<?= h((string)$valeurs['note']) ?>"></label>
        <label>Photo (jpg, jpeg, png — 1 Mo max) <input type="file" name="photo" accept=".jpg,.jpeg,.png"></label>
        <?php if ($modif && $valeurs['photo'] !== ''): ?>
            <img class="photo" src="uploads/<?= h($valeurs['photo']) ?>" alt="photo actuelle">
        <?php endif; ?>
        <button type="submit"><?= $modif ? 'Enregistrer' : 'Ajouter' ?></button>
        <a href="liste.php">Annuler</a>
    </form>
</div>
<?php include 'footer.php'; ?>
