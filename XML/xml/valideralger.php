<?php
// Créer une nouvelle instance DOMDocument
$dom = new DOMDocument();
libxml_use_internal_errors(true); // Activer les erreurs internes de libxml pour valider le XML

// Charger le fichier XML
if ($dom->load('Alger.xml')) {
    echo "XML parsé avec succès.\n";

    // Créer un objet XPath
    $xpath = new DOMXPath($dom);

    // Exemple 1 : Récupérer le nom de la ville
    $ville = $xpath->query('//ville');
    echo "Nom de la ville : " . $ville->item(0)->getAttribute('nom') . "\n";

    // Exemple 2 : Récupérer le descriptif de la ville
    $descriptif = $xpath->query('//descriptif');
    echo "\nDescriptif de la ville : " . $descriptif->item(0)->nodeValue . "\n";

    // Exemple 3 : Récupérer tous les sites touristiques
    $sites = $xpath->query('//sites/site');
    echo "\nSites touristiques :\n";
    foreach ($sites as $site) {
        echo "- " . $site->getAttribute('nom') . " (photo: " . $site->getAttribute('photo') . ")\n";
    }

    // Exemple 4 : Récupérer les hôtels
    $hotels = $xpath->query('//hotels/hotel');
    echo "\nHôtels :\n";
    foreach ($hotels as $hotel) {
        echo "- " . $hotel->nodeValue . "\n";
    }

    // Exemple 5 : Récupérer les restaurants
    $restaurants = $xpath->query('//restaurants/restaurant');
    echo "\nRestaurants :\n";
    foreach ($restaurants as $restaurant) {
        echo "- " . $restaurant->nodeValue . "\n";
    }

    // Exemple 6 : Récupérer les gares
    $gares = $xpath->query('//gares/gare');
    echo "\nGares :\n";
    foreach ($gares as $gare) {
        echo "- " . $gare->nodeValue . "\n";
    }

    // Exemple 7 : Récupérer les aéroports
    $aeroports = $xpath->query('//aeroports/aeroport');
    echo "\nAéroports :\n";
    foreach ($aeroports as $aeroport) {
        echo "- " . $aeroport->nodeValue . "\n";
    }

} else {
    echo "Erreur lors du chargement du fichier XML :\n";
    foreach(libxml_get_errors() as $error) {
        echo $error->message . "\n";
    }
}
?>
