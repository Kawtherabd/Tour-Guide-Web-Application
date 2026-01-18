<?php
// Initialiser les variables de recherche
$rechercheVille = '';
$recherchePays = '';
$rechercheContinent = '';
$rechercheSite = '';
// Charger et parser le fichier XML des villes
$villesXML = new DOMDocument();
$villesXML->load('xml/Villes.xml');

// Initialiser XPath (à un emplacement global pour qu'il soit disponible partout)
$xpath = new DOMXPath($villesXML);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les valeurs de recherche
    $rechercheVille = isset($_POST['ville']) ? $_POST['ville'] : '';
    $recherchePays = isset($_POST['pays']) ? $_POST['pays'] : '';
    $rechercheContinent = isset($_POST['continent']) ? $_POST['continent'] : '';
    $rechercheSite = isset($_POST['site']) ? $_POST['site'] : '';

    // Construire l'expression XPath pour la recherche
    $expressions = [];
    if (!empty($rechercheVille)) {
        $expressions[] = 'contains(@nom, "' . htmlspecialchars($rechercheVille) . '")';
    }
    if (!empty($recherchePays)) { 
        $expressions[] = 'ancestor::pays[contains(@nom, "' . htmlspecialchars($recherchePays) . '")]'; 
    }
    if (!empty($rechercheContinent)) {
        // Rechercher tous les continents dont le nom contient la chaîne saisie
        $continentNos = $xpath->query('/recherche/continents/continent[contains(@nom, "' . htmlspecialchars($rechercheContinent) . '")]/@no');
        $continentFilters = [];
        foreach ($continentNos as $continentNo) {
            $continentFilters[] = '@no="' . $continentNo->nodeValue . '"';
        }
        // Si des continents sont trouvés, les ajouter aux expressions XPath
        if (!empty($continentFilters)) {
            $expressions[] = 'ancestor::pays[' . implode(' or ', $continentFilters) . ']';
        }
    }
    if (!empty($rechercheSite)) {
        $expressions[] = 'sites/site[contains(@nom, "' . htmlspecialchars($rechercheSite) . '")]';
    }

    // Combiner les expressions XPath
    $xpathExpression = '//ville';
    if (!empty($expressions)) {
        $xpathExpression .= '[' . implode(' and ', $expressions) . ']';
    }

    // Effectuer la recherche XPath
    $resultats = $xpath->query($xpathExpression);

    // Générer les résultats HTML
    $resultatsHTML = '<ul id="search-results">';
    $index = 1;
    foreach ($resultats as $ville) {
        $nom = $ville->getAttribute('nom');
        $pays = $ville->parentNode->parentNode->getAttribute('nom');
        $resultatsHTML .= '<li>';
        $resultatsHTML .= $index . '. ';
        $resultatsHTML .= '<a href="Ville.html?ville=' . urlencode($nom) . '">' . htmlspecialchars($nom) . '</a>';
        $resultatsHTML .= ' (' . htmlspecialchars($pays) . ') ';
        
        // Ajout de l'icône de modification
        $resultatsHTML .= '<a href="modifier.php?ville=' . urlencode($nom) . '&pays=' . urlencode($pays) . '" class="edit-icon">✏️</a>';
        
        // Ajout de l'icône de suppression
        $resultatsHTML .= '<a href="?delete=' . urlencode($nom) . '" class="delete-icon" style="color: red;">🚮</a>';

        $resultatsHTML .= '</li>';
        $index++;
    }
    $resultatsHTML .= '</ul>';
}

// Suppression d'une ville via l'icône de suppression
// Variables pour le message
$message = '';
$messageType = '';

if (isset($_GET['delete'])) {
    $villeToDelete = htmlspecialchars($_GET['delete']);
    $villeNode = $xpath->query('//ville[@nom="' . $villeToDelete . '"]')->item(0);

    if ($villeNode) {
        $villeNode->parentNode->removeChild($villeNode);
        $villesXML->save('xml/Villes.xml');
        $message = "La ville \"$villeToDelete\" a été supprimée avec succès.";
        $messageType = 'success';
    } else {
        $message = "La ville \"$villeToDelete\" n'existe pas ou n'a pas pu être trouvée.";
        $messageType = 'error';
    }
}



