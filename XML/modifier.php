<?php
// Variables initiales
$ville = $pays = $continent = $descriptif = '';
$sites = $hotels = $restaurants = $gares = $aeroports = [];
$xmlFile = '';

// Vérifier si une ville est spécifiée via GET
if (isset($_GET['ville'])) {
    $ville = htmlspecialchars($_GET['ville']);
    $xmlFile = "xml/{$ville}.xml";

    if (file_exists($xmlFile)) {
        $xml = new DOMDocument();
        $xml->load($xmlFile);
        $root = $xml->documentElement;

        // Extraire les informations du fichier XML
        $pays = $root->getAttribute('pays');
        $continent = $root->getAttribute('continent');
        $descriptifNode = $root->getElementsByTagName('descriptif')->item(0);
        $descriptif = $descriptifNode ? $descriptifNode->textContent : '';

        // Extraire les sites
        foreach ($root->getElementsByTagName('site') as $site) {
            $sites[] = $site->getAttribute('nom');
        }

        // Extraire les hôtels
        foreach ($root->getElementsByTagName('hotel') as $hotel) {
            $hotels[] = $hotel->textContent;
        }

        // Extraire les restaurants
        foreach ($root->getElementsByTagName('restaurant') as $restaurant) {
            $restaurants[] = $restaurant->textContent;
        }

        // Extraire les gares
        foreach ($root->getElementsByTagName('gare') as $gare) {
            $gares[] = $gare->textContent;
        }

        // Extraire les aéroports
        foreach ($root->getElementsByTagName('aeroport') as $aeroport) {
            $aeroports[] = $aeroport->textContent;
        }
    }
}

// Traitement des modifications
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ville = htmlspecialchars($_POST['ville']);
    $pays = htmlspecialchars($_POST['pays']);
    $continent = htmlspecialchars($_POST['continent']);
    $descriptif = htmlspecialchars($_POST['descriptif']);
    $sites = isset($_POST['sites']) ? $_POST['sites'] : [];
    $hotels = isset($_POST['hotels']) ? $_POST['hotels'] : [];
    $restaurants = isset($_POST['restaurants']) ? $_POST['restaurants'] : [];
    $gares = isset($_POST['gares']) ? $_POST['gares'] : [];
    $aeroports = isset($_POST['aeroports']) ? $_POST['aeroports'] : [];

    // Vérifier que le fichier XML existe
    if (file_exists($xmlFile)) {
        $xml = new DOMDocument();
        $xml->load($xmlFile);
        $root = $xml->documentElement;

        // Mettre à jour les attributs
        $root->setAttribute('pays', $pays);
        $root->setAttribute('continent', $continent);

        // Mettre à jour le descriptif
        $descriptifNode = $root->getElementsByTagName('descriptif')->item(0);
        if ($descriptifNode) {
            $descriptifNode->textContent = $descriptif;
        }

        // Fonction pour mettre à jour les éléments du XML
        function updateElements($xml, $root, $tagName, $data) {
            $node = $root->getElementsByTagName($tagName . 's')->item(0);
            if ($node) {
                while ($node->hasChildNodes()) {
                    $node->removeChild($node->firstChild);
                }
                foreach ($data as $item) {
                    $newNode = $xml->createElement($tagName, htmlspecialchars($item));
                    $node->appendChild($newNode);
                }
            }
        }

        // Mettre à jour les sections du XML
        updateElements($xml, $root, 'site', $sites);
        updateElements($xml, $root, 'hotel', $hotels);
        updateElements($xml, $root, 'restaurant', $restaurants);
        updateElements($xml, $root, 'gare', $gares);
        updateElements($xml, $root, 'aeroport', $aeroports);

        // Sauvegarder les modifications
        $xml->save($xmlFile);
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            const alertBox = document.getElementById('success-alert');
            alertBox.style.display = 'block';
            setTimeout(() => {
                alertBox.style.display = 'none';
            }, 5000); // Cache l'alerte après 5 secondes
        });
        </script>";
    } else {
        echo "Le fichier XML de la ville n'existe pas.";
    }
}
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de Ville Étendu</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/modifier.css">
    
</head>
<style> /* allerte de succé d'enregestrement de ville */
.alert {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #4CAF50; /* Couleur de fond verte */
    color: white; /* Couleur du texte */
    padding: 20px; /* Espacement interne */
    border-radius: 8px; /* Coins arrondis */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); /* Ombre */
    text-align: center;
    font-size: 18px; /* Taille du texte */
    z-index: 1000; /* Toujours au-dessus */
}
</style>
<body>
<a href="accueil.php" class="back-button">Revenir à l'accueil</a>

    <h1>Formulaire Étendu de Ville</h1>
    <div id="success-alert" class="alert" style="display: none;">
      Les informations de la ville ont été modifiées avec succès.
    </div>
    <form id="city-form" method="post" enctype="multipart/form-data">
        <!-- Informations principales -->
        <label for="ville">Ville:</label>
<input type="text" id="ville" name="ville" required minlength="2" value="<?php echo htmlspecialchars($ville); ?>"><br><br>

