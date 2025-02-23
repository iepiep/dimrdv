CREATE TABLE IF NOT EXISTS `PREFIX_dim_rdv` (
    `id_dim_rdv` INT(11) NOT NULL AUTO_INCREMENT,
    `lastname` VARCHAR(255) NOT NULL,
    `firstname` VARCHAR(255) NOT NULL,
    `address` VARCHAR(255) NOT NULL,
    `postal_code` VARCHAR(10) NOT NULL,
    `city` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `date_creneau1` VARCHAR(50) NOT NULL,
    `date_creneau2` VARCHAR(50) NOT NULL,
    `visited` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_dim_rdv`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `PREFIX_dim_rdv` (`lastname`, `firstname`, `address`, `postal_code`, `city`, `phone`, `email`, `date_creneau1`, `date_creneau2`, `visited`)
VALUES
('Supermarket', 'Carrefour', '1 Rue de Bretagne', '53000', 'Laval', '0123456789', 'carrefour@example.com', '2025-02-24 10:00', '2025-02-24 11:00', 0),
('Pharmacy', 'Citypharma', '12 Avenue Robert Buron', '53000', 'Laval', '0123456789', 'citypharma@example.com', '2025-02-25 14:00', '2025-02-25 15:00', 0),
('Bakery', 'Boulangerie', '5 Rue Charles Landelle', '53000', 'Laval', '0123456789', 'boulangerie@example.com', '2025-02-26 09:00', '2025-02-26 10:00', 0),
('Library', 'Bibliothèque', '10 Rue de la Paix', '53000', 'Laval', '0123456789', 'bibliotheque@example.com', '2025-02-27 16:00', '2025-02-27 17:00', 0),
('Park', 'Jardin', '2 Place de Hercé', '53000', 'Laval', '0123456789', 'jardin@example.com', '2025-02-28 08:00', '2025-02-28 09:00', 0),
('Restaurant', 'Le Gourmet', '3 Rue de la République', '53100', 'Mayenne', '0123456789', 'gourmet@example.com', '2025-02-24 12:00', '2025-02-24 13:00', 0),
('Clinic', 'SantéPlus', '7 Avenue du Général de Gaulle', '53200', 'Chateau-Gontier', '0123456789', 'santeplus@example.com', '2025-02-25 15:00', '2025-02-25 16:00', 0),
('School', 'Collège Jean Moulin', '4 Rue de la Liberté', '53500', 'Ernée', '0123456789', 'collegejean@example.com', '2025-02-26 10:00', '2025-02-26 11:00', 0),
('Supermarket', 'Intermarché', '8 Rue de la Gare', '53400', 'Craon', '0123456789', 'intermarche@example.com', '2025-02-27 09:00', '2025-02-27 10:00', 0),
('Pharmacy', 'Pharmacie Centrale', '6 Rue des Fleurs', '53940', 'Saint-Berthevin', '0123456789', 'pharmacie@example.com', '2025-02-28 11:00', '2025-02-28 12:00', 0);
