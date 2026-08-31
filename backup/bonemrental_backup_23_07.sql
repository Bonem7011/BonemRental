--
-- PostgreSQL database dump
--

\restrict 3QqdWeHir5d2ad3TQwL4xKTlNtQO9fV66c0IBzF1w2Lsu9dXNIshGgS2hkcIhWI

-- Dumped from database version 18.1
-- Dumped by pg_dump version 18.1

-- Started on 2026-07-23 22:12:19

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 233 (class 1255 OID 24928)
-- Name: add_carrosserie(character varying); Type: FUNCTION; Schema: public; Owner: anonyme
--

CREATE FUNCTION public.add_carrosserie(p_nom character varying) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
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
$$;


ALTER FUNCTION public.add_carrosserie(p_nom character varying) OWNER TO anonyme;

--
-- TOC entry 235 (class 1255 OID 24930)
-- Name: add_client(character varying, character varying, character varying, character varying, character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: anonyme
--

CREATE FUNCTION public.add_client(p_email character varying, p_password character varying, p_nom character varying, p_prenom character varying, p_adresse character varying, p_numero character varying, p_telephone character varying) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
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
$$;


ALTER FUNCTION public.add_client(p_email character varying, p_password character varying, p_nom character varying, p_prenom character varying, p_adresse character varying, p_numero character varying, p_telephone character varying) OWNER TO anonyme;

--
-- TOC entry 248 (class 1255 OID 24932)
-- Name: ajouter_vehicule(integer, integer, character varying, character varying, numeric, numeric, numeric, character varying, character varying); Type: FUNCTION; Schema: public; Owner: anonyme
--

CREATE FUNCTION public.ajouter_vehicule(p_id_gamme integer, p_id_carrosserie integer, p_marque character varying, p_modele character varying, p_prix_achat numeric, p_prix_location numeric, p_caution numeric, p_status character varying, p_image character varying) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
BEGIN
    INSERT INTO vehicule (id_gamme, id_carrosserie, marque, modele, prix_achat, prix_location, caution, status, image) 
    VALUES (p_id_gamme, p_id_carrosserie, p_marque, p_modele, p_prix_achat, p_prix_location, p_caution, p_status, p_image);
    RETURN TRUE;
EXCEPTION
    WHEN OTHERS THEN
        RETURN FALSE;
END;
$$;


ALTER FUNCTION public.ajouter_vehicule(p_id_gamme integer, p_id_carrosserie integer, p_marque character varying, p_modele character varying, p_prix_achat numeric, p_prix_location numeric, p_caution numeric, p_status character varying, p_image character varying) OWNER TO anonyme;

--
-- TOC entry 247 (class 1255 OID 24931)
-- Name: creer_commande(integer, integer, character varying, numeric, date, date); Type: FUNCTION; Schema: public; Owner: anonyme
--

CREATE FUNCTION public.creer_commande(p_id_client integer, p_id_vehicule integer, p_type character varying, p_montant numeric, p_date_debut date DEFAULT NULL::date, p_date_fin date DEFAULT NULL::date) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
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
$$;


ALTER FUNCTION public.creer_commande(p_id_client integer, p_id_vehicule integer, p_type character varying, p_montant numeric, p_date_debut date, p_date_fin date) OWNER TO anonyme;

--
-- TOC entry 234 (class 1255 OID 24929)
-- Name: delete_carrosserie(integer); Type: FUNCTION; Schema: public; Owner: anonyme
--

CREATE FUNCTION public.delete_carrosserie(p_id integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
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
$$;


ALTER FUNCTION public.delete_carrosserie(p_id integer) OWNER TO anonyme;

--
-- TOC entry 232 (class 1255 OID 24927)
-- Name: get_admin(text, text); Type: FUNCTION; Schema: public; Owner: anonyme
--

CREATE FUNCTION public.get_admin(p_nom_admin text, p_password text) RETURNS TABLE(id_admin integer, nom_admin character varying, statut integer)
    LANGUAGE plpgsql
    AS $$
BEGIN
RETURN QUERY SELECT a.id_admin, a.nom_admin, a.statut FROM admin a
    WHERE a.nom_admin = p_nom_admin AND a.password = p_password;
IF NOT FOUND THEN
        RETURN QUERY SELECT -1, cast('' as varchar), -1;
END IF;
END;
$$;


ALTER FUNCTION public.get_admin(p_nom_admin text, p_password text) OWNER TO anonyme;

--
-- TOC entry 249 (class 1255 OID 24933)
-- Name: modifier_vehicule(integer, integer, integer, character varying, character varying, numeric, numeric, numeric, character varying, character varying); Type: FUNCTION; Schema: public; Owner: anonyme
--

CREATE FUNCTION public.modifier_vehicule(p_id_vehicule integer, p_id_gamme integer, p_id_carrosserie integer, p_marque character varying, p_modele character varying, p_prix_achat numeric, p_prix_location numeric, p_caution numeric, p_status character varying, p_image character varying) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
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
$$;


ALTER FUNCTION public.modifier_vehicule(p_id_vehicule integer, p_id_gamme integer, p_id_carrosserie integer, p_marque character varying, p_modele character varying, p_prix_achat numeric, p_prix_location numeric, p_caution numeric, p_status character varying, p_image character varying) OWNER TO anonyme;

--
-- TOC entry 250 (class 1255 OID 24934)
-- Name: supprimer_vehicule(integer); Type: FUNCTION; Schema: public; Owner: anonyme
--

CREATE FUNCTION public.supprimer_vehicule(p_id_vehicule integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
BEGIN
    DELETE FROM vehicule WHERE id_vehicule = p_id_vehicule;
    RETURN TRUE;
EXCEPTION
    WHEN OTHERS THEN
        RETURN FALSE;
END;
$$;


ALTER FUNCTION public.supprimer_vehicule(p_id_vehicule integer) OWNER TO anonyme;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 220 (class 1259 OID 24808)
-- Name: admin; Type: TABLE; Schema: public; Owner: anonyme
--

CREATE TABLE public.admin (
    id_admin integer NOT NULL,
    nom_admin character varying(50) NOT NULL,
    password text NOT NULL,
    statut integer DEFAULT 1 NOT NULL
);


ALTER TABLE public.admin OWNER TO anonyme;

--
-- TOC entry 219 (class 1259 OID 24807)
-- Name: admin_id_admin_seq; Type: SEQUENCE; Schema: public; Owner: anonyme
--

CREATE SEQUENCE public.admin_id_admin_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.admin_id_admin_seq OWNER TO anonyme;

--
-- TOC entry 5094 (class 0 OID 0)
-- Dependencies: 219
-- Name: admin_id_admin_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: anonyme
--

ALTER SEQUENCE public.admin_id_admin_seq OWNED BY public.admin.id_admin;


--
-- TOC entry 226 (class 1259 OID 24849)
-- Name: carrosserie; Type: TABLE; Schema: public; Owner: anonyme
--

CREATE TABLE public.carrosserie (
    id_carrosserie integer NOT NULL,
    nom_carrosserie character varying(50) NOT NULL
);


ALTER TABLE public.carrosserie OWNER TO anonyme;

--
-- TOC entry 225 (class 1259 OID 24848)
-- Name: carrosserie_id_carrosserie_seq; Type: SEQUENCE; Schema: public; Owner: anonyme
--

CREATE SEQUENCE public.carrosserie_id_carrosserie_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.carrosserie_id_carrosserie_seq OWNER TO anonyme;

--
-- TOC entry 5095 (class 0 OID 0)
-- Dependencies: 225
-- Name: carrosserie_id_carrosserie_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: anonyme
--

ALTER SEQUENCE public.carrosserie_id_carrosserie_seq OWNED BY public.carrosserie.id_carrosserie;


--
-- TOC entry 222 (class 1259 OID 24822)
-- Name: client; Type: TABLE; Schema: public; Owner: anonyme
--

CREATE TABLE public.client (
    id_client integer NOT NULL,
    email_client character varying(100) NOT NULL,
    password_client text NOT NULL,
    nom_client character varying(50) NOT NULL,
    prenom_client character varying(50) NOT NULL,
    adresse_client text,
    numero_client character varying(10),
    telephone_client character varying(20)
);


ALTER TABLE public.client OWNER TO anonyme;

--
-- TOC entry 221 (class 1259 OID 24821)
-- Name: client_id_client_seq; Type: SEQUENCE; Schema: public; Owner: anonyme
--

CREATE SEQUENCE public.client_id_client_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.client_id_client_seq OWNER TO anonyme;

--
-- TOC entry 5096 (class 0 OID 0)
-- Dependencies: 221
-- Name: client_id_client_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: anonyme
--

ALTER SEQUENCE public.client_id_client_seq OWNED BY public.client.id_client;


--
-- TOC entry 231 (class 1259 OID 24903)
-- Name: commande; Type: TABLE; Schema: public; Owner: anonyme
--

CREATE TABLE public.commande (
    id_commande integer NOT NULL,
    id_client integer NOT NULL,
    id_vehicule integer NOT NULL,
    type_commande character varying(20) NOT NULL,
    date_debut date,
    date_fin date,
    montant_total numeric NOT NULL,
    date_creation timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.commande OWNER TO anonyme;

--
-- TOC entry 230 (class 1259 OID 24902)
-- Name: commande_id_commande_seq; Type: SEQUENCE; Schema: public; Owner: anonyme
--

CREATE SEQUENCE public.commande_id_commande_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.commande_id_commande_seq OWNER TO anonyme;

--
-- TOC entry 5097 (class 0 OID 0)
-- Dependencies: 230
-- Name: commande_id_commande_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: anonyme
--

ALTER SEQUENCE public.commande_id_commande_seq OWNED BY public.commande.id_commande;


--
-- TOC entry 224 (class 1259 OID 24838)
-- Name: gamme; Type: TABLE; Schema: public; Owner: anonyme
--

CREATE TABLE public.gamme (
    id_gamme integer NOT NULL,
    nom_gamme character varying(50) NOT NULL
);


ALTER TABLE public.gamme OWNER TO anonyme;

--
-- TOC entry 227 (class 1259 OID 24859)
-- Name: gamme_carrosserie; Type: TABLE; Schema: public; Owner: anonyme
--

CREATE TABLE public.gamme_carrosserie (
    id_gamme integer NOT NULL,
    id_carrosserie integer NOT NULL
);


ALTER TABLE public.gamme_carrosserie OWNER TO anonyme;

--
-- TOC entry 223 (class 1259 OID 24837)
-- Name: gamme_id_gamme_seq; Type: SEQUENCE; Schema: public; Owner: anonyme
--

CREATE SEQUENCE public.gamme_id_gamme_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.gamme_id_gamme_seq OWNER TO anonyme;

--
-- TOC entry 5098 (class 0 OID 0)
-- Dependencies: 223
-- Name: gamme_id_gamme_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: anonyme
--

ALTER SEQUENCE public.gamme_id_gamme_seq OWNED BY public.gamme.id_gamme;


--
-- TOC entry 229 (class 1259 OID 24877)
-- Name: vehicule; Type: TABLE; Schema: public; Owner: anonyme
--

CREATE TABLE public.vehicule (
    id_vehicule integer NOT NULL,
    id_gamme integer NOT NULL,
    id_carrosserie integer NOT NULL,
    marque character varying(50) NOT NULL,
    modele character varying(50) NOT NULL,
    prix_achat numeric,
    prix_location numeric,
    caution numeric DEFAULT 0,
    status character varying(20) DEFAULT 'Disponible'::character varying,
    image text
);


ALTER TABLE public.vehicule OWNER TO anonyme;

--
-- TOC entry 228 (class 1259 OID 24876)
-- Name: vehicule_id_vehicule_seq; Type: SEQUENCE; Schema: public; Owner: anonyme
--

CREATE SEQUENCE public.vehicule_id_vehicule_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.vehicule_id_vehicule_seq OWNER TO anonyme;

--
-- TOC entry 5099 (class 0 OID 0)
-- Dependencies: 228
-- Name: vehicule_id_vehicule_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: anonyme
--

ALTER SEQUENCE public.vehicule_id_vehicule_seq OWNED BY public.vehicule.id_vehicule;


--
-- TOC entry 4893 (class 2604 OID 24811)
-- Name: admin id_admin; Type: DEFAULT; Schema: public; Owner: anonyme
--

ALTER TABLE ONLY public.admin ALTER COLUMN id_admin SET DEFAULT nextval('public.admin_id_admin_seq'::regclass);


--
-- TOC entry 4897 (class 2604 OID 24852)
-- Name: carrosserie id_carrosserie; Type: DEFAULT; Schema: public; Owner: anonyme
--

ALTER TABLE ONLY public.carrosserie ALTER COLUMN id_carrosserie SET DEFAULT nextval('public.carrosserie_id_carrosserie_seq'::regclass);


--
-- TOC entry 4895 (class 2604 OID 24825)
-- Name: client id_client; Type: DEFAULT; Schema: public; Owner: anonyme
--

ALTER TABLE ONLY public.client ALTER COLUMN id_client SET DEFAULT nextval('public.client_id_client_seq'::regclass);


--
-- TOC entry 4901 (class 2604 OID 24906)
-- Name: commande id_commande; Type: DEFAULT; Schema: public; Owner: anonyme
--

ALTER TABLE ONLY public.commande ALTER COLUMN id_commande SET DEFAULT nextval('public.commande_id_commande_seq'::regclass);


--
-- TOC entry 4896 (class 2604 OID 24841)
-- Name: gamme id_gamme; Type: DEFAULT; Schema: public; Owner: anonyme
--

ALTER TABLE ONLY public.gamme ALTER COLUMN id_gamme SET DEFAULT nextval('public.gamme_id_gamme_seq'::regclass);


--
-- TOC entry 4898 (class 2604 OID 24880)
-- Name: vehicule id_vehicule; Type: DEFAULT; Schema: public; Owner: anonyme
--

ALTER TABLE ONLY public.vehicule ALTER COLUMN id_vehicule SET DEFAULT nextval('public.vehicule_id_vehicule_seq'::regclass);


--
-- TOC entry 5077 (class 0 OID 24808)
-- Dependencies: 220
-- Data for Name: admin; Type: TABLE DATA; Schema: public; Owner: anonyme
--

COPY public.admin (id_admin, nom_admin, password, statut) FROM stdin;
1	alan	admin123	1
\.


--
-- TOC entry 5083 (class 0 OID 24849)
-- Dependencies: 226
-- Data for Name: carrosserie; Type: TABLE DATA; Schema: public; Owner: anonyme
--

COPY public.carrosserie (id_carrosserie, nom_carrosserie) FROM stdin;
1	Compact
2	Berline
3	SUV
\.


--
-- TOC entry 5079 (class 0 OID 24822)
-- Dependencies: 222
-- Data for Name: client; Type: TABLE DATA; Schema: public; Owner: anonyme
--

COPY public.client (id_client, email_client, password_client, nom_client, prenom_client, adresse_client, numero_client, telephone_client) FROM stdin;
\.


--
-- TOC entry 5088 (class 0 OID 24903)
-- Dependencies: 231
-- Data for Name: commande; Type: TABLE DATA; Schema: public; Owner: anonyme
--

COPY public.commande (id_commande, id_client, id_vehicule, type_commande, date_debut, date_fin, montant_total, date_creation) FROM stdin;
\.


--
-- TOC entry 5081 (class 0 OID 24838)
-- Dependencies: 224
-- Data for Name: gamme; Type: TABLE DATA; Schema: public; Owner: anonyme
--

COPY public.gamme (id_gamme, nom_gamme) FROM stdin;
1	Basique
2	Sport
3	Luxury
\.


--
-- TOC entry 5084 (class 0 OID 24859)
-- Dependencies: 227
-- Data for Name: gamme_carrosserie; Type: TABLE DATA; Schema: public; Owner: anonyme
--

COPY public.gamme_carrosserie (id_gamme, id_carrosserie) FROM stdin;
1	1
1	2
1	3
2	1
2	2
2	3
3	1
3	2
3	3
\.


--
-- TOC entry 5086 (class 0 OID 24877)
-- Dependencies: 229
-- Data for Name: vehicule; Type: TABLE DATA; Schema: public; Owner: anonyme
--

COPY public.vehicule (id_vehicule, id_gamme, id_carrosserie, marque, modele, prix_achat, prix_location, caution, status, image) FROM stdin;
1	1	1	Peugeot	207	3000	25	200	Disponible	207.png
2	2	1	Volkswagen	Golf 8R	60000	280	2000	Disponible	golf8r.webp
3	2	2	bmw	M4 CS	70000	350	3000	Disponible	m4cs.png
4	1	1	Opel	Corsa	2500	20	150	Disponible	corsa.png
\.


--
-- TOC entry 5100 (class 0 OID 0)
-- Dependencies: 219
-- Name: admin_id_admin_seq; Type: SEQUENCE SET; Schema: public; Owner: anonyme
--

SELECT pg_catalog.setval('public.admin_id_admin_seq', 1, true);


--
-- TOC entry 5101 (class 0 OID 0)
-- Dependencies: 225
-- Name: carrosserie_id_carrosserie_seq; Type: SEQUENCE SET; Schema: public; Owner: anonyme
--

SELECT pg_catalog.setval('public.carrosserie_id_carrosserie_seq', 3, true);


--
-- TOC entry 5102 (class 0 OID 0)
-- Dependencies: 221
-- Name: client_id_client_seq; Type: SEQUENCE SET; Schema: public; Owner: anonyme
--

SELECT pg_catalog.setval('public.client_id_client_seq', 1, false);


--
-- TOC entry 5103 (class 0 OID 0)
-- Dependencies: 230
-- Name: commande_id_commande_seq; Type: SEQUENCE SET; Schema: public; Owner: anonyme
--

SELECT pg_catalog.setval('public.commande_id_commande_seq', 1, false);


--
-- TOC entry 5104 (class 0 OID 0)
-- Dependencies: 223
-- Name: gamme_id_gamme_seq; Type: SEQUENCE SET; Schema: public; Owner: anonyme
--

SELECT pg_catalog.setval('public.gamme_id_gamme_seq', 3, true);


--
-- TOC entry 5105 (class 0 OID 0)
-- Dependencies: 228
-- Name: vehicule_id_vehicule_seq; Type: SEQUENCE SET; Schema: public; Owner: anonyme
--

SELECT pg_catalog.setval('public.vehicule_id_vehicule_seq', 4, true);


--
-- TOC entry 4904 (class 2606 OID 24820)
-- Name: admin admin_pkey; Type: CONSTRAINT; Schema: public; Owner: anonyme
--

ALTER TABLE ONLY public.admin
    ADD CONSTRAINT admin_pkey PRIMARY KEY (id_admin);


--
-- TOC entry 4914 (class 2606 OID 24858)
-- Name: carrosserie carrosserie_nom_carrosserie_key; Type: CONSTRAINT; Schema: public; Owner: anonyme
--

ALTER TABLE ONLY public.carrosserie
    ADD CONSTRAINT carrosserie_nom_carrosserie_key UNIQUE (nom_carrosserie);


--
-- TOC entry 4916 (class 2606 OID 24856)
-- Name: carrosserie carrosserie_pkey; Type: CONSTRAINT; Schema: public; Owner: anonyme
--

ALTER TABLE ONLY public.carrosserie
    ADD CONSTRAINT carrosserie_pkey PRIMARY KEY (id_carrosserie);


--
-- TOC entry 4906 (class 2606 OID 24836)
-- Name: client client_email_client_key; Type: CONSTRAINT; Schema: public; Owner: anonyme
--

ALTER TABLE ONLY public.client
    ADD CONSTRAINT client_email_client_key UNIQUE (email_client);


--
-- TOC entry 4908 (class 2606 OID 24834)
-- Name: client client_pkey; Type: CONSTRAINT; Schema: public; Owner: anonyme
--

ALTER TABLE ONLY public.client
    ADD CONSTRAINT client_pkey PRIMARY KEY (id_client);


--
-- TOC entry 4922 (class 2606 OID 24916)
-- Name: commande commande_pkey; Type: CONSTRAINT; Schema: public; Owner: anonyme
--

ALTER TABLE ONLY public.commande
    ADD CONSTRAINT commande_pkey PRIMARY KEY (id_commande);


--
-- TOC entry 4918 (class 2606 OID 24865)
-- Name: gamme_carrosserie gamme_carrosserie_pkey; Type: CONSTRAINT; Schema: public; Owner: anonyme
--

ALTER TABLE ONLY public.gamme_carrosserie
    ADD CONSTRAINT gamme_carrosserie_pkey PRIMARY KEY (id_gamme, id_carrosserie);


--
-- TOC entry 4910 (class 2606 OID 24847)
-- Name: gamme gamme_nom_gamme_key; Type: CONSTRAINT; Schema: public; Owner: anonyme
--

ALTER TABLE ONLY public.gamme
    ADD CONSTRAINT gamme_nom_gamme_key UNIQUE (nom_gamme);


--
-- TOC entry 4912 (class 2606 OID 24845)
-- Name: gamme gamme_pkey; Type: CONSTRAINT; Schema: public; Owner: anonyme
--

ALTER TABLE ONLY public.gamme
    ADD CONSTRAINT gamme_pkey PRIMARY KEY (id_gamme);


--
-- TOC entry 4920 (class 2606 OID 24891)
-- Name: vehicule vehicule_pkey; Type: CONSTRAINT; Schema: public; Owner: anonyme
--

ALTER TABLE ONLY public.vehicule
    ADD CONSTRAINT vehicule_pkey PRIMARY KEY (id_vehicule);


--
-- TOC entry 4927 (class 2606 OID 24917)
-- Name: commande commande_id_client_fkey; Type: FK CONSTRAINT; Schema: public; Owner: anonyme
--

ALTER TABLE ONLY public.commande
    ADD CONSTRAINT commande_id_client_fkey FOREIGN KEY (id_client) REFERENCES public.client(id_client) ON DELETE CASCADE;


--
-- TOC entry 4928 (class 2606 OID 24922)
-- Name: commande commande_id_vehicule_fkey; Type: FK CONSTRAINT; Schema: public; Owner: anonyme
--

ALTER TABLE ONLY public.commande
    ADD CONSTRAINT commande_id_vehicule_fkey FOREIGN KEY (id_vehicule) REFERENCES public.vehicule(id_vehicule) ON DELETE CASCADE;


--
-- TOC entry 4923 (class 2606 OID 24871)
-- Name: gamme_carrosserie gamme_carrosserie_id_carrosserie_fkey; Type: FK CONSTRAINT; Schema: public; Owner: anonyme
--

ALTER TABLE ONLY public.gamme_carrosserie
    ADD CONSTRAINT gamme_carrosserie_id_carrosserie_fkey FOREIGN KEY (id_carrosserie) REFERENCES public.carrosserie(id_carrosserie) ON DELETE CASCADE;


--
-- TOC entry 4924 (class 2606 OID 24866)
-- Name: gamme_carrosserie gamme_carrosserie_id_gamme_fkey; Type: FK CONSTRAINT; Schema: public; Owner: anonyme
--

ALTER TABLE ONLY public.gamme_carrosserie
    ADD CONSTRAINT gamme_carrosserie_id_gamme_fkey FOREIGN KEY (id_gamme) REFERENCES public.gamme(id_gamme) ON DELETE CASCADE;


--
-- TOC entry 4925 (class 2606 OID 24897)
-- Name: vehicule vehicule_id_carrosserie_fkey; Type: FK CONSTRAINT; Schema: public; Owner: anonyme
--

ALTER TABLE ONLY public.vehicule
    ADD CONSTRAINT vehicule_id_carrosserie_fkey FOREIGN KEY (id_carrosserie) REFERENCES public.carrosserie(id_carrosserie);


--
-- TOC entry 4926 (class 2606 OID 24892)
-- Name: vehicule vehicule_id_gamme_fkey; Type: FK CONSTRAINT; Schema: public; Owner: anonyme
--

ALTER TABLE ONLY public.vehicule
    ADD CONSTRAINT vehicule_id_gamme_fkey FOREIGN KEY (id_gamme) REFERENCES public.gamme(id_gamme);


-- Completed on 2026-07-23 22:12:19

--
-- PostgreSQL database dump complete
--

\unrestrict 3QqdWeHir5d2ad3TQwL4xKTlNtQO9fV66c0IBzF1w2Lsu9dXNIshGgS2hkcIhWI