<label for="pays">Pays:</label>
<input type="text" id="pays" name="pays" required minlength="2" value="<?php echo htmlspecialchars($pays); ?>"><br><br>

<label for="continent">Continent:</label>
<select id="continent" name="continent" required>
    <option value="Afrique" <?php if ($continent == "Afrique") echo "selected"; ?>>Afrique</option>
    <option value="Europe" <?php if ($continent == "Europe") echo "selected"; ?>>Europe</option>
    <option value="Asie" <?php if ($continent == "Asie") echo "selected"; ?>>Asie</option>
    <option value="Amérique" <?php if ($continent == "Amérique") echo "selected"; ?>>Amérique</option>
    <option value="Océanie" <?php if ($continent == "Océanie") echo "selected"; ?>>Océanie</option>
</select><br><br>

<label for="descriptif">Descriptif:</label>
<textarea id="descriptif" name="descriptif" required minlength="10"><?php echo htmlspecialchars($descriptif); ?></textarea><br><br>


        <!-- Sites -->
        <label for="sites">Sites:</label>
<div id="sites">
    <?php
    foreach ($sites as $site) {
        echo "<input type='text' name='sites[]' value='" . htmlspecialchars($site) . "'><br>";
    }
    ?>
</div>
<button type="button" id="add-site-btn">Ajouter un site</button><br><br>


         <!-- Photos -->
         <label for="photos">Photos:</label>
        <input type="file" name="photos[]" accept="image/*" multiple><br><br>

        <!-- Hôtels -->
        <label for="hotels">Hôtels:</label>
        <div id="hotels">
            <?php
            if (!empty($hotels)) {
                foreach ($hotels as $hotel) {
                    echo '<input type="text" name="hotels[]" value="' . htmlspecialchars($hotel) . '" placeholder="Nom de l\'hôtel"><br>';
                }
            } else {
                echo '<input type="text" name="hotels[]" placeholder="Nom de l\'hôtel"><br>';
            }
            ?>
        </div>
        <button type="button" id="add-hotel-btn">Ajouter un hôtel</button><br><br>

        <!-- Restaurants -->
        <label for="restaurants">Restaurants:</label>
        <div id="restaurants">
            <?php
            if (!empty($restaurants)) {
                foreach ($restaurants as $restaurant) {
                    echo '<input type="text" name="restaurants[]" value="' . htmlspecialchars($restaurant) . '" placeholder="Nom du restaurant"><br>';
                }
            } else {
                echo '<input type="text" name="restaurants[]" placeholder="Nom du restaurant"><br>';
            }
            ?>
        </div>
        <button type="button" id="add-restaurant-btn">Ajouter un restaurant</button><br><br>

        <!-- Gares -->
        <label for="gares">Gares:</label>
        <div id="gares">
            <?php
            if (!empty($gares)) {
                foreach ($gares as $gare) {
                    echo '<input type="text" name="gares[]" value="' . htmlspecialchars($gare) . '" placeholder="Nom de la gare"><br>';
                }
            } else {
                echo '<input type="text" name="gares[]" placeholder="Nom de la gare"><br>';
            }
            ?>
        </div>
        <button type="button" id="add-station-btn">Ajouter une gare</button><br><br>

        <!-- Aéroports -->
        <label for="aeroports">Aéroports:</label>
        <div id="aeroports">
            <?php
            if (!empty($aeroports)) {
                foreach ($aeroports as $aeroport) {
                    echo '<input type="text" name="aeroports[]" value="' . htmlspecialchars($aeroport) . '" placeholder="Nom de l\'aéroport"><br>';
                }
            } else {
                echo '<input type="text" name="aeroports[]" placeholder="Nom de l\'aéroport"><br>';
            }
            ?>
        </div>
        <button type="button" id="add-airport-btn">Ajouter un aéroport</button><br><br>

        <!-- Formulaire de soumission -->
        <button type="submit">Enregistrer</button>
    </form>

    <script>
        function ajouterChamp(containerId, placeholder, inputType = 'text') {
            const container = document.getElementById(containerId);
            const input = document.createElement('input');
            input.type = inputType;
            input.name = containerId + '[]';
            input.placeholder = placeholder;
            container.appendChild(document.createElement('br'));
            container.appendChild(input);
        }

        document.getElementById('add-site-btn').addEventListener('click', function() {
            ajouterChamp('sites', 'Nom du site');
        });

        document.getElementById('add-hotel-btn').addEventListener('click', function() {
            ajouterChamp('hotels', 'Nom de l\'hôtel');
        });

        document.getElementById('add-restaurant-btn').addEventListener('click', function() {
            ajouterChamp('restaurants', 'Nom du restaurant');
        });

        document.getElementById('add-station-btn').addEventListener('click', function() {
            ajouterChamp('gares', 'Nom de la gare');
        });

        document.getElementById('add-airport-btn').addEventListener('click', function() {
            ajouterChamp('aeroports', 'Nom de l\'aéroport');
        });
    </script>
</body>
</html>
