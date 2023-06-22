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
                            <option value="mensuel">Mensuel</option>
                            <option value="trimestriel">Trimestriel</option>
                            <option value="annuel">Annuel</option>
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

            if (periode === "mensuel") {
                // Create the mensuelly date form (mensuel and annuel)
                var mensuelInput = document.createElement("input");
                mensuelInput.setAttribute("type", "number");
                mensuelInput.setAttribute("class", "form-control mb-3");
                mensuelInput.setAttribute("placeholder", "Mois");
                mensuelInput.setAttribute("name", "mensuel");
                mensuelInput.setAttribute("required", "required");
                mensuelInput.setAttribute("min", "1");
                mensuelInput.setAttribute("max", "12");

                var annuelInput = document.createElement("input");
                annuelInput.setAttribute("type", "number");
                annuelInput.setAttribute("class", "form-control mb-3");
                annuelInput.setAttribute("placeholder", "Année");
                annuelInput.setAttribute("name", "annuel");
                annuelInput.setAttribute("required", "required");
                annuelInput.setAttribute("min", "1900");
                annuelInput.setAttribute("max", "2100");

                dateFields.appendChild(mensuelInput);
                dateFields.appendChild(annuelInput);
            } else if (periode === "trimestriel") {
                // Create the quarterly selection form
                var trimestrielSelect = document.createElement("select");
                trimestrielSelect.setAttribute("class", "form-control mb-3");
                trimestrielSelect.setAttribute("name", "trimestriel");
                trimestrielSelect.setAttribute("required", "required");

                var trimestriels = ["Trimestre 1", "Trimestre 2", "Trimestre 3", "Trimestre 4"];
                var trimestrielValues = ["01", "02", "03", "04"];

                for (var i = 0; i < trimestriels.length; i++) {
                    var option = document.createElement("option");
                    option.value = trimestrielValues[i];
                    option.text = trimestriels[i];
                    trimestrielSelect.appendChild(option);
                }

                var annuelInput = document.createElement("input");
                annuelInput.setAttribute("type", "number");
                annuelInput.setAttribute("class", "form-control mb-3");
                annuelInput.setAttribute("placeholder", "Année");
                annuelInput.setAttribute("name", "annuel");
                annuelInput.setAttribute("required", "required");
                annuelInput.setAttribute("min", "1900");
                annuelInput.setAttribute("max", "2100");

                dateFields.appendChild(trimestrielSelect);
                dateFields.appendChild(annuelInput);
            } else if (periode === "annuel") {
                // Create the date picker for a annuel
                var annuelInput = document.createElement("input");
                annuelInput.setAttribute("type", "number");
                annuelInput.setAttribute("class", "form-control mb-3");
                annuelInput.setAttribute("placeholder", "Année");
                annuelInput.setAttribute("name", "annuel");
                annuelInput.setAttribute("required", "required");
                annuelInput.setAttribute("min", "1900");
                annuelInput.setAttribute("max", "2100");

                dateFields.appendChild(annuelInput);
            }

            // Display the date fields
            dateFields.style.display = "block";
        }

        function generateXML() {
            var periode = document.getElementById("periode").value;

            if (periode === "mensuel") {
                var mensuel = document.getElementsByName("mensuel")[0].value;
                var annuel = document.getElementsByName("annuel")[0].value;

                // Validate the mensuel and annuel values (add additional conditions if necessary)
                if (mensuel < 1 || mensuel > 12) {
                    alert("Mois invalide");
                    return false;
                }
                if (annuel < 1900 || annuel > 2100) {
                    alert("Année invalide");
                    return false;
                }
            } else if (periode === "trimestriel") {
                var trimestriel = document.getElementsByName("trimestriel")[0].value;
                var annuel = document.getElementsByName("annuel")[0].value;

                // Validate the values of the quarter and the annuel (add additional conditions if necessary)
                // Here, we validate only if the annuel is a 4-digit number
                if (!annuel.match(/^\d{4}$/)) {
                    alert("Année invalide");
                    return false;
                }
            }

            // Generate the XML file name
            var fileName = generateFileName(periode, annuel, mensuel, trimestriel);

            // Submit the form with the XML file name
            document.getElementById("xmlFileName").value = fileName;

            return true;
        }

        function generateFileName(periode, annuel, mensuel, trimestriel) {
            var prefix = "DN-";
            var periodeType = "";
            var periodeNumber = "";

            if (periode === "mensuel") {
                periodeType = "M";
                periodeNumber = ("0" + mensuel).slice(-2);
            } else if (periode === "trimestriel") {
                periodeType = "T";
                periodeNumber = trimestriel.split("-")[0];
            } else if (periode === "annuel") {
                periodeType = "A";
                periodeNumber = "01";
            }

            var compteEmployeur = "1234567";
            var suffixeCompteEmployeur = "890";
            var numeroUnique = "001";
            var extension = ".xml";

            var fileName = prefix + annuel + periodeType + periodeNumber + "-" + compteEmployeur + suffixeCompteEmployeur + "-" + numeroUnique + extension;
            return fileName;
        }
    </script>

</body>

</html>