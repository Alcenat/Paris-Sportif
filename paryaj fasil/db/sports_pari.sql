-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 17 mars 2025 à 18:52
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
-- Base de données : `sports_pari`
--

-- --------------------------------------------------------

--
-- Structure de la table `bets`
--

CREATE TABLE `bets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `bet_amount` float NOT NULL,
  `chosen_team` varchar(20) NOT NULL,
  `potential_gain` float NOT NULL,
  `bet_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','validated','refunded') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `bets`
--

INSERT INTO `bets` (`id`, `user_id`, `match_id`, `bet_amount`, `chosen_team`, `potential_gain`, `bet_date`, `status`) VALUES
(1, 1, 1, 10, 'team1', 15, '2025-03-17 08:47:23', ''),
(2, 1, 1, 2, 'team2', 3.4, '2025-03-17 09:04:47', '');

-- --------------------------------------------------------

--
-- Structure de la table `deposits`
--

CREATE TABLE `deposits` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` float NOT NULL,
  `method` enum('moncash','paypal','wise') NOT NULL,
  `payment_id` varchar(255) NOT NULL,
  `proof` varchar(255) NOT NULL,
  `comment` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `request_date` datetime DEFAULT current_timestamp(),
  `processed_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `deposits`
--

INSERT INTO `deposits` (`id`, `user_id`, `amount`, `method`, `payment_id`, `proof`, `comment`, `status`, `request_date`, `processed_date`) VALUES
(1, 1, 250, 'moncash', '12345685', 'uploads/deposits/1742218740_OIP.jpeg', '', 'approved', '2025-03-17 06:39:00', '2025-03-17 10:06:23'),
(2, 1, 12345, 'paypal', '1234567890', 'uploads/deposits/1742221669_OIP-removebg-preview.png', '', 'approved', '2025-03-17 07:27:49', '2025-03-17 10:06:26'),
(3, 1, 12, 'moncash', '12345685', 'uploads/deposits/1742233209_R (2).png', '', 'pending', '2025-03-17 10:40:09', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `matches`
--

CREATE TABLE `matches` (
  `id` int(11) NOT NULL,
  `team1` varchar(50) NOT NULL,
  `team2` varchar(50) NOT NULL,
  `odds_team1` float NOT NULL,
  `odds_team2` float NOT NULL,
  `odds_draw` float NOT NULL,
  `event_date` datetime NOT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'football'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `matches`
--

INSERT INTO `matches` (`id`, `team1`, `team2`, `odds_team1`, `odds_team2`, `odds_draw`, `event_date`, `category`) VALUES
(1, 'Real Madrid', 'Barcelone', 1.5, 1.7, 3, '2025-03-18 08:25:00', 'football');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sender` enum('user','admin') NOT NULL,
  `message` text NOT NULL,
  `sent_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`id`, `user_id`, `sender`, `message`, `sent_at`) VALUES
(1, 1, 'admin', 'pasyante nap valide paris a pa two lontan', '2025-03-17 09:03:18');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `unique_id` varchar(255) NOT NULL,
  `account_type` enum('bronze','argent','or') NOT NULL DEFAULT 'bronze',
  `points` float NOT NULL DEFAULT 0,
  `last_login` datetime DEFAULT current_timestamp(),
  `referrals` int(11) NOT NULL DEFAULT 0,
  `role` varchar(50) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `unique_id`, `account_type`, `points`, `last_login`, `referrals`, `role`) VALUES
(1, 'audiolisten03@gmail.com', '$2y$10$kQoQpJSbfj6hK31d915jL.2dk1ETvQKvQviq2FgYQWmSkJIhZLw/K', 'user_67d81536a829d5.66679482', 'bronze', 12601.8, '2025-03-17 06:28:41', 0, 'user'),
(2, 'superadmin@votresite.com', '$2y$10$5Bv2.oxnTwHjGAi5OgW8vOL34x19uDoQrUCkvjnMCRIxuNJp2xaaG', 'unique_superadmin', '', 1500, '2025-03-17 06:27:59', 0, 'user');

-- --------------------------------------------------------

--
-- Structure de la table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `method` enum('moncash','paypal','wise') NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_proof` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `request_date` datetime DEFAULT current_timestamp(),
  `processed_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `withdrawals`
--

INSERT INTO `withdrawals` (`id`, `user_id`, `method`, `amount`, `payment_proof`, `message`, `status`, `request_date`, `processed_date`) VALUES
(1, 1, 'moncash', 0.00, NULL, 'svp', 'rejected', '2025-03-17 06:38:15', NULL),
(2, 2, 'moncash', 0.00, NULL, '', 'approved', '2025-03-17 07:29:38', NULL),
(3, 1, 'moncash', 0.00, NULL, '', 'rejected', '2025-03-17 09:32:58', '2025-03-17 10:28:09'),
(4, 1, 'moncash', 0.00, NULL, '', 'pending', '2025-03-17 10:43:59', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `bets`
--
ALTER TABLE `bets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `match_id` (`match_id`);

--
-- Index pour la table `deposits`
--
ALTER TABLE `deposits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Index pour la table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `bets`
--
ALTER TABLE `bets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `bets`
--
ALTER TABLE `bets`
  ADD CONSTRAINT `bets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bets_ibfk_2` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`);

--
-- Contraintes pour la table `deposits`
--
ALTER TABLE `deposits`
  ADD CONSTRAINT `deposits_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD CONSTRAINT `withdrawals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
