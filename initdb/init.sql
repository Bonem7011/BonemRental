-- Suppression des anciennes tables si elles existent
DROP TABLE IF EXISTS vehicule CASCADE;
DROP TABLE IF EXISTS categorie CASCADE;
DROP TABLE IF EXISTS client CASCADE;
DROP TABLE IF EXISTS admin CASCADE;
DROP TABLE IF EXISTS gamme CASCADE;
DROP TABLE IF EXISTS carrosserie CASCADE;
DROP TABLE IF EXISTS gamme_carrosserie CASCADE;

-- 1. Table Admin
CREATE TABLE admin (
                       id_admin serial primary key,
                       nom_admin varchar(50) not null,
                       password text not null,
                       statut int not null default 1
);

-- 2. Table Client
CREATE TABLE client (
                        id_client serial primary key,
                        email_client varchar(100) not null unique,
                        password_client text not null,
                        nom_client varchar(50) not null,
                        prenom_client varchar(50) not null,
                        adresse_client text,
                        numero_client varchar(10),
                        telephone_client varchar(20)
);

-- 3. Nouvelle table : Gamme
CREATE TABLE gamme (
                       id_gamme serial primary key,
                       nom_gamme varchar(50) not null unique
);

-- 4. Nouvelle table : Carrosserie
CREATE TABLE carrosserie (
                             id_carrosserie serial primary key,
                             nom_carrosserie varchar(50) not null unique
);

-- 5. Table d'association (Liaison Gamme <-> Carrosserie pour AJAX)
CREATE TABLE gamme_carrosserie (
                                   id_gamme int references gamme(id_gamme) on delete cascade,
                                   id_carrosserie int references carrosserie(id_carrosserie) on delete cascade,
                                   primary key (id_gamme, id_carrosserie)
);

-- 6. Table Véhicule mise à jour (liée à la gamme ET à la carrosserie)
CREATE TABLE vehicule (
                          id_vehicule serial primary key,
                          id_gamme int not null references gamme(id_gamme),
                          id_carrosserie int not null references carrosserie(id_carrosserie),
                          marque varchar(50) not null,
                          modele varchar(50) not null,
                          prix_achat numeric,
                          prix_location numeric,
                          caution numeric default 0,
                          status varchar(20) default 'Disponible',
                          image text
);

-- 7. Table des Commandes / Locations
CREATE TABLE commande (
                          id_commande serial primary key,
                          id_client int not null references client(id_client) on delete cascade,
                          id_vehicule int not null references vehicule(id_vehicule) on delete cascade,
                          type_commande varchar(20) not null, -- 'Achat' ou 'Location'
                          date_debut date, -- NULL si c'est un achat
                          date_fin date, -- NULL si c'est un achat
                          montant_total numeric not null,
                          date_creation timestamp default current_timestamp
);

-- Insertions des données initiales
INSERT INTO admin (nom_admin, password, statut) VALUES ('alan', 'admin123', 1);

INSERT INTO gamme (nom_gamme) VALUES ('Basique'), ('Sport'), ('Luxury');
INSERT INTO carrosserie (nom_carrosserie) VALUES ('Compact'), ('Berline'), ('SUV');

-- Liaisons de TOUTES les combinaisons possibles (puisqu'elles existent toutes)
INSERT INTO gamme_carrosserie (id_gamme, id_carrosserie) VALUES
                                                             (1, 1), (1, 2), (1, 3), -- Basique (Compact, Berline, SUV)
                                                             (2, 1), (2, 2), (2, 3), -- Sport (Compact, Berline, SUV)
                                                             (3, 1), (3, 2), (3, 3); -- Luxury (Compact, Berline, SUV)

-- Fonction plpgsql de sécurité
CREATE OR REPLACE FUNCTION get_admin(p_nom_admin text, p_password text)
RETURNS TABLE (id_admin int, nom_admin varchar, statut int) AS $$
BEGIN
RETURN QUERY SELECT a.id_admin, a.nom_admin, a.statut FROM admin a
    WHERE a.nom_admin = p_nom_admin AND a.password = p_password;
IF NOT FOUND THEN
        RETURN QUERY SELECT -1, cast('' as varchar), -1;
END IF;
END;
$$ LANGUAGE plpgsql;