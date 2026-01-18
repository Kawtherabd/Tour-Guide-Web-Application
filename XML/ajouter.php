<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $ville = $_POST['ville'];
    $pays = $_POST['pays'];
    $continent = $_POST['continent'];
    $descriptif = $_POST['descriptif'];
    $sites = $_POST['sites'] ?? [];
    $photos = $_FILES['photos'] ?? [];
    $hotels = $_POST['hotels'] ?? [];
    $restaurants = $_POST['restaurants'] ?? [];
    $gares = $_POST['gares'] ?? [];
    $aeroports = $_POST['aeroports'] ?? [];

    // Vérifier que le dossier "xml" existe, sinon le créer
    $xmlDirectory = 'xml';
    if (!is_dir($xmlDirectory)) {
        mkdir($xmlDirectory, 0777, true);
    }

    // Création du fichier individuel pour la ville
    $nomVilleFile = "{$xmlDirectory}/{$ville}.xml";
    $nomVilleXml = new DOMDocument('1.0', 'UTF-8');
    $nomVilleXml->formatOutput = true;

    // Ajouter la directive de style XSL
    $processingInstruction = $nomVilleXml->createProcessingInstruction(
        'xml-stylesheet',
        'type="text/xsl" href="xsl/Ville.xsl"'
    );
    $nomVilleXml->appendChild($processingInstruction);

    // Racine du fichier NomVille.xml
    $root = $nomVilleXml->createElement('ville');
    $root->setAttribute('nom', $ville);
    $root->setAttribute('continent', $continent);
    $nomVilleXml->appendChild($root);

    // Ajouter les informations au fichier individuel
    $root->appendChild($nomVilleXml->createElement('descriptif', $descriptif));

    // Ajouter les sites
    $sitesNode = $nomVilleXml->createElement('sites');
    foreach ($sites as $index => $siteName) {
        $siteNode = $nomVilleXml->createElement('site');
        $siteNode->setAttribute('nom', htmlspecialchars($siteName));
        
        // Vérification si une photo a été téléchargée pour ce site
        if (isset($photos['name'][$index]) && $photos['error'][$index] === 0) {
            $photoPath = 'images/' . basename($photos['name'][$index]);
            move_uploaded_file($photos['tmp_name'][$index], $photoPath);  // Déplacer la photo dans le dossier "images"
            $siteNode->setAttribute('photo', $photoPath);
        }

        $sitesNode->appendChild($siteNode);
    }
    $root->appendChild($sitesNode);

    // Ajouter les hôtels
    $hotelsNode = $nomVilleXml->createElement('hotels');
    foreach ($hotels as $hotel) {
        $hotelsNode->appendChild($nomVilleXml->createElement('hotel', htmlspecialchars($hotel)));
    }
    $root->appendChild($hotelsNode);

    // Ajouter les restaurants
    $restaurantsNode = $nomVilleXml->createElement('restaurants');
    foreach ($restaurants as $restaurant) {
        $restaurantsNode->appendChild($nomVilleXml->createElement('restaurant', htmlspecialchars($restaurant)));
    }
    $root->appendChild($restaurantsNode);

    // Ajouter les gares
    $garesNode = $nomVilleXml->createElement('gares');
    foreach ($gares as $gare) {
        $garesNode->appendChild($nomVilleXml->createElement('gare', htmlspecialchars($gare)));
    }
    $root->appendChild($garesNode);

    // Ajouter les aéroports
    $aeroportsNode = $nomVilleXml->createElement('aeroports');
    foreach ($aeroports as $aeroport) {
        $aeroportsNode->appendChild($nomVilleXml->createElement('aeroport', htmlspecialchars($aeroport)));
    }
    $root->appendChild($aeroportsNode);

    // Sauvegarder le fichier NomVille.xml
    $nomVilleXml->save($nomVilleFile);

    // Mise à jour du fichier Villes.xml
    $villesFile = "{$xmlDirectory}/Villes.xml";

    // Charger ou créer le fichier Villes.xml
    if (file_exists($villesFile)) {
        $villesXml = new DOMDocument('1.0', 'UTF-8');
        $villesXml->formatOutput = true;
        $villesXml->load($villesFile);
        $root = $villesXml->documentElement;
    } else {
        $villesXml = new DOMDocument('1.0', 'UTF-8');
        $villesXml->formatOutput = true;
        $root = $villesXml->createElement('villes');
        $villesXml->appendChild($root);
    }

    // Rechercher si le pays existe déjà
    $paysNode = null;
    foreach ($root->getElementsByTagName('pays') as $existingPays) {
        if ($existingPays->getAttribute('nom') === $pays) {
            $paysNode = $existingPays;
            break;
        }
    }

    // Si le pays n'existe pas, le créer
    if (!$paysNode) {
        $paysNode = $villesXml->createElement('pays');
        $paysNode->setAttribute('no', 'C' . rand(1, 100));
        $paysNode->setAttribute('nom', $pays);

        $villesNode = $villesXml->createElement('villes');
        $paysNode->appendChild($villesNode);

        $root->appendChild($paysNode);
    } else {
        $villesNode = $paysNode->getElementsByTagName('villes')->item(0);
    }

    // Ajouter la ville au pays
    $villeNode = $villesXml->createElement('ville');
    $villeNode->setAttribute('nom', $ville);

    // Ajouter les sites dans Villes.xml
    $sitesNode = $villesXml->createElement('sites');
    foreach ($sites as $index => $siteName) {
        $siteNode = $villesXml->createElement('site');
        $siteNode->setAttribute('nom', htmlspecialchars($siteName));
        
        // Si une photo existe, ajouter son chemin
        if (isset($photos['name'][$index]) && $photos['error'][$index] === 0) {
            $photoPath = 'images/' . basename($photos['name'][$index]);
            $siteNode->setAttribute('photo', $photoPath);
        }
        $sitesNode->appendChild($siteNode);
    }
    $villeNode->appendChild($sitesNode);

    $villesNode->appendChild($villeNode);

    // Sauvegarder le fichier Villes.xml
    $villesXml->save($villesFile);

    echo "<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alertBox = document.getElementById('success-alert');
        alertBox.style.display = 'block';
        setTimeout(() => {
            alertBox.style.display = 'none';
        }, 5000); // Cache l'alerte après 5 secondes
    });
    </script>";}



