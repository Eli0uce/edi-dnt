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
$querySalaries = "SELECT s.*, b.*, lb.rubrique_id, SUM(lb.base) AS base
                    FROM salaries s
                    INNER JOIN bulletin b ON s.id = b.salarie_id
                    INNER JOIN ligne_bulletin lb ON b.id = lb.bulletin_id
                    WHERE b.periode IN ('202201', '202202', '202203')
                    AND lb.rubrique_id IN (57, 58, 67, 56, 59, 62, 68, 64, 65, 66)
                    GROUP BY s.nom, lb.rubrique_id";
$resultSalaries = $conn->query($querySalaries);

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

$arrayId = array();
$totalRUAMM = 0;
$totalFIAF = 0;
$totalRETRAITE = 0;
$totalPF = 0;
$totalCHOMAGE = 0;
$totalATMP = 0;
$totalFDS = 0;
$totalFP = 0;
$totalCRE = 0;
$totalFSH = 0;
$totalCCS = 0;

while ($rowSalaries = $resultSalaries->fetch(PDO::FETCH_ASSOC)) {
    $salarieId = $rowSalaries['id'];

    if (!empty($arrayId) and in_array($salarieId, $arrayId) === false) {
        $xmlContent .= "                </assiettes>\n";

        if ($drupture != null) {
            $xmlContent .= "                <dateRupture>$drupture</dateRupture>\n";
            $xmlContent .= "                <observations>FIN DE CONTRAT</observations>\n";
        }
        $xmlContent .= "            </assure>\n";
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
        $nombreHeures = number_format($nombreHeures, 2, '.', '');
    }

    if (in_array($salarieId, $arrayId) === false) {
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

        array_push($arrayId, $salarieId);
    }

    // while ($rowLigneBulletin = $resultSalaries->fetch(PDO::FETCH_ASSOC)) {
    $base = $rowSalaries['base'];
    $rubriqueId = $rowSalaries['rubrique_id'];

    // Définir le libellé en fonction de la valeur de $rubriqueId
    if ($rubriqueId == 57 || $rubriqueId == 58) {
        $libelle = "RUAMM";
        $tauxRUAMM1 = 15.52;
        $tauxRUAMM2 = 5;
        $totalRUAMM += $base;
    } elseif ($rubriqueId == 67) {
        $libelle = "FIAF";
        $tauxFIAF = 0.2;
        $totalFIAF += $base;
    } elseif ($rubriqueId == 56) {
        $libelle = rand(0, 2) == 0 ? "RETRAITE" : (rand(0, 1) == 0 ? "PRESTATIONS_FAMILIALES" : "CHOMAGE");
        $tauxRETRAITE = 14;
        $tauxPF = 5.63;
        $tauxCHOMAGE = 2.06;
        $totalRETRAITE += $base;
    } elseif ($rubriqueId == 59 || $rubriqueId == 62) {
        $libelle = "ATMP";
        $tauxATMP = 0.72;
        $totalATMP += $base;
    } elseif ($rubriqueId == 68) {
        $libelle = "FDS";
        $tauxFDS = 0.075;
        $totalFDS += $base;
    } elseif ($rubriqueId == 64) {
        $libelle = "FORMATION_PROFESSIONNELLE";
        $tauxFP = 0.25;
        $totalFP += $base;
    } elseif ($rubriqueId == 71 || $rubriqueId == 75) {
        $libelle = "CRE";
        $totalCRE += $base;
    } elseif ($rubriqueId == 65) {
        $libelle = "FSH";
        $tauxFSH = 2;
        $totalFSH += $base;
    } elseif ($rubriqueId == 66) {
        $libelle = "CCS";
        $tauxCCS = 2;
        $totalCCS += $base;
    }

    if ($rubriqueId != 66) {
        $xmlContent .= "                    <assiette>\n";
        $xmlContent .= "                        <type>$libelle</type>\n";
        $xmlContent .= "                        <valeur>$base</valeur>\n";
        $xmlContent .= "                    </assiette>\n";
    }
}

