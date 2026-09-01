-- 1. FIRST: Insert categories with explicit IDs to ensure they match product references
INSERT INTO categories (category_id, name, description) VALUES
(1, 'Floral', 'Parfums aux notes de fleurs comme la rose, le jasmin, la lavande, etc.'),
(2, 'Boisé', 'Parfums aux notes de bois comme le cèdre, le santal, le vétiver, etc.'),
(3, 'Oriental', 'Parfums épicés et chauds avec des notes comme la vanille, l''ambre, les épices, etc.'),
(4, 'Frais', 'Parfums légers et rafraîchissants avec des notes d''agrumes, aquatiques ou aromatiques'),
(5, 'Fruité', 'Parfums aux notes de fruits comme la pêche, la pomme, les baies, etc.'),
(6, 'Gourmand', 'Parfums aux notes sucrées évoquant la nourriture comme la vanille, le caramel, le chocolat');

-- 2. SECOND: Insert brands with explicit IDs
INSERT INTO brands (brand_id, name, description, image) VALUES
(1, 'Dior', 'Maison de parfumerie de luxe française fondée en 1946', 'images/brands/dior.jpg'),
(2, 'Chanel', 'Maison de haute couture française fondée par Coco Chanel', 'images/brands/chanel.jpg'),
(3, 'Guerlain', 'L''une des plus anciennes maisons de parfum au monde, fondée en 1828', 'images/brands/guerlain.jpg'),
(4, 'Tom Ford', 'Marque de luxe américaine fondée par le designer Tom Ford', 'images/brands/tomford.jpg'),
(5, 'Yves Saint Laurent', 'Maison de haute couture française fondée par Yves Saint Laurent', 'images/brands/ysl.jpg'),
(6, 'Jo Malone', 'Marque britannique spécialisée dans les parfums de luxe et les bougies parfumées', 'images/brands/jomalone.jpg'),
(7, 'Hermès', 'Maison de luxe française spécialisée dans les parfums, la maroquinerie et les accessoires', 'images/brands/hermes.jpg'),
(8, 'Byredo', 'Marque suédoise de parfums de niche fondée en 2006', 'images/brands/byredo.jpg');

-- 3. THIRD: Insert products (make sure to run this AFTER categories and brands are inserted)
INSERT INTO products (name, description, price, stock_quantity, image, category_id, brand_id, volume, concentration, gender) VALUES
-- Dior
('J''adore', 'Un bouquet floral lumineux et équilibré, composé d''essence d''ylang-ylang, d''essence de rose de Damas et de jasmin Sambac.', 125.00, 50, 'images/products/dior_jadore.jpg', 1, 1, 50, 'Eau de Parfum', 'Femme'),
('Sauvage', 'Un parfum masculin aux notes fraîches et boisées, avec de la bergamote de Calabre et de l''ambroxan.', 105.00, 60, 'images/products/dior_sauvage.jpg', 2, 1, 100, 'Eau de Toilette', 'Homme'),
('Miss Dior', 'Un bouquet floral chypré aux notes de rose de Grasse et de bois de rose.', 115.00, 45, 'images/products/dior_missdior.jpg', 1, 1, 50, 'Eau de Parfum', 'Femme'),

-- Chanel
('N°5', 'Parfum emblématique floral-aldéhydé avec des notes de jasmin, rose, ylang-ylang et vanille.', 135.00, 40, 'images/products/chanel_no5.jpg', 1, 2, 50, 'Eau de Parfum', 'Femme'),
('Bleu de Chanel', 'Parfum aromatique-boisé avec des notes d''agrumes, de menthe, de jasmin et de vétiver.', 115.00, 55, 'images/products/chanel_bleu.jpg', 2, 2, 100, 'Eau de Toilette', 'Homme'),
('Coco Mademoiselle', 'Parfum oriental-frais avec des notes d''orange, de jasmin et de patchouli.', 125.00, 48, 'images/products/chanel_cocomademoiselle.jpg', 3, 2, 50, 'Eau de Parfum', 'Femme'),

-- Guerlain
('Shalimar', 'Parfum oriental-vanillé emblématique avec des notes de bergamote, d''iris et de vanille.', 110.00, 30, 'images/products/guerlain_shalimar.jpg', 3, 3, 50, 'Eau de Parfum', 'Femme'),
('L''Homme Idéal', 'Parfum boisé-aromatique avec des notes d''amande, de vanille et de cuir.', 95.00, 40, 'images/products/guerlain_hommeideal.jpg', 2, 3, 100, 'Eau de Toilette', 'Homme'),
('Mon Guerlain', 'Parfum oriental-floral avec des notes de lavande, de vanille et de santal.', 105.00, 35, 'images/products/guerlain_monguerlain.jpg', 1, 3, 50, 'Eau de Parfum', 'Femme'),

