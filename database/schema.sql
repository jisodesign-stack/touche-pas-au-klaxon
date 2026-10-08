-- Schéma de la base de données "Touche pas au klaxon"

CREATE TABLE IF NOT EXISTS agences (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_agences_nom (nom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('utilisateur', 'admin') NOT NULL DEFAULT 'utilisateur',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_utilisateurs_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS trajets (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    agence_depart_id INT UNSIGNED NOT NULL,
    agence_arrivee_id INT UNSIGNED NOT NULL,
    date_depart DATETIME NOT NULL,
    date_arrivee DATETIME NOT NULL,
    places_total TINYINT UNSIGNED NOT NULL,
    places_disponibles TINYINT UNSIGNED NOT NULL,
    auteur_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_trajets_date_depart (date_depart),
    CONSTRAINT fk_trajets_depart FOREIGN KEY (agence_depart_id) REFERENCES agences (id),
    CONSTRAINT fk_trajets_arrivee FOREIGN KEY (agence_arrivee_id) REFERENCES agences (id),
    CONSTRAINT fk_trajets_auteur FOREIGN KEY (auteur_id) REFERENCES utilisateurs (id) ON DELETE CASCADE,
    CONSTRAINT chk_trajets_agences CHECK (agence_depart_id <> agence_arrivee_id),
    CONSTRAINT chk_trajets_dates CHECK (date_arrivee > date_depart),
    CONSTRAINT chk_trajets_places CHECK (places_disponibles <= places_total)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
