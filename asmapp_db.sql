-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Gazdă: 127.0.0.1
-- Timp de generare: mart. 11, 2020 la 01:10 AM
-- Versiune server: 10.1.37-MariaDB
-- Versiune PHP: 7.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Bază de date: `asmapp`
--

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `access_right`
--

CREATE TABLE `access_right` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Eliminarea datelor din tabel `access_right`
--

INSERT INTO `access_right` (`id`, `name`) VALUES
(8, 'addAccessRight'),
(6, 'addApp'),
(7, 'addRole'),
(5, 'addUser'),
(1, 'changePassword'),
(4, 'login'),
(3, 'manageUsers');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `app`
--

CREATE TABLE `app` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Eliminarea datelor din tabel `app`
--

INSERT INTO `app` (`id`, `name`) VALUES
(4, 'aaa'),
(5, 'app123'),
(2, 'app2'),
(1, 'asmapp'),
(6, 'bbbb');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `app_access_right`
--

CREATE TABLE `app_access_right` (
  `id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `access_right_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Eliminarea datelor din tabel `app_access_right`
--

INSERT INTO `app_access_right` (`id`, `app_id`, `access_right_id`) VALUES
(1, 1, 1),
(3, 1, 3),
(2, 1, 4);

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `app_role`
--

CREATE TABLE `app_role` (
  `id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Eliminarea datelor din tabel `app_role`
--

INSERT INTO `app_role` (`id`, `app_id`, `role_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 1),
(4, 2, 2),
(5, 2, 3),
(6, 4, 4),
(8, 5, 4);

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Eliminarea datelor din tabel `role`
--

INSERT INTO `role` (`id`, `name`) VALUES
(2, 'admin'),
(4, 'master'),
(3, 'supervisor'),
(1, 'user');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `role_access_right`
--

CREATE TABLE `role_access_right` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `access_right_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Eliminarea datelor din tabel `role_access_right`
--

INSERT INTO `role_access_right` (`id`, `role_id`, `access_right_id`) VALUES
(2, 1, 1),
(1, 1, 4),
(4, 2, 1),
(5, 2, 3),
(3, 2, 4),
(6, 2, 8);

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT '1',
  `app_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Eliminarea datelor din tabel `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `role_id`, `app_id`) VALUES
(1, 'emma.rus93@gmail.com', '$2y$10$/y5KfX0UpXCubV0d2ryRFO6yKASpf6UD6hhzxu3hsfX14xRrsRAwq', 2, 1),
(2, 'emma.rus93@yahoo.com', '$2y$10$/lVCL.ifXcaexSeGTra.MeQX6UqeTUDaJ9klEi0U/3GSbUycd.v76', 1, 1),
(9, 'emma.rus@gmail.com', '$2y$10$WbVV/F0nZris6fJdWr1.RewLX6Cc9n9AfP/pLckyt/1g7G1/F0CLS', 1, 1),
(10, 'emma@gmail.com', '$2y$10$WbVV/F0nZris6fJdWr1.RewLX6Cc9n9AfP/pLckyt/1g7G1/F0CLS', 1, 1),
(11, 'emma.rus@yahoo.com1', '$2y$10$sg.u8CRIX.zcRIQVKmeef.hHjxZYiQQY7XdxKA6rHd4qzH9MsOJey', 2, 2);

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `user_access_right`
--

CREATE TABLE `user_access_right` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `access_right_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Eliminarea datelor din tabel `user_access_right`
--

INSERT INTO `user_access_right` (`id`, `user_id`, `access_right_id`) VALUES
(3, 1, 1),
(4, 1, 3),
(5, 1, 5),
(6, 1, 6),
(7, 1, 7),
(8, 1, 8),
(2, 5, 1),
(1, 5, 4);

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `user_settings`
--

CREATE TABLE `user_settings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `last_request_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Eliminarea datelor din tabel `user_settings`
--

INSERT INTO `user_settings` (`id`, `user_id`, `session_id`, `last_request_at`) VALUES
(1, 1, '1-5e682074e2f0e', '2020-03-11 00:27:02'),
(9, 2, '2-5e67f5ddce6f2', '2020-03-10 21:17:33');

--
-- Indexuri pentru tabele eliminate
--

--
-- Indexuri pentru tabele `access_right`
--
ALTER TABLE `access_right`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `access_right_name_unique` (`name`) USING BTREE;

--
-- Indexuri pentru tabele `app`
--
ALTER TABLE `app`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `app_name_unique` (`name`);

--
-- Indexuri pentru tabele `app_access_right`
--
ALTER TABLE `app_access_right`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `app_access_right_unique` (`app_id`,`access_right_id`),
  ADD KEY `app_access_right_index` (`app_id`),
  ADD KEY `access_right_app_index` (`access_right_id`);

--
-- Indexuri pentru tabele `app_role`
--
ALTER TABLE `app_role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `app_role_unique` (`app_id`,`role_id`) USING BTREE,
  ADD KEY `app_role_index` (`app_id`) USING BTREE,
  ADD KEY `role_app_index` (`role_id`) USING BTREE;

--
-- Indexuri pentru tabele `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name_unique` (`name`);

--
-- Indexuri pentru tabele `role_access_right`
--
ALTER TABLE `role_access_right`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_access_right` (`role_id`,`access_right_id`),
  ADD KEY `role_access_right_index` (`role_id`),
  ADD KEY `access_right_role_index` (`access_right_id`);

--
-- Indexuri pentru tabele `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username_unique` (`username`),
  ADD KEY `role_id_index` (`role_id`) USING BTREE,
  ADD KEY `app_id_index` (`app_id`);

--
-- Indexuri pentru tabele `user_access_right`
--
ALTER TABLE `user_access_right`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_access_right_unique` (`user_id`,`access_right_id`) USING BTREE,
  ADD KEY `user_access_right_index` (`user_id`),
  ADD KEY `access_right_user_index` (`access_right_id`);

--
-- Indexuri pentru tabele `user_settings`
--
ALTER TABLE `user_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id_unique` (`user_id`),
  ADD KEY `user_id_index` (`user_id`);

--
-- AUTO_INCREMENT pentru tabele eliminate
--

--
-- AUTO_INCREMENT pentru tabele `access_right`
--
ALTER TABLE `access_right`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pentru tabele `app`
--
ALTER TABLE `app`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pentru tabele `app_access_right`
--
ALTER TABLE `app_access_right`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pentru tabele `app_role`
--
ALTER TABLE `app_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pentru tabele `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pentru tabele `role_access_right`
--
ALTER TABLE `role_access_right`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pentru tabele `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pentru tabele `user_access_right`
--
ALTER TABLE `user_access_right`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pentru tabele `user_settings`
--
ALTER TABLE `user_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constrângeri pentru tabele eliminate
--

--
-- Constrângeri pentru tabele `app_access_right`
--
ALTER TABLE `app_access_right`
  ADD CONSTRAINT `app_access_right_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `app` (`id`),
  ADD CONSTRAINT `app_access_right_ibfk_2` FOREIGN KEY (`access_right_id`) REFERENCES `access_right` (`id`);

--
-- Constrângeri pentru tabele `app_role`
--
ALTER TABLE `app_role`
  ADD CONSTRAINT `app_role_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `app` (`id`),
  ADD CONSTRAINT `app_role_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`);

--
-- Constrângeri pentru tabele `role_access_right`
--
ALTER TABLE `role_access_right`
  ADD CONSTRAINT `role_access_right_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`),
  ADD CONSTRAINT `role_access_right_ibfk_2` FOREIGN KEY (`access_right_id`) REFERENCES `access_right` (`id`);

--
-- Constrângeri pentru tabele `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`),
  ADD CONSTRAINT `user_ibfk_2` FOREIGN KEY (`app_id`) REFERENCES `app` (`id`);

--
-- Constrângeri pentru tabele `user_access_right`
--
ALTER TABLE `user_access_right`
  ADD CONSTRAINT `user_access_right_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `user_access_right_ibfk_2` FOREIGN KEY (`access_right_id`) REFERENCES `access_right` (`id`);

--
-- Constrângeri pentru tabele `user_settings`
--
ALTER TABLE `user_settings`
  ADD CONSTRAINT `user_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
