# EDI DNT

Ce projet est une application web pour la génération de fichiers XML conformes au format EDI DNT (Déclaration Nominative Trimestrielle) utilisé pour la déclaration d'employés.

## Fonctionnalités

- Génération de fichiers XML pour les déclarations mensuelles, trimestrielles et annuelles.
- Prise en charge des différents formats de période.
- Validation des données de période saisies par l'utilisateur.
- Génération automatique des noms de fichiers conformes à la structure EDI DNT.

## Prérequis

Avant d'exécuter cette application, assurez-vous d'avoir installé les éléments suivants :

- Serveur web (Apache, Nginx, etc.)
- PHP version 7.0 ou supérieure

## Installation

1. Clonez ce référentiel sur votre machine locale :

`git clone https://github.com/VOTRE_UTILISATEUR/edi-dnt.git`


2. Configurez votre serveur web pour qu'il pointe vers le répertoire racine de l'application.

3. Ouvrez le fichier `config.php` et configurez les paramètres nécessaires (par exemple, la base de données).

4. Accédez à l'application via votre navigateur web :

`http://localhost/edi-dnt`


## Utilisation

1. Sur la page d'accueil, sélectionnez la période souhaitée (mensuelle, trimestrielle ou annuelle).

2. Saisissez les informations requises pour la période sélectionnée (mois, année, trimestres, etc.).

3. Cliquez sur le bouton "Générer" pour générer le fichier XML correspondant à la période et aux données saisies.

4. Le fichier XML sera automatiquement téléchargé sur votre machine.

## Auteur

[Your Name](https://github.com/VOTRE_UTILISATEUR)

N'hésitez pas à me contacter si vous avez des questions ou des commentaires.

## Licence

Ce projet est sous licence [MIT](LICENSE).
