<?php

// Get data from the societe table
$querySociete = "SELECT 
    SUBSTRING(numerocafat, 1, 6) AS numeroCafat,
    SUBSTRING(numerocafat, 8, 10) AS suffixeCafat,
    enseigne,
    SUBSTRING(ridet, 1, 7) AS ridet,
    SUBSTRING(ridet, 9, 11) AS RID,
    tauxat 
FROM societe";
$resultSociete = $conn->query($querySociete);
$rowSociete = $resultSociete->fetch(PDO::FETCH_ASSOC);

$numeroCafat = $rowSociete['numeroCafat'];
$suffixeCafat = $rowSociete['suffixeCafat'];
$enseigne = $rowSociete['enseigne'];
$ridet = $rowSociete['ridet'];
$RID = $rowSociete['RID'];
$tauxat = $rowSociete['tauxat'];

// Get data from the salaries table
$querySalaries = "SELECT s.*, b.*, lb.rubrique_id FROM salaries s
                  INNER JOIN bulletin b ON s.id = b.salarie_id
                  INNER JOIN ligne_bulletin lb ON b.id = lb.bulletin_id LIMIT 1";
$resultSalaries = $conn->query($querySalaries);

if ($periode === "trimestriel" && $trimestriel === "1") {
    $queryBulletin = "SELECT * FROM bulletin WHERE SUBSTRING(periode, 1, 4) = '$annuel' AND (SUBSTRING(periode, 3, 2) = '01' OR SUBSTRING(periode, 3, 2) = '02' OR SUBSTRING(periode, 3, 2) = '03')";
} elseif ($periode === "trimestriel" && $trimestriel === "2") {
    $queryBulletin = "SELECT * FROM bulletin WHERE SUBSTRING(periode, 1, 4) = '$annuel' AND (SUBSTRING(periode, 3, 2) = '04' OR SUBSTRING(periode, 3, 2) = '05' OR SUBSTRING(periode, 3, 2) = '06')";
} elseif ($periode === "trimestriel" && $trimestriel === "3") {
    $queryBulletin = "SELECT * FROM bulletin WHERE SUBSTRING(periode, 1, 4) = '$annuel' AND (SUBSTRING(periode, 3, 2) = '07' OR SUBSTRING(periode, 3, 2) = '08' OR SUBSTRING(periode, 3, 2) = '09')";
} elseif ($periode === "trimestriel" && $trimestriel === "4") {
    $queryBulletin = "SELECT * FROM bulletin WHERE SUBSTRING(periode, 1, 4) = '$annuel' AND (SUBSTRING(periode, 3, 2) = '10' OR SUBSTRING(periode, 3, 2) = '11' OR SUBSTRING(periode, 3, 2) = '12')";
}

// Generate the XML content
$xmlContent = "<?xml version='1.0' encoding='ISO-8859-1'?>\n<doc>\n";
$xmlContent .= "    <entete>\n";
$xmlContent .= "        <type>DN</type>\n";
$xmlContent .= "        <version>VERSION_2_0</version>\n";
$xmlContent .= "        <emetteur>$enseigne</emetteur>\n";
$xmlContent .= "        <dateGeneration>" . date('Y-m-d\TH:i:s') . "</dateGeneration>\n";
$xmlContent .= "        <logiciel>\n";
$xmlContent .= "            <editeur>MONEDITEUR</editeur>\n";
$xmlContent .= "            <nom>MONPROGICIEL</nom>\n";
$xmlContent .= "            <version>11.0.0 Patch 3</version>\n";
$xmlContent .= "            <dateVersion>2022-12-25</dateVersion>\n";
$xmlContent .= "        </logiciel>\n";
$xmlContent .= "    </entete>\n";
$xmlContent .= "    <corps>\n";
$xmlContent .= "        <periode>\n";
$xmlContent .= "            <type>$periode</type>\n";
$xmlContent .= "            <annee>$annuel</annee>\n";
$xmlContent .= "            <numero>$numero</numero>\n";
$xmlContent .= "        </periode>\n";
$xmlContent .= "        <attributs>\n";
$xmlContent .= "            <complementaire>false</complementaire>\n";
$xmlContent .= "            <contratAlternance>false</contratAlternance>\n";
$xmlContent .= "            <pasAssureRemunere>false</pasAssureRemunere>\n";
$xmlContent .= "            <pasDeReembauche>false</pasDeReembauche>\n";
$xmlContent .= "        </attributs>\n";
$xmlContent .= "        <employeur>\n";
$xmlContent .= "            <numero>$numeroCafat</numero>\n";
$xmlContent .= "            <suffixe>$suffixeCafat</suffixe>\n";
$xmlContent .= "            <nom>$enseigne</nom>\n";
$xmlContent .= "            <rid>$ridet</rid>\n";
$xmlContent .= "            <codeCotisation>001</codeCotisation>\n";
$xmlContent .= "            <tauxATPrincipal>$tauxat</tauxATPrincipal>\n";
$xmlContent .= "        </employeur>\n";
$xmlContent .= "        <assures>\n";

