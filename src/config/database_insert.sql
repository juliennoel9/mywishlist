INSERT INTO `account` (`username`, `email`, `hash`, `nom`, `prenom`) VALUES
('root', 'root@root.com', '$2y$10$zUnLFNxa0iP4svm5PMKvHu8u7Z8LWsedo0udjxQqfcJJa8h1CKRE2', 'root', 'root'),
('test', 'test@test.com', '$2y$10$oGt5yvyqrTzAPJN8/SCf.eK5YgKbrzJhvnfPzYuIFElDU61OnzYj2', 'test', 'test');

INSERT INTO `liste` (`user_id`, `titre`, `description`, `expiration`, `token`, `public`) VALUES
(1, 'Pour fêter le bac !', 'Pour un week-end à Nancy qui nous fera oublier les épreuves. ', '2020-06-27', 'HOf3UXyX', TRUE),
(1, 'Liste de mariage d\'Alice et Bob', 'Nous souhaitons passer un week-end royal à Nancy pour notre lune de miel :)', '2018-06-30', '54xs1en0', TRUE),
(1, 'C\'est l\'anniversaire de Charlie', 'Pour lui préparer une fête dont il se souviendra :)', '2017-12-12', 'seab3twa', FALSE);

INSERT INTO `item` (`liste_id`, `nom`, `descr`, `img`, `url`, `tarif`) VALUES
(1, 'Appart Hotel', 'Appart’hôtel Coeur de Ville, en plein centre-ville', 'apparthotel.jpg', '', 56.00),
(1, 'Boite de nuit', 'Discothèque, Boîte tendance avec des soirées à thème & DJ invités', 'boitedenuit.jpg', '', 32.00),
(1, 'Planètes Laser', 'Laser game : Gilet électronique et pistolet laser comme matériel, vous voilà équipé.', 'laser.jpg', '', 15.00),
(1, 'Fort Aventure', 'Découvrez Fort Aventure à Bainville-sur-Madon, un site Accropierre unique en Lorraine ! Des Parcours Acrobatiques pour petits et grands, Jeu Mission Aventure, Crypte de Crapahute, Tyrolienne, Saut à l\'élastique inversé, Toboggan géant... et bien plus encore.', 'fort.jpg', '', 25.00),
(2, 'Champagne', 'Bouteille de champagne + flutes + jeux à gratter', 'champagne.jpg', '', 20.00),
(2, 'Musique', 'Partitions de piano à 4 mains', 'musique.jpg', '', 25.00),
(2, 'Exposition', 'Visite guidée de l’exposition ‘REGARDER’ à la galerie Poirel', 'poirelregarder.jpg', '', 14.00),
(2, 'Bouquet', 'Bouquet de roses et Mots de Marion Renaud', 'rose.jpg', '', 16.00),
(2, 'Diner Stanislas', 'Diner à La Table du Bon Roi Stanislas (Apéritif /Entrée / Plat / Vin / Dessert / Café / Digestif)', 'bonroi.jpg', '', 60.00),
(2, 'Diner  Grand Rue ', 'Diner au Grand’Ru(e) (Apéritif / Entrée / Plat / Vin / Dessert / Café)', 'grandrue.jpg', '', 59.00),
(2, 'Visite guidée', 'Visite guidée personnalisée de Saint-Epvre jusqu’à Stanislas', 'place.jpg', '', 11.00),
(2, 'Bijoux', 'Bijoux de manteau + Sous-verre pochette de disque + Lait après-soleil', 'bijoux.jpg', '', 29.00),
(2, 'Hôtel d\'Haussonville', 'Hôtel d\'Haussonville, au coeur de la Vieille ville à deux pas de la place Stanislas', 'hotel_haussonville_logo.jpg', '', 169.00),
(3, 'Goûter', 'Goûter au FIFNL', 'gouter.jpg', '', 20.00),
(3, 'Projection', 'Projection courts-métrages au FIFNL', 'film.jpg', '', 10.00),
(3, 'Origami', 'Baguettes magiques en Origami en buvant un thé', 'origami.jpg', '', 12.00),
(3, 'Livres', 'Livre bricolage avec petits-enfants + Roman', 'bricolage.jpg', '', 24.00),
(3, 'Jeu contacts', 'Jeu pour échange de contacts', 'contact.png', '', 5.00),
(3, 'Concert', 'Un concert à Nancy', 'concert.jpg', '', 17.00);
UPDATE `item` SET `cagnotte` = TRUE WHERE `id` <= 3;

INSERT INTO `cagnotte` (`item_id`, `account_id`, `montant`) VALUES
(2, 2, 10.50),
(3, 2, 5.0);