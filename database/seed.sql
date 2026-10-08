-- Jeu de données de démonstration.
-- Mot de passe de tous les comptes de démo : Password123!

INSERT INTO agences (nom) VALUES
    ('Paris'), ('Lyon'), ('Marseille'), ('Toulouse'), ('Nantes'),
    ('Strasbourg'), ('Bordeaux'), ('Lille'), ('Rennes'), ('Montpellier');

INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, role) VALUES
    ('Admin', 'Alice', 'admin@klaxon.test', '0600000001', '$2y$10$y3aTj55d3OH/gIEEMjPFlu2AqEedYq89gXw0DhSHaMIBoZNxLX/0O', 'admin'),
    ('Martin', 'Bob', 'bob.martin@klaxon.test', '0600000002', '$2y$10$y3aTj55d3OH/gIEEMjPFlu2AqEedYq89gXw0DhSHaMIBoZNxLX/0O', 'utilisateur'),
    ('Durand', 'Chloé', 'chloe.durand@klaxon.test', '0600000003', '$2y$10$y3aTj55d3OH/gIEEMjPFlu2AqEedYq89gXw0DhSHaMIBoZNxLX/0O', 'utilisateur'),
    ('Petit', 'David', 'david.petit@klaxon.test', '0600000004', '$2y$10$y3aTj55d3OH/gIEEMjPFlu2AqEedYq89gXw0DhSHaMIBoZNxLX/0O', 'utilisateur');

INSERT INTO trajets (agence_depart_id, agence_arrivee_id, date_depart, date_arrivee, places_total, places_disponibles, auteur_id) VALUES
    (1, 2, DATE_ADD(CURDATE(), INTERVAL '1 8:00' DAY_MINUTE),  DATE_ADD(CURDATE(), INTERVAL '1 12:00' DAY_MINUTE), 4, 3, 2),
    (2, 1, DATE_ADD(CURDATE(), INTERVAL '2 14:00' DAY_MINUTE), DATE_ADD(CURDATE(), INTERVAL '2 18:00' DAY_MINUTE), 3, 3, 2),
    (3, 4, DATE_ADD(CURDATE(), INTERVAL '3 7:30' DAY_MINUTE),  DATE_ADD(CURDATE(), INTERVAL '3 11:30' DAY_MINUTE), 4, 1, 3),
    (5, 9, DATE_ADD(CURDATE(), INTERVAL '4 9:00' DAY_MINUTE),  DATE_ADD(CURDATE(), INTERVAL '4 11:00' DAY_MINUTE), 3, 2, 3),
    (6, 1, DATE_ADD(CURDATE(), INTERVAL '5 6:00' DAY_MINUTE),  DATE_ADD(CURDATE(), INTERVAL '5 10:00' DAY_MINUTE), 4, 0, 4),
    (7, 10, DATE_ADD(CURDATE(), INTERVAL '6 13:00' DAY_MINUTE), DATE_ADD(CURDATE(), INTERVAL '6 17:00' DAY_MINUTE), 2, 2, 4);
