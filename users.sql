-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 07 mai 2026 à 16:46
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `myapp`
--

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `role` enum('super_admin','admin_regional','chef_agence','agent','client') NOT NULL DEFAULT 'client',
  `agence_id` int(11) DEFAULT NULL,
  `2fa_secret` varchar(255) DEFAULT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `otp` varchar(10) DEFAULT NULL,
  `otp_expires_at` datetime DEFAULT NULL,
  `otp_created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `nom`, `prenom`, `telephone`, `role`, `agence_id`, `2fa_secret`, `email_verified_at`, `remember_token`, `reset_token`, `created_at`, `updated_at`, `otp`, `otp_expires_at`, `otp_created_at`) VALUES
(1, 'Lala', 'lala@gmail.com', '$2y$10$DbcMIKjYm12VRFNLtM7EtuQwofrQHUScVwD0VZYOGV3.MoTgw6tGG', 'lala', 'ralahy', '', 'client', NULL, NULL, NULL, NULL, NULL, '2026-03-06 10:43:22', '2026-05-04 11:41:10', NULL, NULL, '2026-04-30 17:02:05'),
(2, 'alizara', 'Tammaly@gmail.com', '$2y$10$8s0xiIik0jixZnJrxPNSSefQjEbqkpxN1c3V57TPX58gKwLCpgJlO', 'alooo', 'soob', '', 'client', NULL, NULL, NULL, NULL, NULL, '2026-03-06 10:43:22', '2026-05-05 09:39:36', NULL, NULL, '2026-04-30 17:02:05'),
(3, 'Rabeza', 'Rb@gmail.com', '$2y$10$i0W3SE1Hjfi/P8ZUNEbKsO9/17OB/bOXitbM//S4N1VICk9ZAwkAK', NULL, NULL, NULL, 'client', NULL, NULL, NULL, NULL, NULL, '2026-03-06 10:43:22', '2026-03-10 09:27:44', NULL, NULL, '2026-04-30 17:02:05'),
(4, 'Christiane', 'Ch@gmail.com', '$2y$10$qDo4RY6b/7TAqCcXO4Ppmez8oTwCYoYv22YRXtCYfVJehqHbcOI9.', NULL, NULL, NULL, 'client', NULL, NULL, NULL, NULL, NULL, '2026-03-06 10:43:22', '2026-03-10 09:27:44', NULL, NULL, '2026-04-30 17:02:05'),
(5, 'Yoann', 'Yoann@gmail.com', '$2y$10$Jm7c30gl/VauUYXI0Go17OqmqhN.lCXgTAktjTJwbxCEWeG6FgOZC', NULL, NULL, NULL, 'super_admin', 17, NULL, NULL, NULL, NULL, '2026-03-06 10:43:22', '2026-03-10 14:53:10', NULL, NULL, '2026-04-30 17:02:05'),
(6, 'Eric', 'Eric@gmail.com', '$2y$10$l0f166GyZb0gVJLgrlntKekzk35CzIQW5MGemxRP/jT6woRd9gwFq', NULL, NULL, NULL, 'client', NULL, NULL, NULL, NULL, NULL, '2026-03-06 10:43:22', '2026-03-10 09:27:44', NULL, NULL, '2026-04-30 17:02:05'),
(7, 'RAKOTO', 'Rkt@gmail.com', '$2y$12$Uoy4K5EKXQ4oqCUjal2loO2octp/819Nrxt5CgLssXxGa4TOiPJyy', NULL, NULL, NULL, 'client', NULL, NULL, NULL, NULL, NULL, '2026-03-06 10:43:22', '2026-03-10 09:27:44', NULL, NULL, '2026-04-30 17:02:05'),
(8, 'RABE', 'rabe@gmail.com', '$2y$12$r1spS9yq/SrqyU.H52DAkeB66qW3SJBJybppV8aW3CmWenXAPGvDe', NULL, NULL, NULL, 'client', NULL, NULL, NULL, NULL, NULL, '2026-03-06 10:43:22', '2026-03-10 09:27:44', NULL, NULL, '2026-04-30 17:02:05'),
(9, 'aina.rabetokotany', 'Aina@gmail.com', '$2y$10$Us5l5DJdmKMbconl.HqP5uz1jo9s62UE.HS4kWWYr.PZjsXQ4DiTm', NULL, NULL, NULL, 'agent', NULL, NULL, NULL, NULL, NULL, '2026-03-06 10:43:22', '2026-03-12 21:09:08', NULL, NULL, '2026-04-30 17:02:05'),
(11, 'lita.ratsimbazafy', 'lita@gmail.com', '$2y$10$8ImxAzURjJtCPMX6l8hmiO7vdosEExVBHMz//u146okJhqqiHH1xW', NULL, NULL, NULL, 'client', NULL, NULL, NULL, NULL, NULL, '2026-03-06 10:43:22', '2026-03-10 09:27:44', NULL, NULL, '2026-04-30 17:02:05'),
(22, 'frédéric.rakotomalala', 'deric@gmail.com', '$2y$10$0/osyhJrmgeI4Z.BKviql.CfjGmezZku6T8ocheaAoIcglIF/Bw.G', 'RAKOTOMALALA', 'Frédéric', '0345560677', 'client', NULL, NULL, NULL, NULL, NULL, '2026-03-06 10:48:53', '2026-03-06 10:48:53', NULL, NULL, '2026-04-30 17:02:05'),
(23, 'RABARY', 'chef.elite@boa.mg', '$2y$12$ay7BRAWxUClNnJKaWGm6I.Jhk/s.jhar3uIu3Qd9cvbbPIDOhHsMy', NULL, NULL, NULL, 'chef_agence', 17, NULL, NULL, NULL, NULL, '2026-03-10 15:21:37', '2026-03-10 15:21:59', NULL, NULL, '2026-04-30 17:02:05'),
(24, 'client.elite', 'client.elite@boa.mg', '$2y$10$DbcMIKjYm12VRFNLtM7EtuQwofrQHUScVwD0VZYOGV3.MoTgw6tGG', 'RASOA', 'Faly', NULL, 'client', 17, NULL, NULL, NULL, NULL, '2026-03-10 15:26:46', '2026-03-10 15:26:46', NULL, NULL, '2026-04-30 17:02:05'),
(25, 'rasoa.faly', 'rasoa.faly@boa.mg', '$2y$10$DbcMIKjYm12VRFNLtM7EtuQwofrQHUScVwD0VZYOGV3.MoTgw6tGG', 'RASOA', 'Faly', NULL, 'client', 17, NULL, NULL, NULL, NULL, '2026-03-10 15:29:15', '2026-03-10 15:29:15', NULL, NULL, '2026-04-30 17:02:05'),
(26, 'Romeo', 'romeo@example.com', '$2y$12$IwnM/h8Nt17IrwBIoh2RNOWVAGoVL8hD.HSOx9ze6pcSTses78Tha', NULL, NULL, NULL, 'client', NULL, NULL, NULL, NULL, NULL, '2026-04-23 05:56:04', '2026-05-04 16:52:08', NULL, NULL, '2026-04-30 17:02:05'),
(28, 'roro', 'roro@gmail.com', '$2y$10$d4n80V7i2BVtfFdlV5tyNeEp1NFXDf4ZU5DzLAkCWLtzsdZYK/NIq', NULL, NULL, NULL, 'super_admin', NULL, NULL, NULL, NULL, NULL, '2026-04-23 14:15:04', '2026-05-04 11:06:45', NULL, NULL, '2026-04-30 17:02:05'),
(30, 'ravaomaria', 'ravao@example.com', '$2y$12$zxRzb/JhO2u/XIjOMpow0OUlKKS3xS0eDuEcYG4JME/9tPG.M7ylC', NULL, NULL, NULL, 'client', NULL, NULL, NULL, NULL, NULL, '2026-04-30 10:16:00', '2026-04-30 10:16:00', NULL, NULL, '2026-04-30 17:02:05'),
(31, 'mikolo', 'mikolohortense@gmail.com', '$2y$12$OGv3xMFyKY4g5jQGffIMIOrpIwD3Ofo8t8IF3Zmz5gOdAfW5mOXDC', NULL, NULL, NULL, 'client', NULL, NULL, NULL, NULL, NULL, '2026-05-04 07:05:35', '2026-05-04 09:38:58', NULL, NULL, '2026-05-04 10:05:35'),
(32, 'yoyo', 'yoyo@gmail.com', '$2y$10$MMd3qYhk4kce274y6uI1luYpfPM9aDMjJfDVUJth3bEdXta3ZwUg2', NULL, NULL, NULL, 'super_admin', NULL, NULL, NULL, NULL, NULL, '2026-05-04 10:57:54', '2026-05-04 11:07:49', '644861', '2026-05-04 13:22:49', '2026-05-04 13:57:54'),
(33, 'Fenohasina', 'raholiarijaonayoann@gmail.com', '$2y$12$2n7mmQV8vyZskkMieR1BQufiLmjEiXCjIxSS6oVyoc30DpfAQpET6', NULL, NULL, NULL, 'client', NULL, NULL, NULL, NULL, NULL, '2026-05-07 14:09:09', '2026-05-07 14:09:59', NULL, NULL, '2026-05-07 17:09:09');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