-- Tom Ford
('Black Orchid', 'Parfum oriental-floral luxueux avec des notes d''orchidée noire, de truffe et de patchouli.', 165.00, 25, 'images/products/tomford_blackorchid.jpg', 3, 4, 50, 'Eau de Parfum', 'Unisexe'),
('Oud Wood', 'Parfum boisé-épicé rare et exotique avec des notes de bois de oud, de bois de santal et de vétiver.', 185.00, 20, 'images/products/tomford_oudwood.jpg', 2, 4, 50, 'Eau de Parfum', 'Unisexe'),
('Neroli Portofino', 'Parfum citronné-aromatique frais avec des notes de néroli, de bergamote et de romarin.', 155.00, 30, 'images/products/tomford_neroliportofino.jpg', 4, 4, 50, 'Eau de Parfum', 'Unisexe'),

-- YSL
('Black Opium', 'Parfum oriental-gourmand avec des notes de café, de vanille et de fleur d''oranger.', 120.00, 50, 'images/products/ysl_blackopium.jpg', 3, 5, 50, 'Eau de Parfum', 'Femme'),
('La Nuit de l''Homme', 'Parfum oriental-épicé avec des notes de cardamome, de lavande et de vétiver.', 95.00, 45, 'images/products/ysl_lanuitdelhomme.jpg', 3, 5, 100, 'Eau de Toilette', 'Homme'),
('Libre', 'Parfum floral-oriental avec des notes de lavande, de fleur d''oranger et de vanille.', 110.00, 40, 'images/products/ysl_libre.jpg', 1, 5, 50, 'Eau de Parfum', 'Femme'),

-- Jo Malone
('English Pear & Freesia', 'Parfum fruité-floral avec des notes de poire, de freesia et d''ambre.', 140.00, 25, 'images/products/jomalone_pearfreesia.jpg', 5, 6, 100, 'Eau de Cologne', 'Unisexe'),
('Wood Sage & Sea Salt', 'Parfum boisé-aromatique avec des notes de sauge, de sel marin et de bois flotté.', 140.00, 30, 'images/products/jomalone_woodsage.jpg', 4, 6, 100, 'Eau de Cologne', 'Unisexe'),
('Peony & Blush Suede', 'Parfum floral-fruité avec des notes de pivoine, de pomme rouge et de daim.', 140.00, 25, 'images/products/jomalone_peony.jpg', 1, 6, 100, 'Eau de Cologne', 'Unisexe'),

-- Hermès
('Terre d''Hermès', 'Parfum boisé-épicé avec des notes d''orange, de poivre et de vétiver.', 130.00, 35, 'images/products/hermes_terre.jpg', 2, 7, 100, 'Eau de Toilette', 'Homme'),
('Un Jardin sur le Nil', 'Parfum vert-fruité avec des notes de mangue verte, de lotus et de sycamore.', 120.00, 30, 'images/products/hermes_jardinnil.jpg', 4, 7, 100, 'Eau de Toilette', 'Unisexe'),
('Twilly d''Hermès', 'Parfum floral-épicé avec des notes de gingembre, de tubéreuse et de santal.', 110.00, 25, 'images/products/hermes_twilly.jpg', 1, 7, 50, 'Eau de Parfum', 'Femme'),

-- Byredo
('Gypsy Water', 'Parfum boisé-aromatique avec des notes de bergamote, de genévrier, de santal et de vanille.', 190.00, 20, 'images/products/byredo_gypsywater.jpg', 2, 8, 50, 'Eau de Parfum', 'Unisexe'),
('Bal d''Afrique', 'Parfum boisé-floral avec des notes de néroli, de jasmin, de vétiver et de cèdre.', 190.00, 18, 'images/products/byredo_baldafrique.jpg', 1, 8, 50, 'Eau de Parfum', 'Unisexe'),
('Mojave Ghost', 'Parfum boisé-ambré avec des notes d''ambrette, de nectarine, de magnolia et de cèdre.', 190.00, 15, 'images/products/byredo_mojaveghost.jpg', 2, 8, 50, 'Eau de Parfum', 'Unisexe');
-- D'abord, vérifier et insérer les marques si elles n'existent pas déjà

