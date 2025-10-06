-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `temple_website`;
USE `temple_website`;

-- Table structure for `admins`
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert a default admin user
-- Password is 'admin123'
INSERT INTO `admins` (`username`, `password`) VALUES
('admin', '$2y$10$IcaFCi.D2GDGk2n4h3F2GeA8z2Y.zQxOUuMVT8wYk2m9xVz.8b.mS');

-- Table structure for `slider`
CREATE TABLE `slider` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_url` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default slider data
INSERT INTO `slider` (`image_url`, `title`, `description`, `sort_order`) VALUES
('uploads/slider1.jpg', 'Welcome to Shree Temple', 'Experience divinity, peace, and culture at our temple.', 1),
('uploads/slider2.jpg', 'Daily Panchang', 'Stay updated with rituals, timings, and astrological details.', 2),
('uploads/slider3.jpg', 'Join the Community', 'Be part of cultural and spiritual growth together.', 3);


-- Table structure for `stories`
CREATE TABLE `stories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `thumbnail_url` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default stories
INSERT INTO `stories` (`thumbnail_url`, `sort_order`) VALUES
('uploads/story_thumb1.jpg', 1),
('uploads/story_thumb2.jpg', 2),
('uploads/story_thumb3.jpg', 3);


-- Table structure for `story_slides`
CREATE TABLE `story_slides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `story_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `story_id` (`story_id`),
  CONSTRAINT `story_slides_ibfk_1` FOREIGN KEY (`story_id`) REFERENCES `stories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default story slides
INSERT INTO `story_slides` (`story_id`, `image_url`, `title`, `description`, `sort_order`) VALUES
(1, 'uploads/story1.jpg', 'Temple Ceremony', 'A glimpse from our divine rituals.', 1),
(2, 'uploads/story2.jpg', 'Community Work', 'Helping hands in food donation drive.', 1),
(3, 'uploads/story3.jpg', 'Festive Celebrations', 'Join us in the joy of togetherness.', 1);
