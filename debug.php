<?php
echo "<h1>Débogage de Scent</h1>";
echo "<p>URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Dossier racine: " . __DIR__ . "</p>";
echo "<h2>Chemins importants :</h2>";
echo "<p>app/views/user/login.php existe : " . (file_exists(__DIR__ . '/app/views/user/login.php') ? 'Oui' : 'Non') . "</p>";
echo "<p>app/controllers/UserController.php existe : " . (file_exists(__DIR__ . '/app/controllers/UserController.php') ? 'Oui' : 'Non') . "</p>";
echo "<p>public/index.php existe : " . (file_exists(__DIR__ . '/public/index.php') ? 'Oui' : 'Non') . "</p>";
?>