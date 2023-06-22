<?php
require "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $periode = $_POST["periode"];
    $annuel = $_POST["annuel"];

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
    if ($periode === "mensuel") {
        $type = "M";
        $numero = str_pad($_POST["mensuel"], 2, "0", STR_PAD_LEFT);
    } elseif ($periode === "trimestriel") {
        $type = "T";
        $trimestriel = $_POST["trimestriel"];
        $numero = str_pad($trimestriel, 2, "0", STR_PAD_LEFT);
    } elseif ($periode === "annuel") {
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
    $fileName = $prefix . $annuel . $type . $numero . $separator . "0" . $numeroCafat . $suffixeCafat . $separator . $numeroUnique . $extension;

    // Construire et exécuter la requête SQL pour récupérer les données
    $periode = strtoupper($_POST["periode"]);
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