-- Bvlgari
INSERT INTO brands (name, description)
SELECT 'Bvlgari', 'Maison de luxe italienne fondée en 1884, connue pour ses parfums élégants et sophistiqués'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Bvlgari');

-- Davidoff
INSERT INTO brands (name, description)
SELECT 'Davidoff', 'Marque suisse fondée par Zino Davidoff, créateur de parfums raffinés et intemporels'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Davidoff');

-- Paco Rabanne
INSERT INTO brands (name, description)
SELECT 'Paco Rabanne', 'Marque de mode et de parfums avant-gardiste fondée par le couturier espagnol Paco Rabanne'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Paco Rabanne');

-- Kenzo
INSERT INTO brands (name, description)
SELECT 'Kenzo', 'Maison de mode française fondée par Kenzo Takada, connue pour ses parfums originaux et poétiques'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Kenzo');

-- Dolce & Gabbana
INSERT INTO brands (name, description)
SELECT 'Dolce & Gabbana', 'Maison de haute couture italienne fondée par Domenico Dolce et Stefano Gabbana, célèbre pour ses parfums méditerranéens'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Dolce & Gabbana');

-- Ralph Lauren
INSERT INTO brands (name, description)
SELECT 'Ralph Lauren', 'Marque américaine emblématique fondée par Ralph Lauren, proposant des parfums élégants et sophistiqués'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Ralph Lauren');

-- Versace
INSERT INTO brands (name, description)
SELECT 'Versace', 'Maison de mode italienne fondée par Gianni Versace, créatrice de parfums audacieux et luxueux'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Versace');

-- Maintenant, récupérer les IDs après s'être assuré que les marques existent
SET @bvlgari_id = (SELECT brand_id FROM brands WHERE name = 'Bvlgari');
SET @davidoff_id = (SELECT brand_id FROM brands WHERE name = 'Davidoff');
SET @paco_rabanne_id = (SELECT brand_id FROM brands WHERE name = 'Paco Rabanne');
SET @kenzo_id = (SELECT brand_id FROM brands WHERE name = 'Kenzo');
SET @dolce_gabbana_id = (SELECT brand_id FROM brands WHERE name = 'Dolce & Gabbana');
SET @ralph_lauren_id = (SELECT brand_id FROM brands WHERE name = 'Ralph Lauren');
SET @versace_id = (SELECT brand_id FROM brands WHERE name = 'Versace');

-- Récupérer l'ID de la catégorie "Aquatique"
SET @aquatic_category_id = (SELECT category_id FROM categories WHERE name = 'Aquatique');

-- Maintenant insérer les produits
INSERT INTO products (name, description, price, stock_quantity, image, category_id, brand_id, volume, concentration, gender) VALUES
('Bvlgari Aqva Pour Homme', 'Un parfum frais et marin inspiré par la force de l\'océan', 89.99, 50, '/images/products/men/aquatic/bvlgari_aqva.jpg', @aquatic_category_id, @bvlgari_id, 100, 'Eau de Toilette', 'Homme'),
('Davidoff Cool Water', 'Un parfum rafraîchissant évoquant l\'océan et ses vagues', 65.50, 75, '/images/products/men/aquatic/davidoff_cool_water.jpg', @aquatic_category_id, @davidoff_id, 125, 'Eau de Toilette', 'Homme'),
('Invictus Aqua', 'Une fragrance énergique combinant fraîcheur marine et bois ambré', 95.00, 30, '/images/products/men/aquatic/invictus_aqua.jpg', @aquatic_category_id, @paco_rabanne_id, 100, 'Eau de Toilette', 'Homme'),
('Kenzo Pour Homme', 'Un parfum aquatique avec des notes de bambou et de menthe', 78.75, 40, '/images/products/men/aquatic/kenzo_pour_homme.jpg', @aquatic_category_id, @kenzo_id, 100, 'Eau de Toilette', 'Homme'),
('Light Blue Pour Homme', 'Une fragrance méditerranéenne avec des notes d\'agrumes et de bois', 87.25, 35, '/images/products/men/aquatic/light_blue_pour_homme.jpg', @aquatic_category_id, @dolce_gabbana_id, 75, 'Eau de Toilette', 'Homme'),
('Polo Blue', 'Un parfum frais et sportif avec des notes de concombre et de suède', 82.50, 45, '/images/products/men/aquatic/polo_blue.jpg', @aquatic_category_id, @ralph_lauren_id, 125, 'Eau de Toilette', 'Homme'),
('Versace Dylan Blue', 'Une fragrance moderne avec des notes aquatiques et boisées', 90.00, 25, '/images/products/men/aquatic/versace_dylan_blue.jpg', @aquatic_category_id, @versace_id, 100, 'Eau de Parfum', 'Homme');
 -----aromatic
 -- Récupérer l'ID de la catégorie "Aromatique"
