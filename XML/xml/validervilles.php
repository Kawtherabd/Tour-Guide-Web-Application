<?php
// Créer une nouvelle instance DOMDocument
$dom = new DOMDocument();
libxml_use_internal_errors(true); // Activer les erreurs internes de libxml pour valider le XML

// Charger le fichier XML
if ($dom->load('Villes.xml')) {
    echo "XML parsé avec succès.\n";

    // Créer un objet XPath
    $xpath = new DOMXPath($dom);

    // Exemple 1 : Récupérer tous les continents
    $continents = $xpath->query('//continents/continent');
    echo "Continents :\n";
    foreach ($continents as $continent) {
        echo "- " . $continent->getAttribute('nom') . "\n";
    }

    // Exemple 2 : Récupérer toutes les villes d'un pays donné (par exemple, "Algérie")
    $villesAlgerie = $xpath->query('//pays[@nom="Algérie"]/villes/ville');
    echo "\nVilles en Algérie :\n";
    foreach ($villesAlgerie as $ville) {
        echo "- " . $ville->getAttribute('nom') . "\n";
    }

    // Exemple 3 : Récupérer les sites d'une ville donnée (par exemple, "Oran")
    $sitesOran = $xpath->query('//pays[@nom="Algérie"]/villes/ville[@nom="Oran"]/sites/site');
    echo "\nSites à Oran :\n";
    foreach ($sitesOran as $site) {
        echo "- " . $site->getAttribute('nom') . " (photo: " . $site->getAttribute('photo') . ")\n";
    }

} else {
    echo "Erreur lors du chargement du fichier XML :\n";
    foreach(libxml_get_errors() as $error) {
        echo $error->message . "\n";
    }
}
?>