$xmlContent .= "                </assiettes>\n";
$xmlContent .= "            </assure>\n";
$xmlContent .= "        </assures>\n";
$xmlContent .= "        <decompte>\n";
$xmlContent .= "            <cotisations>\n";
$xmlContent .= "                <cotisation>\n";
$xmlContent .= "                    <type>RUAMM</type>\n";
$xmlContent .= "                    <assiette>$totalRUAMM</assiette>\n";
$xmlContent .= "                    <valeur>" . round($totalRUAMM * $tauxRUAMM1 / 100, 0) . "</valeur>\n";
$xmlContent .= "                </cotisation>\n";
$xmlContent .= "                <cotisation>\n";
$xmlContent .= "                    <type>RUAMM</type>\n";
$xmlContent .= "                    <assiette>0</assiette>\n";
$xmlContent .= "                    <valeur>" . round($totalRUAMM * $tauxRUAMM2 / 100, 0) . "</valeur>\n";
$xmlContent .= "                </cotisation>\n";
$xmlContent .= "                <cotisation>\n";
$xmlContent .= "                    <type>FIAF</type>\n";
$xmlContent .= "                    <assiette>$totalFIAF</assiette>\n";
$xmlContent .= "                    <valeur>" . round($totalFIAF * $tauxFIAF / 100, 0) . "</valeur>\n";
$xmlContent .= "                </cotisation>\n";
$xmlContent .= "                <cotisation>\n";
$xmlContent .= "                    <type>RETRAITE</type>\n";
$xmlContent .= "                    <assiette>$totalRETRAITE</assiette>\n";
$xmlContent .= "                    <valeur>" . round($totalRETRAITE * $tauxRETRAITE / 100, 0) . "</valeur>\n";
$xmlContent .= "                </cotisation>\n";
$xmlContent .= "                <cotisation>\n";
$xmlContent .= "                    <type>PRESTATIONS_FAMILIALES</type>\n";
$xmlContent .= "                    <assiette>$totalRETRAITE</assiette>\n";
$xmlContent .= "                    <valeur>" . round($totalRETRAITE * $tauxPF / 100, 0) . "</valeur>\n";
$xmlContent .= "                </cotisation>\n";
$xmlContent .= "                <cotisation>\n";
$xmlContent .= "                    <type>CHOMAGE</type>\n";
$xmlContent .= "                    <assiette>$totalRETRAITE</assiette>\n";
$xmlContent .= "                    <valeur>" . round($totalRETRAITE * $tauxCHOMAGE / 100, 0) . "</valeur>\n";
$xmlContent .= "                </cotisation>\n";
$xmlContent .= "                <cotisation>\n";
$xmlContent .= "                    <type>ATMP_PRINCIPAL</type>\n";
$xmlContent .= "                    <assiette>$totalATMP</assiette>\n";
$xmlContent .= "                    <valeur>" . round($totalATMP * $tauxATMP / 100, 0) . "</valeur>\n";
$xmlContent .= "                </cotisation>\n";
$xmlContent .= "                <cotisation>\n";
$xmlContent .= "                    <type>FDS</type>\n";
$xmlContent .= "                    <assiette>$totalFDS</assiette>\n";
$xmlContent .= "                    <valeur>" . round($totalFDS * $tauxFDS / 100, 0) . "</valeur>\n";
$xmlContent .= "                </cotisation>\n";
$xmlContent .= "                <cotisation>\n";
$xmlContent .= "                    <type>FORMATION_PROFESSIONNELLE</type>\n";
$xmlContent .= "                    <assiette>$totalFP</assiette>\n";
$xmlContent .= "                    <valeur>" . round($totalFP * $tauxFP / 100, 0) . "</valeur>\n";
$xmlContent .= "                </cotisation>\n";
$xmlContent .= "                <cotisation>\n";
$xmlContent .= "                    <type>CRE</type>\n";
$xmlContent .= "                    <assiette>$totalCRE</assiette>\n";
$xmlContent .= "                    <valeur>$totalCRE</valeur>\n";
$xmlContent .= "                </cotisation>\n";
$xmlContent .= "                <cotisation>\n";
$xmlContent .= "                    <type>FSH</type>\n";
$xmlContent .= "                    <assiette>$totalFSH</assiette>\n";
$xmlContent .= "                    <valeur>" . round($totalFSH * $tauxFSH / 100, 0) . "</valeur>\n";
$xmlContent .= "                </cotisation>\n";
$xmlContent .= "                <cotisation>\n";
$xmlContent .= "                    <type>FSH</type>\n";
$xmlContent .= "                    <assiette>$totalCCS</assiette>\n";
$xmlContent .= "                    <valeur>" . round($totalCCS * $tauxCCS / 100, 0) . "</valeur>\n";
$xmlContent .= "                </cotisation>\n";
$xmlContent .= "            </cotisations>\n";
$xmlContent .= "            <deductions>\n </deductions>\n";
$xmlContent .= "        </decompte>\n";
$xmlContent .= "    </corps>\n";
$xmlContent .= "</doc>";