SET @aromatic_category_id = (SELECT category_id FROM categories WHERE name = 'Aromatique');

-- Vérifier et insérer les marques supplémentaires si nécessaire
INSERT INTO brands (name, description)
SELECT 'Acqua di Parma', 'Maison de parfumerie italienne de luxe fondée en 1916, connue pour ses fragrances élégantes et raffinées'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Acqua di Parma');

INSERT INTO brands (name, description)
SELECT 'Dior', 'Maison de haute couture française fondée par Christian Dior, créatrice de parfums emblématiques et luxueux'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Dior');

INSERT INTO brands (name, description)
SELECT 'Yves Saint Laurent', 'Maison de mode française fondée par Yves Saint Laurent, célèbre pour ses parfums sophistiqués et avant-gardistes'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Yves Saint Laurent');

INSERT INTO brands (name, description)
SELECT 'Montblanc', 'Marque allemande de luxe connue pour ses parfums élégants et raffinés'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Montblanc');

-- Récupérer les IDs des marques
SET @acqua_di_parma_id = (SELECT brand_id FROM brands WHERE name = 'Acqua di Parma');
SET @dior_id = (SELECT brand_id FROM brands WHERE name = 'Dior');
SET @ysl_id = (SELECT brand_id FROM brands WHERE name = 'Yves Saint Laurent');
SET @montblanc_id = (SELECT brand_id FROM brands WHERE name = 'Montblanc');
SET @paco_rabanne_id = (SELECT brand_id FROM brands WHERE name = 'Paco Rabanne');

-- Insérer les produits aromatiques pour hommes
INSERT INTO products (name, description, price, stock_quantity, image, category_id, brand_id, volume, concentration, gender) VALUES
('Acqua di Parma Colonia Club', 'Une fragrance raffinée et élégante avec des notes aromatiques de menthe, néroli et petit grain', 110.00, 30, '/images/products/men/aromatic/Acqua di Parma Colonia Club – Acqua di Parma.jpg', @aromatic_category_id, @acqua_di_parma_id, 100, 'Eau de Cologne', 'Homme'),
('Dior Sauvage', 'Un parfum intense et frais avec des notes de bergamote, poivre et ambroxan', 99.50, 60, '/images/products/men/aromatic/Dior Sauvage – Dior.jpg', @aromatic_category_id, @dior_id, 100, 'Eau de Toilette', 'Homme'),
('L\'Homme', 'Une fragrance moderne avec des notes d\'épices blanches, gingembre et basilic', 85.00, 45, '/images/products/men/aromatic/L\'Homme – Yves Saint Laurent.jpg', @aromatic_category_id, @ysl_id, 100, 'Eau de Toilette', 'Homme'),
('Montblanc Explorer', 'Un parfum aromatique d\'aventure avec des notes de bergamote, sauge et vétiver', 75.00, 40, '/images/products/men/aromatic/Montblanc Explorer – Montblanc.jpg', @aromatic_category_id, @montblanc_id, 100, 'Eau de Parfum', 'Homme'),
('Paco Rabanne Invictus Legend', 'Une fragrance audacieuse et puissante avec des notes de pamplemousse, laurier et bois d\'ambre', 92.00, 35, '/images/products/men/aromatic/Paco Rabanne Invictus Legend – Paco Rabanne.jpg', @aromatic_category_id, @paco_rabanne_id, 100, 'Eau de Parfum', 'Homme');
-- Récupérer l'ID de la catégorie "Cyprès" (ou "Chyprés" selon votre nomenclature)
SET @chypre_category_id = (SELECT category_id FROM categories WHERE name = 'Cyprès');

-- Vérifier et insérer les marques supplémentaires si nécessaire
INSERT INTO brands (name, description)
SELECT 'Chanel', 'Maison de haute couture française fondée par Coco Chanel, connue pour ses parfums iconiques et intemporels'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Chanel');

INSERT INTO brands (name, description)
SELECT 'Armaf', 'Marque de parfumerie moderne offrant des fragrances de qualité à prix accessibles'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Armaf');

INSERT INTO brands (name, description)
SELECT 'Givenchy', 'Maison de haute couture française fondée par Hubert de Givenchy, créatrice de parfums élégants et raffinés'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Givenchy');

