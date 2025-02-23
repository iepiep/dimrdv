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