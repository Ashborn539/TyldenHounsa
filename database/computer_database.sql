-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 23 sep. 2026 à 19:02
-- Version du serveur : 8.4.7
-- Version de PHP : 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `computer_database`
--

-- --------------------------------------------------------

--
-- Structure de la table `articles`
--

DROP TABLE IF EXISTS `Commandes`;
DROP TABLE IF EXISTS `articles`;
CREATE TABLE IF NOT EXISTS `articles` (
  `id_art` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantite` int NOT NULL DEFAULT '0',
  `prix` float NOT NULL,
  `url_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_art`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id_user` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adr` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `num` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mdp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `mail` (`mail`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `Commandes`
--

CREATE TABLE IF NOT EXISTS `Commandes` (
  `id_commande` int NOT NULL AUTO_INCREMENT,
  `id_art` int NOT NULL,
  `id_client` int UNSIGNED NOT NULL,
  `quantite` int NOT NULL,
  `envoi` boolean NOT NULL DEFAULT FALSE,
  PRIMARY KEY (`id_commande`),
  KEY `idx_commandes_article` (`id_art`),
  KEY `idx_commandes_client` (`id_client`),
  CONSTRAINT `fk_commandes_article` FOREIGN KEY (`id_art`) REFERENCES `articles` (`id_art`),
  CONSTRAINT `fk_commandes_client` FOREIGN KEY (`id_client`) REFERENCES `user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Déchargement des données de la table `articles`
--

INSERT INTO `articles` (`id_art`, `nom`, `quantite`, `prix`, `url_photo`, `description`) VALUES
(1, 'Greed Ultra 7', 24, 299.99, 'assets/images/Greed-7-Ultra-PC-Intel-Core-i7-Proceeur-4-0-GHz-Ordinateur-de-Bureau-Tours-PC-16-GO-DDR4-1TO-D-1TO-HDD-DVD-RW-USB-3-1-WiFi-Windows-11-Pro.jpg', 'PC Greed Ultra 7 avec processeur Intel Core Ultra 7, 16 Go de mémoire RAM, SSD de 512 Go, écran Full HD, connexion Wi-Fi et système Windows 11.'),
(2, 'Greed MK2 PC Gamer', 10, 1299.99, 'assets/images/Greed-MK2-PC-Gamer-AMD-Ryzen-7-5700X-Nvidia-Geforce-RTX-5060Ti-Ordinateur-de-Bureau-PC-Gaming-4-6-GHz-4K-Raytracing-32GO-DDR4-RAM-1To-D-WiFi-Windows-11-Pro.jpg', 'PC gaming avec processeur AMD Ryzen 7 5700X, carte graphique Nvidia GeForce RTX 5060 Ti, 32 Go de RAM DDR4, stockage 1 To, Wi-Fi et Windows 11 Pro.'),
(3, 'Lenovo Yoga 7 2-en-1', 8, 999.99, 'assets/images/PC-2-en-1-Lenovo-Yoga-7-16AGP11-16-OLED-120-Hz-Ecran-tactile-Copilot-PC-AMD-Ryzen-AI-7-16-Go-RAM-512-Go-D-Gris.jpg', 'PC convertible Lenovo Yoga 7 avec écran OLED tactile de 16 pouces à 120 Hz, processeur AMD Ryzen AI 7, 16 Go de RAM et SSD de 512 Go.'),
(4, 'Mred CBT047-03', 12, 1099.99, 'assets/images/PC-Gaming-Mred-CBT047-03-Intel-Core-i5-16-Go-RAM-1-To-D-Nvidia-GeForce-RTX-5060-Noir.jpg', 'PC gaming avec processeur Intel Core i5, carte graphique Nvidia GeForce RTX 5060, 16 Go de RAM et stockage de 1 To.'),
(5, 'PC portable Intel i3 15,6 pouces', 15, 499.99, 'assets/images/PC-portable-15-6-Intel-I3-16Go-RAM-DDR4-512Go-D-Windows-11PRO-WiFi5-HDMI-Ordinateur-Portable.jpg', 'PC portable avec écran de 15,6 pouces, processeur Intel Core i3, 16 Go de RAM DDR4, SSD de 512 Go, Wi-Fi 5, HDMI et Windows 11 Pro.'),
(6, 'Asus ROG Strix G G531GT', 6, 899.99, 'assets/images/PC-Portable-Asus-ROG-STRIX-G-G531GT-AL030T-Intel-Core-i7-8-Go-RAM-1-To-SATA-256-Go-D.jpg', 'PC portable gaming Asus ROG Strix avec processeur Intel Core i7, 8 Go de RAM, disque de 1 To et SSD de 256 Go.'),
(7, 'Asus Vivobook S17', 9, 749.99, 'assets/images/PC-portable-Asus-Vivobook-S17-X1704VA-AU1224W-17-3-Full-HD-60-Hz-Intel-Core-5-16-Go-RAM-512-Go-D-Gris.jpg', 'PC portable Asus Vivobook avec écran Full HD de 17,3 pouces à 60 Hz, processeur Intel Core 5, 16 Go de RAM et SSD de 512 Go.'),
(8, 'Asus Zenbook A14', 7, 1199.99, 'assets/images/PC-portable-Asus-Zenbook-A14-UX3407QA-QD219W-14-OLED-Copilot-PC-Snapdragon-X-16-Go-RAM-512-Go-D-Beige.jpg', 'PC portable Asus Zenbook avec écran OLED de 14 pouces, processeur Snapdragon X, 16 Go de RAM et SSD de 512 Go.'),
(9, 'Gigabyte AERO X16', 5, 1599.99, 'assets/images/PC-portable-Gaming-Gigabyte-aero-X16-3VHL3FRC94DH-16-WQXGA-165-Hz-Copilot-PC-AMD-Ryzen-9-16-Go-RAM-1-To-D-Nvidia-GeForce-RTX-5060-Gris-sideral.jpg', 'PC portable gaming Gigabyte AERO X16 avec écran WQXGA de 16 pouces à 165 Hz, processeur AMD Ryzen 9, carte graphique RTX 5060, 16 Go de RAM et SSD de 1 To.'),
(10, 'HP Omen Max 16', 4, 2499.99, 'assets/images/PC-portable-gaming-HP-Omen-Max-16-ak0012nf-16-60-240-Hz-AMD-Ryzen-AI-9-32-Go-RAM-1-To-D-Nvidia-GeForce-RTX-5080-Noir-celeste.jpg', 'PC portable gaming HP Omen Max avec écran de 16 pouces jusqu’à 240 Hz, processeur AMD Ryzen AI 9, carte graphique Nvidia GeForce RTX 5080, 32 Go de RAM et SSD de 1 To.'),
(11, 'HP OmniBook X Flip', 6, 1399.99, 'assets/images/PC-Portable-HP-OmniBook-X-Flip-2-en-1-Next-Gen-14-fm0013nf-Copilot-PC-Intel-Core-AI-Ultra-7-32-Go-RAM-1-To-D-Bleu-atmospherique.jpg', 'PC convertible HP OmniBook X Flip avec écran de 14 pouces, processeur Intel Core Ultra 7, 32 Go de RAM et SSD de 1 To.'),
(12, 'Vibox PC Gamer', 11, 999.99, 'assets/images/Vibox-PC-Gamer-Ryzen-7-5700X-4-6-GHz-RTX-5060-8-Go-16-Go-RAM-1-To-NVMe-Windows-11-WiFi.jpg', 'PC gaming Vibox avec processeur AMD Ryzen 7 5700X à 4,6 GHz, carte graphique RTX 5060, 16 Go de RAM, SSD NVMe de 1 To, Wi-Fi et Windows 11.'),
(13, 'VIST Stellar', 8, 1149.99, 'assets/images/VIST-Stellar-PC-Gaming-Ryzen-7-5700X-RAM-32Go-RTX-5060-D-1To-M-2-WIFI-Windows-11-Pro.jpg', 'PC gaming VIST Stellar avec processeur AMD Ryzen 7 5700X, carte graphique RTX 5060, 32 Go de RAM, SSD M.2 de 1 To, Wi-Fi et Windows 11 Pro.'),
(14, 'Microsoft Surface Pro 7', 10, 599.99, 'assets/images/PC-Hybride-Microsoft-Surface-Pro-7-12-3-Intel-Core-i7-16-Go-RAM-256-Go-D-Argent-Reconditionne.jpg', 'PC hybride Microsoft Surface Pro 7 avec processeur Intel Core i7, 16 Go de RAM et SSD de 256 Go.'),
(15, 'Sedatech PC Gamer Pro Watercooling Vision XL', 5, 4999.99, 'assets/images/Sedatech-PC-Gamer-Pro-Watercooling-Vision-XL-Intel-Core-Ultra-9-285K-RTX5090-128Go-DDR5-4To-D-M-2-Windows-11.jpg', 'PC gamer Sedatech avec processeur Intel Core Ultra 9 285K, carte graphique RTX 5090, 128 Go de RAM et SSD de 4 To.'),
(16, 'Lenovo ThinkPad T14 Gen 1', 8, 449.99, 'assets/images/PC-portable-Lenovo-ThinkPad-T14-Gen-1-14-Full-HD-AMD-Ryzen-5-Pro-16-Go-RAM-256-Go-D-Noir-Reconditionne-Grade-B.jpg', 'PC portable Lenovo ThinkPad T14 Gen 1 avec processeur AMD Ryzen 5 Pro, 16 Go de RAM et SSD de 256 Go.');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
