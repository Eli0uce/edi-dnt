<?php
require "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $periode = $_POST["periode"];
    $year = $_POST["annee"];

    // Generate the XML content
    $prefix = "DN-";
    $type = "";
    $numero = "";
    $separator = "-";
    $compteEmployeur = "";
    $suffixeCompteEmployeur = "";
    $numeroUnique = str_pad(mt_rand(1, 999), 3, "0", STR_PAD_LEFT);
    $extension = ".xml";

    // Determine the values based on the selected period
    if ($periode === "mois") {
        $type = "M";
        $numero = str_pad($_POST["mois"], 2, "0", STR_PAD_LEFT);
    } elseif ($periode === "trimestre") {
        $type = "T";
        $trimestre = $_POST["trimestre"];
        $numero = str_pad($trimestre, 2, "0", STR_PAD_LEFT);
    } elseif ($periode === "annee") {
        $type = "A";
        $numero = "01";
    }

    $querySociete = "SELECT 
        SUBSTRING(numerocafat, 1, 6) AS numeroCafat,
        SUBSTRING(numerocafat, 8, 10) AS suffixeCafat
        FROM societe";
    $resultSociete = $conn->query($querySociete);
    $rowSociete = $resultSociete->fetch(PDO::FETCH_ASSOC);
    $numeroCafat = $rowSociete['numeroCafat'];
    $suffixeCafat = $rowSociete['suffixeCafat'];

    // Generate the XML file name
    $fileName = $prefix . $year . $type . $numero . $separator . "0" . $numeroCafat . $suffixeCafat . $separator . $numeroUnique . $extension;

    // Construire et exécuter la requête SQL pour récupérer les données
    $query = "SELECT * FROM bulletin";
    $stmt = $conn->query($query);
    $bulletinData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    include "xml.php";

    // Generate the XML file
    file_put_contents($fileName, $xmlContent);

    // Set the appropriate headers for file download
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($fileName));
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . filesize($fileName));

    // Read and output the file content
    readfile($fileName);

    // Delete the temporary XML file
    unlink($fileName);

    // Terminate the script after file download
    exit;
}