while ($rowSalaries = $resultSalaries->fetch(PDO::FETCH_ASSOC)) {
    $salarieId = $rowSalaries['id'];

    // Query to retrieve the bulletin data using the salarie_id
    $queryBulletin = "SELECT * FROM ligne_bulletin WHERE bulletin_id = $salarieId AND rubrique_id IN (57, 58, 67, 56, 59, 62, 68, 64, 65, 66)";
    $resultBulletin = $conn->query($queryBulletin);

    // Check for errors during the query execution
    if (!$resultBulletin) {
        die("Erreur lors de l'exécution de la requête : " . $conn->error);
    }

    $numcafat = $rowSalaries['numcafat'];
    $nom = $rowSalaries['nom'];
    $prenom = $rowSalaries['prenom'];
    $dnaissance = $rowSalaries['dnaissance'];
    $nombreHeures = $rowSalaries['nombre_heures'];
    $salaireBrut = $rowSalaries['brut'];
    $drupture = $rowSalaries['drupture'];
    $bperiod = $rowSalaries['periode'];

    // Vérifier si la valeur de $nombreHeures correspond déjà au format "x.xx"
    if (!preg_match("/\d+\.\d{2}/", $nombreHeures)) {
        // Formater la valeur avec 2 décimales
        $nombreHeures = number_format($nombreHeures, 2, '.', '');
    }

    $xmlContent .= "            <assure>\n";
    $xmlContent .= "                <numero>$numcafat</numero>\n";
    $xmlContent .= "                <nom>$nom</nom>\n";
    $xmlContent .= "                <prenoms>$prenom</prenoms>\n";
    $xmlContent .= "                <dateNaissance>$dnaissance</dateNaissance>\n";
    $xmlContent .= "                <codeAT>PRINCIPAL</codeAT>\n";
    $xmlContent .= "                <etablissementRID>$RID</etablissementRID>\n";
    $xmlContent .= "                <nombreHeures>$nombreHeures</nombreHeures>\n";
    $xmlContent .= "                <remuneration>$salaireBrut</remuneration>\n";
    $xmlContent .= "                <assiettes>\n";

    while ($rowLigneBulletin = $resultBulletin->fetch(PDO::FETCH_ASSOC)) {
        $base = $rowLigneBulletin['base'];
        $rubriqueId = $rowLigneBulletin['rubrique_id'];

        // Définir le libellé en fonction de la valeur de $rubriqueId
        if ($rubriqueId == 57 || $rubriqueId == 58) {
            $libelle = "RUAMM";
        } elseif ($rubriqueId == 67) {
            $libelle = "FIAF";
        } elseif ($rubriqueId == 56) {
            $libelle = rand(0, 2) == 0 ? "RETRAITE" : (rand(0, 1) == 0 ? "PRESTATIONS_FAMILIALES" : "CHOMAGE");
        } elseif ($rubriqueId == 59 || $rubriqueId == 62) {
            $libelle = "ATMP";
        } elseif ($rubriqueId == 68) {
            $libelle = "FDS";
        } elseif ($rubriqueId == 64) {
            $libelle = "FORMATION_PROFESSIONNELLE";
        } elseif ($rubriqueId == 71 || $rubriqueId == 75) {
            $libelle = "CRE";
        } elseif ($rubriqueId == 65) {
            $libelle = "FSH";
        } elseif ($rubriqueId == 66) {
            $libelle = "CCS";
        }

        if ($rubriqueId != 66) {
            $xmlContent .= "                    <assiette>\n";
            $xmlContent .= "                        <type>$libelle</type>\n";
            $xmlContent .= "                        <valeur>$base</valeur>\n";
            $xmlContent .= "                    </assiette>\n";
        }

        $totalAssiette = 0;
        $totalValeur = 0;

        if ($rubriqueId == 66) {
            $totalAssiette += $base;
            $totalValeur += round($base * 2 / 100, 0);
        }
    }
    $xmlContent .= "                </assiettes>\n";

    if ($drupture != null) {
        $xmlContent .= "                <dateRupture>$drupture</dateRupture>\n";
        $xmlContent .= "                <observations>FIN DE CONTRAT</observations>\n";
    }
    $xmlContent .= "            </assure>\n";
}

$xmlContent .= "        </assures>\n";
$xmlContent .= "        <decompte>\n";
$xmlContent .= "            <cotisations>\n";
$xmlContent .= "                <cotisation>\n";
$xmlContent .= "                    <type>CCS</type>\n";
$xmlContent .= "                    <assiette>$totalAssiette</assiette>\n";
$xmlContent .= "                    <valeur>$totalValeur</valeur>\n";
$xmlContent .= "                </cotisation>\n";
$xmlContent .= "            </cotisations>\n";
$xmlContent .= "            <deductions>\n </deductions>\n";
$xmlContent .= "        </decompte>\n";
$xmlContent .= "    </corps>\n";
$xmlContent .= "</doc>";