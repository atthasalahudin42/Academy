-- Base users table for academy DB
USE academy;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(64) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `login_token` varchar(64) DEFAULT NULL,
  `login_token_expiry` datetime DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT 'avatar.png',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_verification_token` (`verification_token`),
  KEY `idx_login_token` (`login_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