INSERT INTO brands (name, description)
SELECT 'Guerlain', 'Maison de parfumerie française fondée en 1828, l\'une des plus anciennes et prestigieuses au monde'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Guerlain');

INSERT INTO brands (name, description)
SELECT 'Roja Parfums', 'Maison de parfumerie de luxe britannique fondée par Roja Dove, créatrice de parfums opulents et sophistiqués'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Roja Parfums');

-- Récupérer les IDs des marques
SET @chanel_id = (SELECT brand_id FROM brands WHERE name = 'Chanel');
SET @armaf_id = (SELECT brand_id FROM brands WHERE name = 'Armaf');
SET @dolce_gabbana_id = (SELECT brand_id FROM brands WHERE name = 'Dolce & Gabbana');
SET @givenchy_id = (SELECT brand_id FROM brands WHERE name = 'Givenchy');
SET @guerlain_id = (SELECT brand_id FROM brands WHERE name = 'Guerlain');
SET @montblanc_id = (SELECT brand_id FROM brands WHERE name = 'Montblanc');
SET @roja_id = (SELECT brand_id FROM brands WHERE name = 'Roja Parfums');

-- Insérer les produits chyprés pour hommes
INSERT INTO products (name, description, price, stock_quantity, image, category_id, brand_id, volume, concentration, gender) VALUES
('Chanel Platinum Égoïste', 'Un parfum élégant et raffiné avec des notes de lavande, romarin, bois de santal et mousse de chêne', 115.00, 25, '/images/products/men/chypre/Chanel Platinum Egoïste – Chanel.jpg', @chypre_category_id, @chanel_id, 100, 'Eau de Toilette', 'Homme'),
('Club de Nuit Intense Man', 'Un parfum masculin intense avec des notes fruitées, épicées et boisées', 60.00, 45, '/images/products/men/chypre/Club de Nuit Intense Man – Armaf.jpg', @chypre_category_id, @armaf_id, 105, 'Eau de Toilette', 'Homme'),
('Dolce & Gabbana Pour Homme', 'Une fragrance méditerranéenne avec des notes d\'agrumes, de lavande et de tabac', 75.00, 30, '/images/products/men/chypre/Dolce & Gabbana Pour Homme – D&G.jpg', @chypre_category_id, @dolce_gabbana_id, 125, 'Eau de Toilette', 'Homme'),
('Givenchy Gentleman Eau de Parfum', 'Un parfum chypré boisé avec des notes d\'iris, de poivre noir et de patchouli', 95.00, 35, '/images/products/men/chypre/Givenchy Gentleman Eau de Parfum – Givenchy.jpg', @chypre_category_id, @givenchy_id, 100, 'Eau de Parfum', 'Homme'),
('Guerlain Habit Rouge', 'Un classique élégant avec des notes d\'agrumes, de cannelle, de vanille et de cuir', 120.00, 20, '/images/products/men/chypre/Guerlain Habit Rouge – Guerlain.jpg', @chypre_category_id, @guerlain_id, 100, 'Eau de Parfum', 'Homme'),
('Montblanc Explorer', 'Un parfum aventurier avec des notes de bergamote, de vétiver et de patchouli', 78.00, 40, '/images/products/men/chypre/Montblanc Explorer – Montblanc.jpg', @chypre_category_id, @montblanc_id, 100, 'Eau de Parfum', 'Homme'),
('Pour Monsieur', 'Un parfum classique et raffiné avec des notes d\'agrumes, de vétiver et de mousse de chêne', 110.00, 15, '/images/products/men/chypre/Pour Monsieur – Chanel.jpg', @chypre_category_id, @chanel_id, 75, 'Eau de Toilette', 'Homme'),
('Roja Dove Scandal Pour Homme', 'Un parfum sophistiqué et luxueux avec des notes de bergamote, de lavande et de cuir', 295.00, 10, '/images/products/men/chypre/Roja Dove Scandal Pour Homme – Roja Parfums.jpg', @chypre_category_id, @roja_id, 50, 'Parfum', 'Homme');
-- Récupérer l'ID de la catégorie "Agrumes" (correspondant à "Citrus")
SET @citrus_category_id = (SELECT category_id FROM categories WHERE name = 'Agrumes');

-- Vérifier et insérer les marques supplémentaires si nécessaire
INSERT INTO brands (name, description)
SELECT 'Giorgio Armani', 'Maison de haute couture italienne fondée par Giorgio Armani, créatrice de parfums élégants et sophistiqués'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Giorgio Armani');

