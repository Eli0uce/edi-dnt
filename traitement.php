<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $periode = $_POST["periode"];
    $year = $_POST["annee"];

    // Generate the XML content
    $prefix = "DN-";
    $type = "";
    $numero = "";
    $separator = "-";
    $compteEmployeur = ""; // Replace with the actual value
    $suffixeCompteEmployeur = ""; // Replace with the actual value
    $numeroUnique = ""; // Replace with the actual value
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

    // Generate the XML file name
    $fileName = $prefix . $year . $type . $numero . $separator . $compteEmployeur . $suffixeCompteEmployeur . $separator . $numeroUnique . $extension;

    // Generate the XML content
    $xmlContent = "<root>
        <prefix>$prefix</prefix>
        <annee>$year</annee>
        <type>$type</type>
        <numero>$numero</numero>
        <separator>$separator</separator>
        <compteEmployeur>$compteEmployeur</compteEmployeur>
        <suffixeCompteEmployeur>$suffixeCompteEmployeur</suffixeCompteEmployeur>
        <numeroUnique>$numeroUnique</numeroUnique>
        <extension>$extension</extension>
    </root>";

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
