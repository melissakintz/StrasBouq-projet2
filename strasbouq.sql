-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 03 mai 2021 à 09:56
-- Version du serveur :  8.0.23
-- Version de PHP : 7.3.24-(to be removed in future macOS)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `strasbouq`
--

-- --------------------------------------------------------

--
-- Structure de la table `bouquetCustomer`
--

CREATE TABLE `bouquetCustomer` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `bouquetVitrine`
--

CREATE TABLE `bouquetVitrine` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `bouquetVitrine`
--

INSERT INTO `bouquetVitrine` (`id`, `name`, `image`) VALUES
(1, 'Nom', '');

-- --------------------------------------------------------

--
-- Structure de la table `command`
--

CREATE TABLE `command` (
  `id` int NOT NULL,
  `totalAmount` float NOT NULL,
  `dateOrder` datetime NOT NULL,
  `datePick` datetime NOT NULL,
  `customer_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `command`
--

INSERT INTO `command` (`id`, `totalAmount`, `dateOrder`, `datePick`, `customer_id`) VALUES
(101, 5, '2021-05-03 08:10:07', '2021-05-14 15:59:00', 23),
(102, 8, '2021-05-03 08:23:40', '2021-05-14 15:59:00', 23),
(103, 4, '2021-05-03 08:24:44', '2021-05-21 13:14:00', 23),
(104, 4, '2021-05-03 08:25:18', '2021-05-13 14:14:00', 23),
(105, 4, '2021-05-03 08:25:51', '2021-05-22 13:16:00', 23),
(106, 4, '2021-05-03 08:26:53', '2021-05-20 13:41:00', 23),
(107, 10, '2021-05-03 08:27:42', '2021-05-21 15:09:00', 23),
(108, 10, '2021-05-03 08:33:12', '2021-05-06 14:59:00', 23),
(109, 12, '2021-05-03 08:35:17', '2021-06-12 13:09:00', 23);

-- --------------------------------------------------------

--
-- Structure de la table `commandDetails`
--

CREATE TABLE `commandDetails` (
  `stock_id` int NOT NULL,
  `quantity` int NOT NULL,
  `command_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `commandDetails`
--

INSERT INTO `commandDetails` (`stock_id`, `quantity`, `command_id`) VALUES
(4, 2, 102),
(4, 1, 103),
(4, 1, 104),
(4, 1, 105),
(4, 1, 106),
(3, 1, 107),
(4, 1, 107),
(1, 2, 108),
(3, 2, 109);

-- --------------------------------------------------------

--
-- Structure de la table `commandStatus`
--

CREATE TABLE `commandStatus` (
  `command_id` int NOT NULL,
  `ispick` tinyint(1) NOT NULL,
  `isprepared` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `commandStatus`
--

INSERT INTO `commandStatus` (`command_id`, `ispick`, `isprepared`) VALUES
(101, 0, 0),
(107, 0, 0),
(108, 0, 0),
(109, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `customer`
--

CREATE TABLE `customer` (
  `id` int NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` int NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `customer`
--

INSERT INTO `customer` (`id`, `firstname`, `lastname`, `email`, `phone`, `password`) VALUES
(17, 'Madame', 'Croque', 'croque@madame.fr', 789554321, '$2y$10$.HjjJIHF3Xsd2pmz8.RRvea/OifmL/XJErQXD.DG1eSlsCmQqRqiu'),
(23, 'Mélissa', 'Kintz', 'kntzmelissa@gmail.com', 684991520, '$2y$10$W0M1FQsoYMH5L6ozQXUE4.latofMJ666N5jcC9tl9VHz3kipAW8Ba');

-- --------------------------------------------------------

--
-- Structure de la table `stock`
--

CREATE TABLE `stock` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `avalaibleNumber` int NOT NULL,
  `price` int NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `stock`
--

INSERT INTO `stock` (`id`, `name`, `description`, `avalaibleNumber`, `price`, `image`) VALUES
(1, 'Tulipe rouges', 'expriment un amour sincère et fort. On offre un bouquet de tulipes rouges pour faire une déclaration d’amour qui mêle sensualité et plaisir.', 200, 5, 'tulipe.png'),
(3, 'tulipe rose', 'expriment une affection, elles symbolisent la naissance de votre amour. Elles sont douces et délicates et reflètent l’innocence des sentiments.', 197, 6, 'tulipe.png'),
(4, 'rose rouge', 'Depuis toujours la rose rouge rime avec Amour et Passion, symbole fort d\'un attachement ardent.', 249, 4, 'tulipe.png'),
(5, 'Rose noire', 'Rose noire naturelle colorée artisanalement pétales par pétales pour parvenir à une rose exceptionnelle introuvable ailleurs !', 100, 15, 'rosenoire.png'),
(6, 'Iris', 'L’Iris est un genre de plantes vivaces à rhizomes ou à bulbes de la famille des Iridacées.', 175, 6, 'iris.png');

-- --------------------------------------------------------

--
-- Structure de la table `stock_bouquetCustomer`
--

CREATE TABLE `stock_bouquetCustomer` (
  `stock_id` int NOT NULL,
  `bouquetCustomer_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `stock_bouquetVitrine`
--

CREATE TABLE `stock_bouquetVitrine` (
  `stock_id` int NOT NULL,
  `bouquetVitrine_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `stock_bouquetVitrine`
--

INSERT INTO `stock_bouquetVitrine` (`stock_id`, `bouquetVitrine_id`) VALUES
(1, 1),
(1, 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `bouquetCustomer`
--
ALTER TABLE `bouquetCustomer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bouquetcustomer_ibfk_2` (`customer_id`);

--
-- Index pour la table `bouquetVitrine`
--
ALTER TABLE `bouquetVitrine`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `command`
--
ALTER TABLE `command`
  ADD PRIMARY KEY (`id`),
  ADD KEY `command_ibfk_1` (`customer_id`);

--
-- Index pour la table `commandDetails`
--
ALTER TABLE `commandDetails`
  ADD KEY `commanddetails_ibfk_1` (`stock_id`),
  ADD KEY `command_id` (`command_id`) USING BTREE;

--
-- Index pour la table `commandStatus`
--
ALTER TABLE `commandStatus`
  ADD KEY `command_id` (`command_id`) USING BTREE;

--
-- Index pour la table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `stock_bouquetCustomer`
--
ALTER TABLE `stock_bouquetCustomer`
  ADD KEY `stock_bouquetcustomer_ibfk_1` (`stock_id`),
  ADD KEY `stock_bouquetCustomer_ibfk_2` (`bouquetCustomer_id`);

--
-- Index pour la table `stock_bouquetVitrine`
--
ALTER TABLE `stock_bouquetVitrine`
  ADD KEY `stock_bouquetVitrine_ibfk_1` (`stock_id`),
  ADD KEY `stock_bouquetVitrine_ibfk_2` (`bouquetVitrine_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `bouquetCustomer`
--
ALTER TABLE `bouquetCustomer`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `bouquetVitrine`
--
ALTER TABLE `bouquetVitrine`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `command`
--
ALTER TABLE `command`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT pour la table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `bouquetCustomer`
--
ALTER TABLE `bouquetCustomer`
  ADD CONSTRAINT `bouquetcustomer_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `command`
--
ALTER TABLE `command`
  ADD CONSTRAINT `command_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `command_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `commandDetails`
--
ALTER TABLE `commandDetails`
  ADD CONSTRAINT `commanddetails_ibfk_1` FOREIGN KEY (`stock_id`) REFERENCES `stock` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `commanddetails_ibfk_2` FOREIGN KEY (`command_id`) REFERENCES `command` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `commandStatus`
--
ALTER TABLE `commandStatus`
  ADD CONSTRAINT `commandstatus_ibfk_1` FOREIGN KEY (`command_id`) REFERENCES `command` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `stock_bouquetCustomer`
--
ALTER TABLE `stock_bouquetCustomer`
  ADD CONSTRAINT `stock_bouquetcustomer_ibfk_1` FOREIGN KEY (`stock_id`) REFERENCES `stock` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_bouquetCustomer_ibfk_2` FOREIGN KEY (`bouquetCustomer_id`) REFERENCES `bouquetCustomer` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `stock_bouquetVitrine`
--
ALTER TABLE `stock_bouquetVitrine`
  ADD CONSTRAINT `stock_bouquetVitrine_ibfk_1` FOREIGN KEY (`stock_id`) REFERENCES `stock` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_bouquetVitrine_ibfk_2` FOREIGN KEY (`bouquetVitrine_id`) REFERENCES `bouquetVitrine` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