INSERT INTO brands (name, description)
SELECT 'Mancera', 'Maison de parfumerie française de luxe spécialisée dans les fragrances opulentes et exotiques'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Mancera');

INSERT INTO brands (name, description)
SELECT 'Calvin Klein', 'Marque américaine fondée par Calvin Klein, connue pour ses parfums minimalistes et contemporains'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Calvin Klein');

INSERT INTO brands (name, description)
SELECT 'Issey Miyake', 'Maison japonaise fondée par le designer Issey Miyake, créatrice de parfums modernes et épurés'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Issey Miyake');

INSERT INTO brands (name, description)
SELECT 'Nautica', 'Marque américaine inspirée par la navigation et l\'océan, créatrice de parfums frais et sportifs'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Nautica');

-- Récupérer les IDs des marques
SET @armani_id = (SELECT brand_id FROM brands WHERE name = 'Giorgio Armani');
SET @chanel_id = (SELECT brand_id FROM brands WHERE name = 'Chanel');
SET @azro_id = (SELECT brand_id FROM brands WHERE LOWER(name) LIKE '%azro%' LIMIT 1); -- Nous n'avons pas d'information sur cette marque, recherche générique
SET @mancera_id = (SELECT brand_id FROM brands WHERE name = 'Mancera');
SET @calvin_klein_id = (SELECT brand_id FROM brands WHERE name = 'Calvin Klein');
SET @acqua_di_parma_id = (SELECT brand_id FROM brands WHERE name = 'Acqua di Parma');
SET @dior_id = (SELECT brand_id FROM brands WHERE name = 'Dior');
SET @issey_miyake_id = (SELECT brand_id FROM brands WHERE name = 'Issey Miyake');
SET @nautica_id = (SELECT brand_id FROM brands WHERE name = 'Nautica');
SET @versace_id = (SELECT brand_id FROM brands WHERE name = 'Versace');

-- Insérer une marque "Azro" si elle n'existe pas dans la base de données
INSERT INTO brands (name, description)
SELECT 'Azro', 'Marque de parfumerie proposant des fragrances masculines modernes et accessibles'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Azro');

-- Récupérer l'ID de la marque Azro
SET @azro_id = (SELECT brand_id FROM brands WHERE name = 'Azro');

-- Insérer les produits d'agrumes pour hommes
INSERT INTO products (name, description, price, stock_quantity, image, category_id, brand_id, volume, concentration, gender) VALUES
('Acqua di Gio', 'Un parfum frais et marin avec des notes d\'agrumes, de jasmin et de bois de cèdre', 90.00, 60, '/images/products/men/citrus/Acqua di Gio – Giorgio Armani.jpg', @citrus_category_id, @armani_id, 100, 'Eau de Toilette', 'Homme'),
('Allure Homme Sport', 'Une fragrance énergique et fraîche avec des notes d\'agrumes, de poivre blanc et de bois de cèdre', 105.00, 45, '/images/products/men/citrus/Allure Homme Sport – Chanel.jpg', @citrus_category_id, @chanel_id, 100, 'Eau de Toilette', 'Homme'),
('Azro', 'Un parfum masculin dynamique avec des notes d\'agrumes, de menthe et de bois ambré', 55.00, 40, '/images/products/men/citrus/azro.jpg', @citrus_category_id, @azro_id, 100, 'Eau de Parfum', 'Homme'),
('Cedrat Boise', 'Une fragrance boisée et agrumée avec des notes de cédrat, de bergamote et de bois de santal', 180.00, 25, '/images/products/men/citrus/Cedrat Boise – Mancera.jpg', @citrus_category_id, @mancera_id, 120, 'Eau de Parfum', 'Homme'),
('CK One', 'Un parfum unisexe iconique avec des notes d\'agrumes, de thé vert et de musc', 65.00, 70, '/images/products/men/citrus/CK One – Calvin Klein.jpg', @citrus_category_id, @calvin_klein_id, 200, 'Eau de Toilette', 'Homme'),
('Colonia', 'Un parfum classique et élégant avec des notes d\'agrumes, de lavande et de bois précieux', 120.00, 30, '/images/products/men/citrus/Colonia – Acqua di Parma.jpg', @citrus_category_id, @acqua_di_parma_id, 100, 'Eau de Cologne', 'Homme'),
('Eau Sauvage', 'Un classique intemporel avec des notes de citron, de romarin et de vétiver', 110.00, 35, '/images/products/men/citrus/Eau Sauvage – Dior.jpg', @citrus_category_id, @dior_id, 100, 'Eau de Toilette', 'Homme'),
('L\'Eau d\'Issey Pour Homme', 'Un parfum frais et aquatique avec des notes d\'agrumes, de yuzu et de bois précieux', 85.00, 50, '/images/products/men/citrus/L\'Eau d\'Issey Pour Homme – Issey Miyake.jpg', @citrus_category_id, @issey_miyake_id, 125, 'Eau de Toilette', 'Homme'),
('Nautica Voyage', 'Une fragrance marine et fraîche avec des notes d\'agrumes, de pomme verte et de cèdre', 40.00, 65, '/images/products/men/citrus/Nautica Voyage – Nautica.jpg', @citrus_category_id, @nautica_id, 100, 'Eau de Toilette', 'Homme'),
('Versace Man Eau Fraîche', 'Un parfum méditerranéen avec des notes de citron, de bergamote et de bois de santal', 75.00, 55, '/images/products/men/citrus/Versace Man Eau Fraîche – Versace.jpg', @citrus_category_id, @versace_id, 100, 'Eau de Toilette', 'Homme');
-- Récupérer l'ID de la catégorie "Fougère"
SET @fougere_category_id = (SELECT category_id FROM categories WHERE name = 'Fougère');