?>




<?php
// Charger et parser le fichier XML de configuration
$xml = new DOMDocument();
$xml->load('xml/Config.xml');

// Récupérer les éléments du header
$titre = $xml->getElementsByTagName('titre')->item(0)->nodeValue;
$image = $xml->getElementsByTagName('image')->item(0)->nodeValue;

// Récupérer les informations des étudiants
$etudiants = $xml->getElementsByTagName('etudiant');
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Site Touristique</title>
        <link href="https://fonts.googleapis.com/css2?family=Shadows+Into+Light&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="styles/accueil.css">
    </head>
    <body>
        <div id="message-box" 
            class="<?php echo $messageType === 'success' ? 'success' : 'error'; ?>" 
            style="display: <?php echo !empty($message) ? 'block' : 'none'; ?>;">
            <?php echo htmlspecialchars($message); ?>
        </div>


        <div class="container">
            <!-- Sidebar -->
            <div class="sidebar">
                <h2>ETUDIANTES</h2>
                <ul id="student-info">
                    <?php
                    // Afficher les informations des étudiants
                    foreach ($etudiants as $etudiant) {
                        $nom = $etudiant->getElementsByTagName('nom')->item(0)->nodeValue;
                        $prenom = $etudiant->getElementsByTagName('prenom')->item(0)->nodeValue;
                        $specialite = $etudiant->getElementsByTagName('specialite')->item(0)->nodeValue;
                        $section = $etudiant->getElementsByTagName('section')->item(0)->nodeValue;
                        $groupe = $etudiant->getElementsByTagName('groupe')->item(0)->nodeValue;
                        $email = $etudiant->getElementsByTagName('email')->item(0)->nodeValue;
                        echo "<li>";
                        echo "<strong>Nom :</strong> " . htmlspecialchars($nom) . "<br>";
                        echo "<strong>Prénom :</strong> " . htmlspecialchars($prenom) . "<br>";
                        echo "<strong>Spécialité :</strong> " . htmlspecialchars($specialite) . "<br>";
                        echo "<strong>Section :</strong> " . htmlspecialchars($section) . "<br>";
                        echo "<strong>Groupe :</strong> " . htmlspecialchars($groupe) . "<br>";
                        echo "<strong>Mail :</strong> " . htmlspecialchars($email) . "<br>";
                        echo "</li>";
                    }
                    ?>
                </ul>
                <a href="ajouter.php" id="add-city-link">Ajouter Ville</a>
            </div>

            <!-- Main content -->
            <div class="main">
                <header class="header">
                    <img id="header-image" src="<?php echo $image; ?>" alt="Header">
                    <h1 id="site-title"><?php echo $titre; ?></h1>
                </header>
                <div class="search-section">
                    <h2>Recherche</h2>
                    <form id="search-form" method="POST" action="">
                        <input type="text" name="ville" placeholder="Ville" value="<?php echo htmlspecialchars($rechercheVille); ?>">
                        <input type="text" name="pays" placeholder="Pays" value="<?php echo htmlspecialchars($recherchePays); ?>">
                        <input type="text" name="continent" placeholder="Continent" value="<?php echo htmlspecialchars($rechercheContinent); ?>">
                        <input type="text" name="site" placeholder="Site" value="<?php echo htmlspecialchars($rechercheSite); ?>">
                        <button type="submit" id="search-button">Valider</button>
                    </form>
                </div>

                <div class="results-section">
                    <h2>Résultat de recherche</h2>
                    <?php
                    // Afficher les résultats de la recherche
                    if (isset($resultatsHTML)) {
                        echo $resultatsHTML;
                    }
                    ?>
                </div>
            </div>
        </div>
        <script>
            setTimeout(function() {
                const messageBox = document.getElementById('message-box');
                if (messageBox) {
                    messageBox.style.display = 'none';
                }
            }, 5000);
        </script>
    </body>
</html>
