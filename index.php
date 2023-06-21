<!DOCTYPE html>
<html>

<head>
    <title>EDI DNT</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        .card {
            margin: 0 auto;
            margin-top: 30px;
            max-width: 400px;
        }

        .centered-image {
            display: block;
            margin: 0 auto;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="centered-image mt-5">
            <img src="./files/icon-cafat.png" alt="Logo Cafat" width="200px">
        </div>
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">Génération XML</h2>
                <form action="traitement.php" method="POST" onsubmit="return generateXML()">
                    <div class="form-group">
                        <label for="periode">Période:</label>
                        <select class="form-control" id="periode" name="periode" onchange="showDateFields()">
                            <option value="">Choisir une période</option>
                            <option value="mois">Mensuel</option>
                            <option value="trimestre">Trimestriel</option>
                            <option value="annee">Annuel</option>
                        </select>
                    </div>
                    <div id="dateFields" style="display: none;">
                        <!-- The date fields will be dynamically added here -->
                    </div>
                    <button type="submit" class="btn btn-primary">Générer</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showDateFields() {
            var periode = document.getElementById("periode").value;
            var dateFields = document.getElementById("dateFields");

            // Reset the date fields with each period change
            dateFields.innerHTML = "";

            if (periode === "mois") {
                // Create the monthly date form (month and year)
                var monthInput = document.createElement("input");
                monthInput.setAttribute("type", "number");
                monthInput.setAttribute("class", "form-control mb-3");
                monthInput.setAttribute("placeholder", "Mois");
                monthInput.setAttribute("name", "mois");
                monthInput.setAttribute("required", "required");
                monthInput.setAttribute("min", "1");
                monthInput.setAttribute("max", "12");

                var yearInput = document.createElement("input");
                yearInput.setAttribute("type", "number");
                yearInput.setAttribute("class", "form-control mb-3");
                yearInput.setAttribute("placeholder", "Année");
                yearInput.setAttribute("name", "annee");
                yearInput.setAttribute("required", "required");
                yearInput.setAttribute("min", "1900");
                yearInput.setAttribute("max", "2100");

                dateFields.appendChild(monthInput);
                dateFields.appendChild(yearInput);
            } else if (periode === "trimestre") {
                // Create the quarterly selection form
                var trimestreSelect = document.createElement("select");
                trimestreSelect.setAttribute("class", "form-control mb-3");
                trimestreSelect.setAttribute("name", "trimestre");
                trimestreSelect.setAttribute("required", "required");

                var trimestres = ["Trimestre 1", "Trimestre 2", "Trimestre 3", "Trimestre 4"];
                var trimestreValues = ["01-03", "04-06", "07-09", "10-12"];

                for (var i = 0; i < trimestres.length; i++) {
                    var option = document.createElement("option");
                    option.value = trimestreValues[i];
                    option.text = trimestres[i];
                    trimestreSelect.appendChild(option);
                }

                var yearInput = document.createElement("input");
                yearInput.setAttribute("type", "number");
                yearInput.setAttribute("class", "form-control mb-3");
                yearInput.setAttribute("placeholder", "Année");
                yearInput.setAttribute("name", "annee");
                yearInput.setAttribute("required", "required");
                yearInput.setAttribute("min", "1900");
                yearInput.setAttribute("max", "2100");

                dateFields.appendChild(trimestreSelect);
                dateFields.appendChild(yearInput);
            } else if (periode === "annee") {
                // Create the date picker for a year
                var yearInput = document.createElement("input");
                yearInput.setAttribute("type", "number");
                yearInput.setAttribute("class", "form-control mb-3");
                yearInput.setAttribute("placeholder", "Année");
                yearInput.setAttribute("name", "annee");
                yearInput.setAttribute("required", "required");
                yearInput.setAttribute("min", "1900");
                yearInput.setAttribute("max", "2100");

                dateFields.appendChild(yearInput);
            }

            // Display the date fields
            dateFields.style.display = "block";
        }

        function generateXML() {
            var periode = document.getElementById("periode").value;

            if (periode === "mois") {
                var month = document.getElementsByName("mois")[0].value;
                var year = document.getElementsByName("annee")[0].value;

                // Validate the month and year values (add additional conditions if necessary)
                if (month < 1 || month > 12) {
                    alert("Mois invalide");
                    return false;
                }
                if (year < 1900 || year > 2100) {
                    alert("Année invalide");
                    return false;
                }
            } else if (periode === "trimestre") {
                var trimestre = document.getElementsByName("trimestre")[0].value;
                var year = document.getElementsByName("annee")[0].value;

                // Validate the values of the quarter and the year (add additional conditions if necessary)
                // Here, we validate only if the year is a 4-digit number
                if (!year.match(/^\d{4}$/)) {
                    alert("Année invalide");
                    return false;
                }
            }

            // Generate the XML file name
            var fileName = generateFileName(periode, year, month, trimestre);

            // Submit the form with the XML file name
            document.getElementById("xmlFileName").value = fileName;

            return true;
        }

        function generateFileName(periode, year, month, trimestre) {
            var prefix = "DN-";
            var periodeType = "";
            var periodeNumber = "";

            if (periode === "mois") {
                periodeType = "M";
                periodeNumber = ("0" + month).slice(-2);
            } else if (periode === "trimestre") {
                periodeType = "T";
                periodeNumber = trimestre.split("-")[0];
            } else if (periode === "annee") {
                periodeType = "A";
                periodeNumber = "01";
            }

            var compteEmployeur = "1234567";
            var suffixeCompteEmployeur = "890";
            var numeroUnique = "001";
            var extension = ".xml";

            var fileName = prefix + year + periodeType + periodeNumber + "-" + compteEmployeur + suffixeCompteEmployeur + "-" + numeroUnique + extension;
            return fileName;
        }
    </script>

</body>

</html>