-- ZoneWhois Database Schema
-- Run this file to create all necessary tables

-- Domains table
CREATE TABLE IF NOT EXISTS `domains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_name` varchar(255) NOT NULL,
  `creation_date` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `registrar` varchar(255) DEFAULT NULL,
  `http_status` int(11) DEFAULT NULL,
  `server_type` varchar(100) DEFAULT NULL,
  `load_time` decimal(10,3) DEFAULT NULL,
  `ssl_status` tinyint(1) DEFAULT 0,
  `status` enum('visible','hidden') DEFAULT 'visible',
  `outbound_link` tinyint(1) DEFAULT 1,
  `last_updated` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain_name` (`domain_name`),
  KEY `status` (`status`),
  KEY `last_updated` (`last_updated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Page views table
CREATE TABLE IF NOT EXISTS `page_views` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_id` int(11) DEFAULT NULL,
  `view_timestamp` timestamp DEFAULT CURRENT_TIMESTAMP,
  `is_bot` tinyint(1) DEFAULT 0,
  `user_agent` text,
  `ip_address` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `domain_id` (`domain_id`),
  KEY `view_timestamp` (`view_timestamp`),
  KEY `is_bot` (`is_bot`),
  FOREIGN KEY (`domain_id`) REFERENCES `domains`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Categories table
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Countries table
CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(2) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contact submissions table
CREATE TABLE IF NOT EXISTS `contact_submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submission_timestamp` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `submission_timestamp` (`submission_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Rate limiting table
CREATE TABLE IF NOT EXISTS `rate_limits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `request_count` int(11) DEFAULT 1,
  `first_request` timestamp DEFAULT CURRENT_TIMESTAMP,
  `last_request` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_address` (`ip_address`),
  KEY `last_request` (`last_request`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample categories
INSERT IGNORE INTO `categories` (`name`, `description`) VALUES
('Technology', 'Technology and software companies'),
('E-commerce', 'Online shopping and retail'),
('Finance', 'Banking and financial services'),
('Education', 'Educational institutions and resources'),
('Entertainment', 'Media and entertainment websites'),
('Health', 'Healthcare and medical services'),
('News', 'News and media outlets'),
('Social', 'Social media and networking'),
('Tools', 'Web tools and utilities'),
('Other', 'Miscellaneous websites');

-- Insert sample countries
INSERT IGNORE INTO `countries` (`name`, `code`) VALUES
('United States', 'US'),
('United Kingdom', 'UK'),
('Canada', 'CA'),
('Germany', 'DE'),
('France', 'FR'),
('Japan', 'JP'),
('Australia', 'AU'),
('Netherlands', 'NL'),
('Sweden', 'SE'),
('Switzerland', 'CH'); 