-- Vérifier et insérer les marques supplémentaires si nécessaire
INSERT INTO brands (name, description)
SELECT 'Azzaro', 'Maison de couture italienne fondée par Loris Azzaro, connue pour ses parfums masculins emblématiques'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Azzaro');

INSERT INTO brands (name, description)
SELECT 'Fabergé', 'Maison de joaillerie russe historique proposant des parfums de luxe'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Fabergé');

INSERT INTO brands (name, description)
SELECT 'Guy Laroche', 'Maison de haute couture française fondée par Guy Laroche, créatrice de parfums élégants'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Guy Laroche');

INSERT INTO brands (name, description)
SELECT 'Houbigant', 'Une des plus anciennes maisons de parfumerie françaises, fondée en 1775, créatrice de la première fougère moderne'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Houbigant');

INSERT INTO brands (name, description)
SELECT 'Jean Paul Gaultier', 'Maison de haute couture française fondée par Jean Paul Gaultier, célèbre pour ses parfums iconiques et audacieux'
WHERE NOT EXISTS (SELECT 1 FROM brands WHERE name = 'Jean Paul Gaultier');

-- Récupérer les IDs des marques
SET @azzaro_id = (SELECT brand_id FROM brands WHERE name = 'Azzaro');
SET @faberge_id = (SELECT brand_id FROM brands WHERE name = 'Fabergé');
SET @davidoff_id = (SELECT brand_id FROM brands WHERE name = 'Davidoff');
SET @guy_laroche_id = (SELECT brand_id FROM brands WHERE name = 'Guy Laroche');
SET @calvin_klein_id = (SELECT brand_id FROM brands WHERE name = 'Calvin Klein');
SET @houbigant_id = (SELECT brand_id FROM brands WHERE name = 'Houbigant');
SET @jean_paul_gaultier_id = (SELECT brand_id FROM brands WHERE name = 'Jean Paul Gaultier');
SET @paco_rabanne_id = (SELECT brand_id FROM brands WHERE name = 'Paco Rabanne');
SET @ysl_id = (SELECT brand_id FROM brands WHERE name = 'Yves Saint Laurent');
SET @versace_id = (SELECT brand_id FROM brands WHERE name = 'Versace');

