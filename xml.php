<?php

// Get datas from societe table
$querySociete = "SELECT 
    SUBSTRING(numerocafat, 1, 6) AS numeroCafat,
    SUBSTRING(numerocafat, 10) AS suffixeCafat,
    enseigne,
    SUBSTRING(ridet, 1, 7) AS ridet,
    SUBSTRING(ridet, 9, 11) AS codeCotisation,
    tauxat 
FROM societe";
$resultSociete = $conn->query($querySociete);
$rowSociete = $resultSociete->fetch(PDO::FETCH_ASSOC);
$numeroCafat = $rowSociete['numeroCafat'];
$suffixeCafat = $rowSociete['suffixeCafat'];
$enseigne = $rowSociete['enseigne'];
$ridet = $rowSociete['ridet'];
$codeCotisation = $rowSociete['codeCotisation'];
$tauxat = $rowSociete['tauxat'];

// Get datas from salaries table
$querySalaries = "SELECT s.*, b.* FROM salaries s
                INNER JOIN bulletin b ON s.id = b.salarie_id LIMIT 1";
$resultSalaries = $conn->query($querySalaries);

// Generate the XML content
$xmlContent = "
<?xml version='1.0' encoding='ISO-8859-1'?> 
<doc>
    <entete>
        <type>DN</type>
        <version>VERSION_2_0</version>
        <emetteur>{$enseigne}</emetteur>
        <dateGeneration>" . date('Y-m-d\TH:i:s') . "</dateGeneration>
        <logiciel>
            <editeur>MONEDITEUR</editeur>
            <nom>MONPROGICIEL</nom>
            <version>11.0.0 Patch 3</version>
            <dateVersion>2022-12-25</dateVersion>
        </logiciel>
    </entete>
    <corps>
        <periode>
            <type>$periode</type>
            <annee>$year</annee>
            <numero>$numero</numero>
        </periode>
        <attributs>
            <complementaire>false</complementaire>
            <contratAlternance>false</contratAlternance>
            <pasAssureRemunere>false</pasAssureRemunere>
            <pasDeReembauche>false</pasDeReembauche>
        </attributs>
        <employeur>
            <numero>$numeroCafat</numero>
            <suffixe>$suffixeCafat</suffixe>
            <nom>$enseigne</nom>
            <rid>$ridet</rid>
            <codeCotisation>$codeCotisation</codeCotisation>
            <tauxATPrincipal>$tauxat</tauxATPrincipal>
        </employeur>
        <assures>
        ";

while ($rowSalaries = $resultSalaries->fetch(PDO::FETCH_ASSOC)) {
    $numcafat = $rowSalaries['numcafat'];
    $nom = $rowSalaries['nom'];
    $prenom = $rowSalaries['prenom'];
    $dnaissance = $rowSalaries['dnaissance'];
    $nombreHeures = $rowSalaries['nombre_heures'];
    $salaireBrut = $rowSalaries['brut'];
    $drupture = $rowSalaries['drupture'];

    $xmlContent .= "    <assure>
                <numero>$numcafat</numero>
                <nom>$nom</nom>
                <prenoms>$prenom</prenoms>
                <dateNaissance>$dnaissance</dateNaissance>
                <codeAT>PRINCIPAL</codeAT>
                <etablissementRID>123</etablissementRID>
                <codeCommune>98809</codeCommune>
                <nombreHeures>$nombreHeures</nombreHeures>
                <remuneration>$salaireBrut</remuneration>
                <assiettes>
                ";

    
        $xmlContent .= "    <assiette>
                        <type>RUAMM</type>
                        <valeur>500000</valeur>
                    </assiette>";
                    
    $xmlContent .= "
                </assiettes>
                ";
    if ($drupture != null) {
        $xmlContent .= "<dateRupture>$drupture</dateRupture>
                <observations>FIN DE CONTRAT</observations>";
    }
    $xmlContent .= "
            </assure>";
}

$xmlContent .= "
        </assures>
        <decompte>
            <cotisations>
                <cotisation>
                    <type>RUAMM</type>
                    <tranche>TRANCHE_1</tranche>
                    <assiette>3583400</assiette>
                    <valeur>556144</valeur>
                </cotisation>
            </cotisations>
            <totalCotisations>1803203</totalCotisations>
            <deductions>
                <deduction>
                    <type>ACOMPTE</type>
                    <valeur>3203</valeur>
                </deduction>
            </deductions>
            <montantAPayer>1700000</montantAPayer>
        </decompte>
    </corps>
</doc>";
