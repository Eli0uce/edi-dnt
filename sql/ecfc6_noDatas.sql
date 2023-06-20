-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 19 juin 2023 à 23:16
-- Version du serveur : 10.4.27-MariaDB
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ecfc6`
--

-- --------------------------------------------------------

--
-- Structure de la table `bulletin`
--

CREATE TABLE `bulletin` (
  `id` int(11) NOT NULL,
  `salarie_id` int(11) UNSIGNED NOT NULL,
  `periode` varchar(6) NOT NULL,
  `brut` int(11) NOT NULL,
  `net` int(11) NOT NULL,
  `nombre_heures` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ligne_bulletin`
--

CREATE TABLE `ligne_bulletin` (
  `id` int(11) NOT NULL,
  `numero` int(11) NOT NULL COMMENT 'numÃ©ro de ligne',
  `bulletin_id` int(11) NOT NULL,
  `rubrique_id` int(11) NOT NULL,
  `libelle` varchar(50) NOT NULL,
  `base` double NOT NULL,
  `nombre` float NOT NULL,
  `taux_salarial` float NOT NULL,
  `montant_salarial` int(11) NOT NULL,
  `taux_patronal` float NOT NULL,
  `montant_patronal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rubrique`
--

CREATE TABLE `rubrique` (
  `id` int(11) NOT NULL,
  `code` varchar(3) NOT NULL,
  `libelle` varchar(50) NOT NULL,
  `nombre` smallint(6) NOT NULL,
  `base` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 = Base / 2 = Montant / 3 = Rubrique de calcul / 4 = qualification / 5 = anciennetÃ© / 6 = soumis / 7 = cumul annuel / 8 = cumul congÃ© / 9 = cumul rubrique / 10 = valeur point',
  `taux_salarial` float NOT NULL DEFAULT 0,
  `montant_salarial` int(11) NOT NULL DEFAULT 0,
  `taux_patronal` float NOT NULL DEFAULT 0,
  `montant_patronal` int(11) NOT NULL DEFAULT 0,
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = gain / 2 = retenue / 3 = constante / 4 = variable'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `salaries`
--

CREATE TABLE `salaries` (
  `id` int(11) UNSIGNED NOT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `dnaissance` date DEFAULT NULL,
  `dembauche` date DEFAULT NULL,
  `drupture` date DEFAULT NULL,
  `numcafat` mediumint(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `societe`
--

CREATE TABLE `societe` (
  `id` int(11) NOT NULL,
  `numerocafat` varchar(11) NOT NULL,
  `enseigne` varchar(200) NOT NULL,
  `ridet` varchar(11) NOT NULL,
  `tauxat` float NOT NULL DEFAULT 0.72,
  `adresse` varchar(250) NOT NULL,
  `commune` varchar(20) NOT NULL,
  `codepostal` mediumint(9) NOT NULL DEFAULT 98800
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `bulletin`
--
ALTER TABLE `bulletin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_bulletin_salaries` (`salarie_id`),
  ADD KEY `periode` (`periode`);

--
-- Index pour la table `ligne_bulletin`
--
ALTER TABLE `ligne_bulletin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `numero` (`numero`),
  ADD KEY `FK__bulletin` (`bulletin_id`),
  ADD KEY `FK__rubrique` (`rubrique_id`);

--
-- Index pour la table `rubrique`
--
ALTER TABLE `rubrique`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `type` (`type`),
  ADD KEY `ordre` (`code`) USING BTREE;

--
-- Index pour la table `salaries`
--
ALTER TABLE `salaries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Index 2` (`numcafat`);
ALTER TABLE `salaries` ADD FULLTEXT KEY `nom` (`nom`);

--
-- Index pour la table `societe`
--
ALTER TABLE `societe`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `bulletin`
--
ALTER TABLE `bulletin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `ligne_bulletin`
--
ALTER TABLE `ligne_bulletin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `rubrique`
--
ALTER TABLE `rubrique`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `salaries`
--
ALTER TABLE `salaries`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `societe`
--
ALTER TABLE `societe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `bulletin`
--
ALTER TABLE `bulletin`
  ADD CONSTRAINT `FK_bulletin_salaries` FOREIGN KEY (`salarie_id`) REFERENCES `salaries` (`id`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `ligne_bulletin`
--
ALTER TABLE `ligne_bulletin`
  ADD CONSTRAINT `FK__bulletin` FOREIGN KEY (`bulletin_id`) REFERENCES `bulletin` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK__rubrique` FOREIGN KEY (`rubrique_id`) REFERENCES `rubrique` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