-- Insérer les produits fougère pour hommes
INSERT INTO products (name, description, price, stock_quantity, image, category_id, brand_id, volume, concentration, gender) VALUES
('Azzaro Pour Homme', 'Un classique intemporel avec des notes aromatiques de lavande, d\'anis et de bois de santal', 65.00, 55, '/images/products/men/Fougere/Azzaro Pour Homme – Azzaro.jpg', @fougere_category_id, @azzaro_id, 100, 'Eau de Toilette', 'Homme'),
('Brut', 'Un parfum emblématique avec des notes aromatiques d\'anis, de lavande et de mousse de chêne', 30.00, 80, '/images/products/men/Fougere/Brut – Fabergé.jpg', @fougere_category_id, @faberge_id, 100, 'Eau de Toilette', 'Homme'),
('Cool Water', 'Une fragrance fraîche et aromatique avec des notes de menthe, de lavande et de bois d\'ambre', 65.50, 75, '/images/products/men/Fougere/Cool Water – Davidoff.jpg', @fougere_category_id, @davidoff_id, 125, 'Eau de Toilette', 'Homme'),
('Drakkar Noir', 'Un parfum puissant et masculin avec des notes de lavande, de citron et de bois de santal', 50.00, 60, '/images/products/men/Fougere/Drakkar Noir – Guy Laroche.jpg', @fougere_category_id, @guy_laroche_id, 100, 'Eau de Toilette', 'Homme'),
('Eternity for Men', 'Un parfum frais et élégant avec des notes de lavande, de vétiver et de bois de santal', 70.00, 45, '/images/products/men/Fougere/Eternity for Men – Calvin Klein.jpg', @fougere_category_id, @calvin_klein_id, 100, 'Eau de Toilette', 'Homme'),
('Houbigant Fougère Royale', 'Le parfum original qui a donné son nom à la famille olfactive fougère, avec des notes de lavande, géranium et mousse de chêne', 190.00, 20, '/images/products/men/Fougere/Houbigant Fougere Royale – Houbigant.jpg', @fougere_category_id, @houbigant_id, 100, 'Eau de Parfum', 'Homme'),
('Le Mâle', 'Un parfum iconique avec des notes de lavande, de vanille et de bois de santal', 85.00, 65, '/images/products/men/Fougere/Le Mâle – Jean Paul Gaultier.jpg', @fougere_category_id, @jean_paul_gaultier_id, 125, 'Eau de Toilette', 'Homme'),
('Paco Rabanne Pour Homme', 'Un classique aromatique avec des notes de romarin, de lavande et de mousse de chêne', 70.00, 40, '/images/products/men/Fougere/Paco Rabanne Pour Homme – Paco Rabanne.jpg', @fougere_category_id, @paco_rabanne_id, 100, 'Eau de Toilette', 'Homme'),
('Rive Gauche Pour Homme', 'Un parfum élégant et sophistiqué avec des notes d\'anis étoilé, de lavande et de patchouli', 95.00, 25, '/images/products/men/Fougere/Rive Gauche Pour Homme – Yves Saint Laurent.jpg', @fougere_category_id, @ysl_id, 80, 'Eau de Toilette', 'Homme'),
('Versace Pour Homme', 'Une fragrance méditerranéenne avec des notes de citron, de fleurs aromatiques et de bois de cèdre', 75.00, 50, '/images/products/men/Fougere/Versace Pour Homme – Versace.jpg', @fougere_category_id, @versace_id, 100, 'Eau de Toilette', 'Homme');
UPDATE brands SET image = 'images/brands/bvlgari.jpg' WHERE name = 'Bvlgari';
UPDATE brands SET image = 'images/brands/davidoff.jpg' WHERE name = 'Davidoff';
UPDATE brands SET image = 'images/brands/pacorabanne.jpg' WHERE name = 'Paco Rabanne';
UPDATE brands SET image = 'images/brands/kenzo.jpg' WHERE name = 'Kenzo';
UPDATE brands SET image = 'images/brands/dolcegabbana.jpg' WHERE name = 'Dolce & Gabbana';
UPDATE brands SET image = 'images/brands/ralphlauren.jpg' WHERE name = 'Ralph Lauren';
UPDATE brands SET image = 'images/brands/versace.jpg' WHERE name = 'Versace';
UPDATE brands SET image = 'images/brands/acquadiparma.jpg' WHERE name = 'Acqua di Parma';
UPDATE brands SET image = 'images/brands/montblanc.jpg' WHERE name = 'Montblanc';
UPDATE brands SET image = 'images/brands/armaf.jpg' WHERE name = 'Armaf';
UPDATE brands SET image = 'images/brands/givenchy.jpg' WHERE name = 'Givenchy';
UPDATE brands SET image = 'images/brands/rojaparfums.jpg' WHERE name = 'Roja Parfums';
UPDATE brands SET image = 'images/brands/giorgioarmani.jpg' WHERE name = 'Giorgio Armani';
UPDATE brands SET image = 'images/brands/mancera.jpg' WHERE name = 'Mancera';
UPDATE brands SET image = 'images/brands/calvinklein.jpg' WHERE name = 'Calvin Klein';
UPDATE brands SET image = 'images/brands/isseymiyake.jpg' WHERE name = 'Issey Miyake';
UPDATE brands SET image = 'images/brands/nautica.jpg' WHERE name = 'Nautica';
UPDATE users SET is_admin = 1 WHERE username = 'hana';
CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    content TEXT NOT NULL,
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) DEFAULT 0,
    FOREIGN KEY (sender_id) REFERENCES users(user_id),
    FOREIGN KEY (receiver_id) REFERENCES users(user_id)
);