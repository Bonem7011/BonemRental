-- Création des tables de base pour BonemRental
CREATE TABLE admin (
                       id_admin serial primary key,
                       nom_admin varchar(50) not null,
                       password text not null,
                       statut int not null default 1
);

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

CREATE TABLE categorie (
                           id_categorie serial primary key,
                           nom_categorie varchar(50) not null unique
);

CREATE TABLE vehicule (
                          id_vehicule serial primary key,
                          id_categorie int not null references categorie(id_categorie),
                          marque varchar(50) not null,
                          modele varchar(50) not null,
                          prix_achat numeric,
                          prix_location numeric,
                          status varchar(20) default 'Disponible',
                          image text
);

-- Insertion des données initiales
INSERT INTO admin (nom_admin, password, statut) VALUES ('alan', 'admin123', 1);
INSERT INTO categorie (nom_categorie) VALUES ('Basique'), ('Sport'), ('Luxury'), ('Compact'), ('Berline'), ('SUV');

-- Fonction plpgsql exigée pour la sécurité
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