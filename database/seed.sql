-- Moroccan Café Kiosk – MVP seed data
USE `kiosk`;

-- Admin user (password to be set manually later)
INSERT INTO `users` (`email`, `password_hash`, `is_active`)
VALUES ('admin@cafe.local', '$2y$10$CHANGE_ME_HASH', 1)
ON DUPLICATE KEY UPDATE `email` = VALUES(`email`);

-- Categories
INSERT INTO `categories` (`id`, `name`, `is_active`) VALUES
  (1, 'Boissons chaudes', 1),
  (2, 'Boissons froides', 1),
  (3, 'Pâtisseries', 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `is_active` = VALUES(`is_active`);

-- Products (minimal)
INSERT INTO `products` (`category_id`, `name`, `description`, `base_price`, `image_url`, `is_available`) VALUES
  (1, 'Espresso', NULL, 12.00, '/assets/img/espresso.jpg', 1),
  (1, 'Nos Nos', NULL, 14.00, '/assets/img/nosnos.jpg', 1),
  (1, 'Thé à la menthe', NULL, 10.00, '/assets/img/the_menthe.jpg', 1),
  (2, 'Jus d\'orange frais', NULL, 18.00, '/assets/img/jus_orange.jpg', 1),
  (2, 'Smoothie avocat', NULL, 22.00, '/assets/img/smoothie_avocat.jpg', 1),
  (2, 'Eau minérale', NULL, 6.00, '/assets/img/eau.jpg', 1),
  (3, 'Croissant', NULL, 8.00, '/assets/img/croissant.jpg', 1),
  (3, 'Pain au chocolat', NULL, 9.00, '/assets/img/pain_chocolat.jpg', 1)
;
