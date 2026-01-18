<?php
// Charger le fichier XML
$dom = new DOMDocument();

// Activer la validation DTD
$dom->validateOnParse = true;
$dom->resolveExternals = true;

// Charger le fichier XML
if ($dom->load('Config.xml')) {
    echo "XML chargé avec succès.\n";

    // Valider avec la DTD
    if ($dom->validate()) {
        echo "XML valide selon la DTD.\n";

        // Lire les données du header
        $header = $dom->getElementsByTagName('header')->item(0);
        $titre = $header->getElementsByTagName('titre')->item(0)->nodeValue;
        $image = $header->getElementsByTagName('image')->item(0)->nodeValue;
        echo "Titre : $titre\n";
        echo "Image : $image\n";

        // Parcourir les étudiants
        $etudiants = $dom->getElementsByTagName('etudiant');
        foreach ($etudiants as $etudiant) {
            $nom = $etudiant->getElementsByTagName('nom')->item(0)->nodeValue;
            $prenom = $etudiant->getElementsByTagName('prenom')->item(0)->nodeValue;
            $specialite = $etudiant->getElementsByTagName('specialite')->item(0)->nodeValue;
            $email = $etudiant->getElementsByTagName('email')->item(0)->nodeValue;
            echo "Nom : $nom\n";
            echo "Prénom : $prenom\n";
            echo "Spécialité : $specialite\n";
            echo "Email : $email\n";
        }
    } else {
        echo "XML non valide selon la DTD.\n";
    }
} else {
    echo "Erreur lors du chargement du fichier XML.\n";
}
