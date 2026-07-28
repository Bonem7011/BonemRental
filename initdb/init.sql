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

-- Fonction pour ajouter une carrosserie et l'associer aux gammes
CREATE OR REPLACE FUNCTION add_carrosserie(p_nom VARCHAR)
RETURNS BOOLEAN AS $$
DECLARE
v_id_new INTEGER;
BEGIN
    -- 1. On crée la carrosserie et on récupère son nouvel ID
INSERT INTO carrosserie (nom_carrosserie) VALUES (p_nom) RETURNING id_carrosserie INTO v_id_new;

-- 2. On l'associe automatiquement aux 3 gammes (1=Basique, 2=Sport, 3=Luxury)
INSERT INTO gamme_carrosserie (id_gamme, id_carrosserie) VALUES (1, v_id_new), (2, v_id_new), (3, v_id_new);

-- Si on arrive ici sans erreur, la transaction implicite est validée
RETURN TRUE;
EXCEPTION
    WHEN OTHERS THEN
        -- En cas d'erreur, PostgreSQL fait un Rollback automatique
        RETURN FALSE;
END;
$$ LANGUAGE plpgsql;

-- Fonction pour supprimer une carrosserie
CREATE OR REPLACE FUNCTION delete_carrosserie(p_id INTEGER)
RETURNS BOOLEAN AS $$
BEGIN
DELETE FROM carrosserie WHERE id_carrosserie = p_id;

IF FOUND THEN
        RETURN TRUE;
ELSE
        RETURN FALSE;
END IF;
EXCEPTION
    WHEN OTHERS THEN
        RETURN FALSE;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION add_client(
    p_email VARCHAR,
    p_password VARCHAR,
    p_nom VARCHAR,
    p_prenom VARCHAR,
    p_adresse VARCHAR,
    p_numero VARCHAR,
    p_telephone VARCHAR
) RETURNS BOOLEAN AS $$
DECLARE
v_count INTEGER;
BEGIN
    -- 1. On vérifie si l'email existe déjà directement dans la base de données
SELECT COUNT(*) INTO v_count FROM client WHERE email_client = p_email;
IF v_count > 0 THEN
        RETURN FALSE; -- L'email est déjà pris
END IF;

    -- 2. On insère le nouveau client
INSERT INTO client (email_client, password_client, nom_client, prenom_client, adresse_client, numero_client, telephone_client)
VALUES (p_email, p_password, p_nom, p_prenom, p_adresse, p_numero, p_telephone);

RETURN TRUE;
EXCEPTION
    WHEN OTHERS THEN
        RETURN FALSE;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION creer_commande(
    p_id_client INTEGER,
    p_id_vehicule INTEGER,
    p_type VARCHAR,
    p_montant NUMERIC,
    p_date_debut DATE DEFAULT NULL,
    p_date_fin DATE DEFAULT NULL
) RETURNS BOOLEAN AS $$
DECLARE
v_nouveau_statut VARCHAR;
BEGIN
    -- 1. On crée la commande
INSERT INTO commande (id_client, id_vehicule, type_commande, montant_total, date_debut, date_fin)
VALUES (p_id_client, p_id_vehicule, p_type, p_montant, p_date_debut, p_date_fin);

-- 2. On détermine le nouveau statut du véhicule selon le type de commande
IF p_type = 'Achat' THEN
        v_nouveau_statut := 'Vendu';
ELSE
        v_nouveau_statut := 'Loué';
END IF;

    -- 3. On met à jour le statut du véhicule
UPDATE vehicule SET status = v_nouveau_statut WHERE id_vehicule = p_id_vehicule;

-- Si on arrive ici, tout s'est bien passé
RETURN TRUE;
EXCEPTION
    WHEN OTHERS THEN
        -- En cas d'erreur sur l'insert ou l'update, tout est annulé (Rollback automatique)
        RETURN FALSE;
END;
$$ LANGUAGE plpgsql;


-- 1. Fonction pour AJOUTER un véhicule
CREATE OR REPLACE FUNCTION ajouter_vehicule(
    p_id_gamme INTEGER,
    p_id_carrosserie INTEGER,
    p_marque VARCHAR,
    p_modele VARCHAR,
    p_prix_achat NUMERIC,
    p_prix_location NUMERIC,
    p_caution NUMERIC,
    p_status VARCHAR,
    p_image VARCHAR
) RETURNS BOOLEAN AS $$
BEGIN
INSERT INTO vehicule (id_gamme, id_carrosserie, marque, modele, prix_achat, prix_location, caution, status, image)
VALUES (p_id_gamme, p_id_carrosserie, p_marque, p_modele, p_prix_achat, p_prix_location, p_caution, p_status, p_image);
RETURN TRUE;
EXCEPTION
    WHEN OTHERS THEN
        RETURN FALSE;
END;
$$ LANGUAGE plpgsql;

-- 2. Fonction pour MODIFIER un véhicule
CREATE OR REPLACE FUNCTION modifier_vehicule(
    p_id_vehicule INTEGER,
    p_id_gamme INTEGER,
    p_id_carrosserie INTEGER,
    p_marque VARCHAR,
    p_modele VARCHAR,
    p_prix_achat NUMERIC,
    p_prix_location NUMERIC,
    p_caution NUMERIC,
    p_status VARCHAR,
    p_image VARCHAR
) RETURNS BOOLEAN AS $$
BEGIN
UPDATE vehicule
SET id_gamme = p_id_gamme,
    id_carrosserie = p_id_carrosserie,
    marque = p_marque,
    modele = p_modele,
    prix_achat = p_prix_achat,
    prix_location = p_prix_location,
    caution = p_caution,
    status = p_status,
    image = p_image
WHERE id_vehicule = p_id_vehicule;
RETURN TRUE;
EXCEPTION
    WHEN OTHERS THEN
        RETURN FALSE;
END;
$$ LANGUAGE plpgsql;

-- 3. Fonction pour SUPPRIMER un véhicule
CREATE OR REPLACE FUNCTION supprimer_vehicule(p_id_vehicule INTEGER) RETURNS BOOLEAN AS $$
BEGIN
DELETE FROM vehicule WHERE id_vehicule = p_id_vehicule;
RETURN TRUE;
EXCEPTION
    WHEN OTHERS THEN
        RETURN FALSE;
END;
$$ LANGUAGE plpgsql;