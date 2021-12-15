USE HEdS_inv_exp;

INSERT INTO locations(id, str_content) VALUES(1, 'Pharmacie 1er étage');
INSERT INTO locations(id, str_content) VALUES(2, "Cabinet d'entretien");
INSERT INTO locations(id, str_content) VALUES(3, 'Derrière les coussins du canapé');

INSERT INTO users (id, password, alias, login_email, is_admin) VALUES (1, '$2y$10$Sytf53Pfav5E0A1yTDf39uJE669PUjp/0OXYDM8lhlfNjoqrq.0a.', 'noel.biquet@gmail.com', 'noel.biquet@gmail.com', 1);
INSERT INTO users (id, password, alias, login_email, creation_date) VALUES (2, '$2y$10$yw4QX5NjU8g21i5qxnQXbOGhNjDoZ8r0eBiSSJrNTbEFtmTs8VDXC', 'Florence', 'flo-night@crimea.com', '2020-09-06');
INSERT INTO users (id, password, alias, login_email, creation_date) VALUES (3, '$2y$10$rZGyarSuElY1WKcqRMrcVeRUoMWn0iJaitiygV66wb5N3DgHRnpva', 'h.dunant@redcross.ch', 'h.dunant@redcross.ch','2020-04-12');
INSERT INTO users (id, password, alias, login_email, creation_date) VALUES (4, '$2y$10$cKK0yQsz9Ywj3u/Jav4D2OUexjmW8GzWP27jgjSDFPnvlpWYaqrfG', 'clarab@civilwar.org', 'clarab@civilwar.org','2020-12-03');

INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (2, 'Vin rouge', 'celier', '2021-08-01' );
INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (2, 'Papier A4 x 1000', 'bureau 202', '2022-06-01' );

INSERT INTO articles (user_id, article_name, location, expiration_date, comments) VALUES (3, 'Produit nettoyage A1', 'Placard 3A', '2021-08-01', 'this is a comment');
INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (3, 'Produit nettoyage A2', 'Placard 3B', '2021-12-06' );
INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (3, 'Produit nettoyage A2b', 'Placard 3B', '2021-12-06' );
INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (3, 'Produit nettoyage A3', 'Placard 3B', '2021-12-07' );
INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (3, 'Produit nettoyage A3b', 'Placard 3A', '2021-12-07' );
INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (3, 'Produit nettoyage A4', 'Placard 4A', '2021-12-15' );
INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (3, 'Produit nettoyage A5', 'Placard 4A', '2021-12-16' );
INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (2, 'Produit nettoyage A5', 'Placard 4A', '2021-12-16' );
INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (3, 'Produit nettoyage A5', 'Placard 4E', '2021-12-17' );
INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (4, 'Produit nettoyage A5', 'Placard 33', '2021-12-17' );
INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (3, 'Produit nettoyage A6', 'Placard 33', '2021-12-18' );
INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (4, 'Produit nettoyage A7', 'Placard 33', '2021-12-18' );
INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (4, 'Produit nettoyage A8', 'Placard 3A', '2021-12-19' );
INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (4, 'Produit nettoyage A9', 'Placard 3A', '2021-12-24' );
INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (3, 'Produit nettoyage A9', 'Placard 3A', '2021-12-14' );

INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (4, 'Médicament 0XG6', 'Pharmacie 1er', '2021-12-15' );
INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (4, 'Médicament 0XG6', 'Pharmacie 2ème', '2021-12-16' );
INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (4, 'Médicament 0XG6', 'Pharmacie 2ème', '2021-12-17' );
INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (4, 'Médicament F1I8', 'Pharmacie 2ème', '2021-12-18' );
INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (3, 'Médicament F1I8', 'Pharmacie 2ème', '2021-12-19' );
INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (3, 'Médicament ADD1', 'Pharmacie 1er', '2021-08-20' );
INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (3, 'Médicament 9JTX', 'Pharmacie 1er', '2021-04-10' );
INSERT INTO articles (user_id, article_name, location, expiration_date) VALUES (3, 'Médicament 0XG6', 'Pharmacie 1er', '2021-07-12' );