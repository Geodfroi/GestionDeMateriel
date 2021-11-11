USE HEdS_inv_exp;

INSERT INTO users (id, username, email, is_admin) VALUES (1, 'Joël', 'aurore.azure@gmail.com', 1);
INSERT INTO users (id,username, email) VALUES (2, 'Florence Nightingale', 'hospital@crimea.com');
INSERT INTO users (id,username, email) VALUES (3, 'Henry Dunant', 'h.dunant@redcross.ch');
INSERT INTO users (id,username, email) VALUES (4, 'Clara Barton', 'clara-b@civilwar.org');

INSERT INTO items (user_id, item_name, location, expiration_date) VALUES (2, 'Vin rouge', 'celier', '2022-08-01' );
INSERT INTO items (user_id, item_name, location) VALUES (2, 'Papier A4 x 1000', 'bureau 202');

INSERT INTO items (user_id, item_name, location, expiration_date) VALUES (3, 'Produit nettoyage A1', 'Placard 3A', '2023-08-01' );
INSERT INTO items (user_id, item_name, location, expiration_date) VALUES (3, 'Produit nettoyage A2', 'Placard 3A', '2023-06-01' );
INSERT INTO items (user_id, item_name, location, expiration_date) VALUES (3, 'Produit nettoyage A3', 'Placard 3A', '2023-06-01' );
INSERT INTO items (user_id, item_name, location, expiration_date) VALUES (3, 'Produit nettoyage A4', 'Placard 3A', '2023-06-01' );
INSERT INTO items (user_id, item_name, location, expiration_date) VALUES (3, 'Produit nettoyage A5', 'Placard 3A', '2023-06-01' );
INSERT INTO items (user_id, item_name, location, expiration_date) VALUES (3, 'Produit nettoyage A6', 'Placard 3A', '2023-06-01' );
INSERT INTO items (user_id, item_name, location, expiration_date) VALUES (3, 'Produit nettoyage A7', 'Placard 3A', '2023-06-01' );
INSERT INTO items (user_id, item_name, location, expiration_date) VALUES (3, 'Produit nettoyage A8', 'Placard 3A', '2023-06-01' );
INSERT INTO items (user_id, item_name, location, expiration_date) VALUES (3, 'Produit nettoyage A9', 'Placard 3A', '2023-06-01' );

INSERT INTO items (user_id, item_name, location, expiration_date) VALUES (3, 'Produit nettoyage A9', 'Placard 3A', '2023-06-01' );

INSERT INTO items (user_id, item_name, location, expiration_date) VALUES (3, 'Médicament 0XG6', 'Pharmacie 1er', '2022-08-13' );
INSERT INTO items (user_id, item_name, location, expiration_date) VALUES (3, 'Médicament 0XG6', 'Pharmacie 1er', '2022-07-13' );
INSERT INTO items (user_id, item_name, location, expiration_date) VALUES (3, 'Médicament 0XG6', 'Pharmacie 1er', '2022-08-13' );
INSERT INTO items (user_id, item_name, location, expiration_date) VALUES (3, 'Médicament F1I8', 'Pharmacie 1er', '2022-06-13' );
INSERT INTO items (user_id, item_name, location, expiration_date) VALUES (3, 'Médicament F1I8', 'Pharmacie 1er', '2022-08-13' );
INSERT INTO items (user_id, item_name, location, expiration_date) VALUES (3, 'Médicament ADD1', 'Pharmacie 1er', '2022-08-13' );
INSERT INTO items (user_id, item_name, location, expiration_date) VALUES (3, 'Médicament 9JTX', 'Pharmacie 1er', '2023-04-10' );
INSERT INTO items (user_id, item_name, location, expiration_date) VALUES (3, 'Médicament 0XG6', 'Pharmacie 1er', '2021-07-12' );

