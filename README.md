# Gestion des étudiants (TP PHP)

Application PHP de gestion d'étudiants avec stockage dans `data/etudiants.txt`.

## Installation
1. Copier le dossier `gestion_etudiants` dans `htdocs` (XAMPP/WAMP).
2. Démarrer Apache puis ouvrir `http://localhost/gestion_etudiants/`.
3. Comptes : `admin` / `admin123` ou `prof` / `prof2026`.

## Fichiers
- `functions.php` : constantes, session, `h()`, `connecte()`, `exiger_connexion()`, lecture/écriture du fichier.
- `header.php` / `footer.php` : en-tête avec menu et pied de page.
- `index.php` : connexion (cookie `dernier_user` 7 jours).
- `logout.php` : déconnexion.
- `liste.php` : liste, recherche, tri, statistiques, moyenne par filière.
- `ajouter.php` : ajout / modification avec validation et photo.
- `supprimer.php` : suppression (avec photo).
- `export.php` : export CSV (bonus).
- `test.php` : `phpinfo()` ; `test_fichier.php` : test de l'exercice 3.
- `script.js` : confirmation de suppression et recherche instantanée.
- `style.css` : mise en forme (+ thème sombre).