// Variables initiales
$ville = $pays = $continent = $descriptif = '';
$sites = $hotels = $restaurants = $gares = $aeroports = [];
$xmlFile = ''; // Initialiser la variable $xmlFile

// Vérifier si une ville est spécifiée via GET
if (isset($_GET['ville'])) {
    $ville = htmlspecialchars($_GET['ville']);

    // Charger le fichier XML de la ville
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

// Traitement du formulaire pour la mise à jour des données
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

    // Construire le chemin du fichier XML si non défini
    if (!$xmlFile) {
        $xmlFile = "xml/{$ville}.xml";
    }

    // Mise à jour du fichier XML
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

        // Mettre à jour les sites
        $sitesNode = $root->getElementsByTagName('sites')->item(0);
        if ($sitesNode) {
            while ($sitesNode->hasChildNodes()) {
                $sitesNode->removeChild($sitesNode->firstChild);
            }
            foreach ($sites as $site) {
                $newSite = $xml->createElement('site');
                $newSite->setAttribute('nom', htmlspecialchars($site));
                $sitesNode->appendChild($newSite);
            }
        }

        // Mettre à jour les autres éléments (hôtels, restaurants, etc.)
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

        updateElements($xml, $root, 'hotel', $hotels);
        updateElements($xml, $root, 'restaurant', $restaurants);
        updateElements($xml, $root, 'gare', $gares);
        updateElements($xml, $root, 'aeroport', $aeroports);

        // Sauvegarder les modifications
        $xml->save($xmlFile);
       // echo "Les modifications ont été enregistrées avec succès.";
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
    <link rel="stylesheet" href="styles/formulaire.css">
</head>
<style>
    /* allerte de succé d'enregestrement de ville */
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
    /* le bouton revenir a la page d'acceuil */
.back-button {
    display: inline-block;
    position: absolute;
    top: 20px; /* Position verticale */
    left: 20px; /* Position horizontale */
    font-size: 14px; /* Taille du texte */
    color: #fff; /* Couleur du texte */
    background-color: #282b68; /* Couleur de fond principale */
    padding: 15px 20px; /* Espacement interne */
    text-decoration: none; /* Supprime le soulignement */
    border-radius: 50px; /* Coins arrondis pour un style "pill button" */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); /* Ombre douce */
    transition: transform 0.2s ease, background-color 0.3s ease; /* Effet de transition */
    text-align: center; /* Centre le texte */
    white-space: nowrap; /* Évite les retours à la ligne */
}

.back-button:hover {
    background-color: #c28cce; /* Couleur plus foncée au survol */
    transform: scale(1.1); /* Légère mise en avant au survol */
}

.back-button::before {
    content: '←'; /* Ajout d'une flèche avant le texte */
    margin-right: 8px; /* Espacement entre la flèche et le texte */
    font-size: 16px; /* Taille de la flèche */
}
</style>
<body>
   <a href="accueil.php" class="back-button">Revenir à l'accueil</a>

    <h1>Formulaire Étendu de Ville</h1>
    <div id="success-alert" class="alert" style="display: none;">
      Les informations de la ville ont été enregistrées avec succès.
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
