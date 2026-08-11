-- Databse: sutej

-- Create users table
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `contact` VARCHAR(20) NOT NULL,
  `address` TEXT NOT NULL,
  `is_admin` TINYINT(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_email` (`email`),
  UNIQUE KEY `unique_username` (`username`),
  UNIQUE KEY `unique_contact` (`contact`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample users
INSERT INTO `users` (`username`, `email`, `password`, `contact`, `address`, `is_admin`) VALUES
('admin', 'admin@example.com', '$2y$10$F1234567890examplehashadmin', '9999999999', 'Admin Address, City', 1),
('john_doe', 'john@example.com', '$2y$10$F1234567890examplehashuser', '9876543210', '123 Main Street, City', 0),
('jane_smith', 'jane@example.com', '$2y$10$F1234567890examplehashuser2', '9123456780', '456 Park Avenue, City', 0);

--The password values here are hashed using PHP’s password_hash() function. 
--These are example hashes — you should replace them with real hashed values generated like this in PHP:
echo password_hash("yourpassword", PASSWORD_DEFAULT);

-- Create pets table
CREATE TABLE `pets` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `owner_id` INT(11) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `breed` VARCHAR(100) NOT NULL,
  `gender` VARCHAR(10) DEFAULT NULL,
  `age` VARCHAR(50) NOT NULL,
  `description` TEXT NOT NULL,
  `contact` VARCHAR(20) NOT NULL,
  `address` TEXT NOT NULL,
  `status` ENUM('Available', 'Adoption Pending', 'Adopted') DEFAULT 'Available',
  `image` VARCHAR(255) DEFAULT NULL,
  `date_posted` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `owner_id` (`owner_id`),
  CONSTRAINT `fk_pets_owner` FOREIGN KEY (`owner_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample pets
INSERT INTO `pets` (`owner_id`, `name`, `type`, `breed`, `gender`, `age`, `description`, `contact`, `address`, `status`, `image`, `date_posted`) VALUES
(2, 'Bruno', 'Dog', 'Labrador', 'Male', '2 years', 'Friendly and playful, good with kids.', '9876543210', '123 Main Street, City', 'Available', 'bruno.jpg', NOW()),
(3, 'Whiskers', 'Cat', 'Siamese', 'Female', '1 year', 'Loves to cuddle and climb.', '9123456780', '456 Park Avenue, City', 'Available', 'whiskers.jpg', NOW());


-- Create adoptions table
CREATE TABLE `adoptions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `pet_id` INT(11) NOT NULL,
  `adopter_id` INT(11) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
  `contact` VARCHAR(255) DEFAULT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `date_approved` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pet_id` (`pet_id`),
  KEY `adopter_id` (`adopter_id`),
  CONSTRAINT `fk_adoptions_pet` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_adoptions_user` FOREIGN KEY (`adopter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample adoptions
INSERT INTO `adoptions` (`pet_id`, `adopter_id`, `message`, `status`, `contact`, `address`, `date_approved`) VALUES
(1, 3, 'I would love to give Bruno a loving home. I have a big backyard and lots of time.', 'Pending', '9123456780', '789 Lakeview Street, City', NULL),
(2, 2, 'Whiskers would be a great fit in our quiet family home.', 'Pending', '9876543210', '123 Main Street, City', NULL);


-- Create contact_messages table
CREATE TABLE `contact_messages` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) DEFAULT NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `contact` VARCHAR(15) NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Insert sample contact message
INSERT INTO `contact_messages` (`name`, `email`, `contact`, `message`) VALUES
('Suresh Kumar', 'suresh@example.com', '9876543210', 'Hi, I love your website! I’m interested in adopting a pet soon.');
