CREATE DATABASE `Photogram` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `Photogram`;

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `post_id` (`post_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `comments` (`id`, `user_id`, `post_id`, `comment`, `created_at`) VALUES
(20,	26,	31,	'1st comment',	'2024-06-25 11:30:30'),
(21,	27,	31,	'pinon comment',	'2024-06-25 11:31:11'),
(23,	29,	33,	'comment by pinon',	'2024-08-20 18:26:35'),
(24,	31,	34,	'post by jerin',	'2024-08-20 18:29:08'),
(25,	26,	35,	'gyjg',	'2024-08-21 11:21:32');

DROP TABLE IF EXISTS `likes`;
CREATE TABLE `likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_like` (`user_id`,`post_id`),
  KEY `post_id` (`post_id`),
  CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `likes` (`id`, `user_id`, `post_id`, `created_at`) VALUES
(28,	26,	31,	'2024-06-25 11:30:23'),
(29,	27,	31,	'2024-06-25 11:31:03'),
(31,	29,	33,	'2024-08-20 18:26:25'),
(32,	31,	34,	'2024-08-20 18:29:02'),
(33,	26,	35,	'2024-08-21 11:21:27');

DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_type` enum('image','video') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `posts` (`id`, `user_id`, `description`, `file_path`, `file_type`, `created_at`) VALUES
(31,	26,	'this is a video file',	'/Photogram/_pages/uploads/667aaa489bb75_Screenshot from 2024-06-25 16-25-11.png',	'image',	'2024-06-25 11:30:16'),
(33,	29,	'test post',	'/Photogram/_pages/uploads/66c4dfcd74352_Screenshot from 2024-08-16 17-39-17.png',	'image',	'2024-08-20 18:26:21'),
(34,	30,	'post no 3',	'/Photogram/_pages/uploads/66c4e04462ae2_Screenshot from 2024-08-16 17-39-41.png',	'image',	'2024-08-20 18:28:20'),
(35,	26,	'test post',	'/Photogram/_pages/uploads/66c5cda66c806_Screenshot from 2024-08-20 23-53-45.png',	'image',	'2024-08-21 11:21:10');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(26,	'dharshan',	'dharshankumarlearn@gmail.com',	'$2y$10$87x.0dIoU9TSl8woC.sj9.uQrAMXxM19kPS64Z5xFUnyz.88ww/he',	'2024-06-25 11:29:28'),
(27,	'pinon',	'jdharshankumar18@gmail.com',	'$2y$10$4hMWmu95pZR7R6DTfp/TXuMd6OB3Mv9B1L1hqCPG5cWr03eb718ka',	'2024-06-25 11:30:53'),
(28,	'benjamin',	'benjamin@gmail.com',	'$2y$10$mPxS1t6Vd5onBdoCvrBi2uFOvWqDWZNYW3HSOGtXBd.lUzJQBIUJ.',	'2024-08-20 18:15:55'),
(29,	'pinonravi',	'pinon@gmail.com',	'$2y$10$lc1xJTKI901R5HFlNh9ewOol.D15vC4RFXn31sZJNiPHtIpN5ZrRG',	'2024-08-20 18:24:11'),
(30,	'hari varman',	'hari@gmail.com',	'$2y$10$Wf0DjEdBhrj9jBD.4JFkIemR.rzBe4qrx5dJbqnRSs99LyoKTyRgi',	'2024-08-20 18:27:13'),
(31,	'jerin',	'jerin@gmail.com',	'$2y$10$LYfs5F8VuFJN0X4yIyxyWeyrs8FBFsEQlGmwI/bSNvCUProT21YQi',	'2024-08-20 18:28:38');
