--
-- PostgreSQL database dump
--

-- Dumped from database version 17.5 (Debian 17.5-1.pgdg120+1)
-- Dumped by pg_dump version 17.5

-- Started on 2025-06-03 18:19:57 UTC

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
-- TOC entry 4 (class 2615 OID 2200)
-- Name: public; Type: SCHEMA; Schema: -; Owner: pg_database_owner
--

CREATE SCHEMA public;


ALTER SCHEMA public OWNER TO pg_database_owner;

--
-- TOC entry 3485 (class 0 OID 0)
-- Dependencies: 4
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: pg_database_owner
--

COMMENT ON SCHEMA public IS 'standard public schema';


--
-- TOC entry 236 (class 1255 OID 16526)
-- Name: auto_set_priority(); Type: FUNCTION; Schema: public; Owner: docker
--

CREATE FUNCTION public.auto_set_priority() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
    calculated_priority INT := 0;
    car_details_rec RECORD;
    brand_name VARCHAR;
BEGIN
    SELECT * INTO car_details_rec 
    FROM car_details 
    WHERE car_id = NEW.id;
    
    SELECT b.name INTO brand_name
    FROM brands b
    JOIN models m ON b.id = m.brand_id
    WHERE m.id = NEW.model_id;
    
    -- Bazowy priorytet na podstawie statusu
    CASE NEW.status
        WHEN 'available' THEN calculated_priority := 3;
        WHEN 'reserved' THEN calculated_priority := 2;
        WHEN 'sold' THEN calculated_priority := 1;
        ELSE calculated_priority := 1;
    END CASE;
    
    -- Zwiększ priorytet dla nowych samochodów
    IF NEW.is_new = TRUE THEN
        calculated_priority := calculated_priority + 1;
    END IF;
    
    -- Zwiększ priorytet dla luksusowych marek
    IF brand_name IN ('BMW', 'Mercedes-Benz', 'Audi', 'Porsche', 'Ferrari', 'Lamborghini') THEN
        calculated_priority := calculated_priority + 1;
    END IF;
    
    -- Zwiększ priorytet dla mocnych samochodów
    IF car_details_rec.horsepower > 300 THEN
        calculated_priority := calculated_priority + 1;
    END IF;
    
    -- Ograniczenie priorytetu do zakresu 1-5
    calculated_priority := GREATEST(1, LEAST(5, calculated_priority));
    
    NEW.priority := calculated_priority;
    
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.auto_set_priority() OWNER TO docker;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 220 (class 1259 OID 16398)
-- Name: brands; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.brands (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    is_active boolean DEFAULT true
);


ALTER TABLE public.brands OWNER TO docker;

--
-- TOC entry 219 (class 1259 OID 16397)
-- Name: brands_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.brands_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.brands_id_seq OWNER TO docker;

--
-- TOC entry 3486 (class 0 OID 0)
-- Dependencies: 219
-- Name: brands_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.brands_id_seq OWNED BY public.brands.id;


--
-- TOC entry 225 (class 1259 OID 16444)
-- Name: car_details; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.car_details (
    id integer NOT NULL,
    car_id uuid NOT NULL,
    mileage integer NOT NULL,
    fuel_type character varying(50) NOT NULL,
    engine_size numeric(4,1) NOT NULL,
    horsepower integer,
    transmission character varying(50),
    color character varying(50),
    description text,
    CONSTRAINT car_details_engine_size_check CHECK ((engine_size > (0)::numeric)),
    CONSTRAINT car_details_horsepower_check CHECK ((horsepower > 0)),
    CONSTRAINT car_details_mileage_check CHECK ((mileage >= 0))
);


ALTER TABLE public.car_details OWNER TO docker;

--
-- TOC entry 223 (class 1259 OID 16422)
-- Name: cars; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.cars (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    model_id integer,
    user_id integer,
    title character varying(255) NOT NULL,
    year integer NOT NULL,
    price numeric(10,2) NOT NULL,
    priority integer DEFAULT 0,
    status character varying(50),
    is_active boolean DEFAULT true,
    is_new boolean DEFAULT false,
    added_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT cars_year_check CHECK (((year > 1900) AND ((year)::numeric <= EXTRACT(year FROM CURRENT_DATE))))
);


ALTER TABLE public.cars OWNER TO docker;

--
-- TOC entry 222 (class 1259 OID 16408)
-- Name: models; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.models (
    id integer NOT NULL,
    brand_id integer,
    name character varying(100) NOT NULL,
    is_active boolean DEFAULT true
);


ALTER TABLE public.models OWNER TO docker;

--
-- TOC entry 234 (class 1259 OID 16516)
-- Name: car_detail_view; Type: VIEW; Schema: public; Owner: docker
--

CREATE VIEW public.car_detail_view AS
 SELECT cars.id,
    cars.model_id,
    cars.user_id,
    cars.title,
    cars.year,
    cars.price,
    cars.priority,
    cars.status,
    cars.is_active,
    cars.is_new,
    cars.added_at,
    car_details.color,
    car_details.description,
    car_details.engine_size,
    car_details.fuel_type,
    car_details.horsepower,
    car_details.mileage,
    car_details.transmission,
    brands.name AS brand_name,
    models.name AS model_name
   FROM (((public.cars
     JOIN public.car_details ON ((cars.id = car_details.car_id)))
     JOIN public.models ON ((cars.model_id = models.id)))
     JOIN public.brands ON ((models.brand_id = brands.id)));


ALTER VIEW public.car_detail_view OWNER TO docker;

--
-- TOC entry 224 (class 1259 OID 16443)
-- Name: car_details_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.car_details_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.car_details_id_seq OWNER TO docker;

--
-- TOC entry 3487 (class 0 OID 0)
-- Dependencies: 224
-- Name: car_details_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.car_details_id_seq OWNED BY public.car_details.id;


--
-- TOC entry 230 (class 1259 OID 16487)
-- Name: car_tags; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.car_tags (
    car_id uuid NOT NULL,
    tag_id integer NOT NULL
);


ALTER TABLE public.car_tags OWNER TO docker;

--
-- TOC entry 232 (class 1259 OID 16506)
-- Name: cars_full_details; Type: VIEW; Schema: public; Owner: docker
--

CREATE VIEW public.cars_full_details AS
 SELECT cars.id,
    cars.model_id,
    cars.user_id,
    cars.title,
    cars.year,
    cars.price,
    cars.priority,
    cars.status,
    cars.is_active,
    cars.is_new,
    cars.added_at,
    car_details.mileage,
    car_details.fuel_type,
    car_details.engine_size,
    car_details.horsepower,
    car_details.transmission,
    car_details.color,
    car_details.description,
    brands.name AS brand_name,
    models.name AS model_name
   FROM (((public.cars
     JOIN public.car_details ON ((cars.id = car_details.car_id)))
     JOIN public.models ON ((cars.model_id = models.id)))
     JOIN public.brands ON ((models.brand_id = brands.id)));


ALTER VIEW public.cars_full_details OWNER TO docker;

--
-- TOC entry 233 (class 1259 OID 16511)
-- Name: cars_search_view; Type: VIEW; Schema: public; Owner: docker
--

CREATE VIEW public.cars_search_view AS
 SELECT cars.id,
    cars.model_id,
    cars.user_id,
    cars.title,
    cars.year,
    cars.price,
    cars.priority,
    cars.status,
    cars.is_active,
    cars.is_new,
    cars.added_at,
    brands.name AS brand_name,
    models.name AS model_name,
    car_details.mileage,
    car_details.horsepower AS hp
   FROM (((public.cars
     JOIN public.models ON ((cars.model_id = models.id)))
     JOIN public.brands ON ((models.brand_id = brands.id)))
     JOIN public.car_details ON ((cars.id = car_details.car_id)));


ALTER VIEW public.cars_search_view OWNER TO docker;

--
-- TOC entry 231 (class 1259 OID 16502)
-- Name: cars_with_model; Type: VIEW; Schema: public; Owner: docker
--

CREATE VIEW public.cars_with_model AS
 SELECT cars.id,
    cars.model_id,
    cars.user_id,
    cars.title,
    cars.year,
    cars.price,
    cars.priority,
    cars.status,
    cars.is_active,
    cars.is_new,
    cars.added_at,
    models.name AS model_name
   FROM (public.cars
     JOIN public.models ON ((cars.model_id = models.id)));


ALTER VIEW public.cars_with_model OWNER TO docker;

--
-- TOC entry 235 (class 1259 OID 16521)
-- Name: cars_with_tags; Type: VIEW; Schema: public; Owner: docker
--

CREATE VIEW public.cars_with_tags AS
SELECT
    NULL::uuid AS id,
    NULL::character varying(255) AS title,
    NULL::integer AS year,
    NULL::numeric(10,2) AS price,
    NULL::integer AS priority,
    NULL::character varying(50) AS status,
    NULL::boolean AS is_active,
    NULL::boolean AS is_new,
    NULL::character varying(100) AS brand_name,
    NULL::character varying(100) AS model_name,
    NULL::integer AS horsepower,
    NULL::integer AS mileage,
    NULL::text AS tags;


ALTER VIEW public.cars_with_tags OWNER TO docker;

--
-- TOC entry 227 (class 1259 OID 16463)
-- Name: images; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.images (
    id integer NOT NULL,
    car_id uuid NOT NULL,
    image_url text NOT NULL,
    alt_text character varying(255)
);


ALTER TABLE public.images OWNER TO docker;

--
-- TOC entry 226 (class 1259 OID 16462)
-- Name: images_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.images_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.images_id_seq OWNER TO docker;

--
-- TOC entry 3488 (class 0 OID 0)
-- Dependencies: 226
-- Name: images_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.images_id_seq OWNED BY public.images.id;


--
-- TOC entry 221 (class 1259 OID 16407)
-- Name: models_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.models_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.models_id_seq OWNER TO docker;

--
-- TOC entry 3489 (class 0 OID 0)
-- Dependencies: 221
-- Name: models_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.models_id_seq OWNED BY public.models.id;


--
-- TOC entry 229 (class 1259 OID 16477)
-- Name: tags; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.tags (
    id integer NOT NULL,
    name character varying(50) NOT NULL,
    description text
);


ALTER TABLE public.tags OWNER TO docker;

--
-- TOC entry 228 (class 1259 OID 16476)
-- Name: tags_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.tags_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tags_id_seq OWNER TO docker;

--
-- TOC entry 3490 (class 0 OID 0)
-- Dependencies: 228
-- Name: tags_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.tags_id_seq OWNED BY public.tags.id;


--
-- TOC entry 218 (class 1259 OID 16386)
-- Name: users; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.users (
    id integer NOT NULL,
    email character varying(255) NOT NULL,
    password text NOT NULL,
    role character varying(50) NOT NULL,
    session_token character varying(255),
    CONSTRAINT users_role_check CHECK (((role)::text = ANY ((ARRAY['admin'::character varying, 'user'::character varying, 'guest'::character varying])::text[])))
);


ALTER TABLE public.users OWNER TO docker;

--
-- TOC entry 217 (class 1259 OID 16385)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO docker;

--
-- TOC entry 3491 (class 0 OID 0)
-- Dependencies: 217
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 3265 (class 2604 OID 16401)
-- Name: brands id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.brands ALTER COLUMN id SET DEFAULT nextval('public.brands_id_seq'::regclass);


--
-- TOC entry 3274 (class 2604 OID 16447)
-- Name: car_details id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.car_details ALTER COLUMN id SET DEFAULT nextval('public.car_details_id_seq'::regclass);


--
-- TOC entry 3275 (class 2604 OID 16466)
-- Name: images id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.images ALTER COLUMN id SET DEFAULT nextval('public.images_id_seq'::regclass);


--
-- TOC entry 3267 (class 2604 OID 16411)
-- Name: models id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.models ALTER COLUMN id SET DEFAULT nextval('public.models_id_seq'::regclass);


--
-- TOC entry 3276 (class 2604 OID 16480)
-- Name: tags id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.tags ALTER COLUMN id SET DEFAULT nextval('public.tags_id_seq'::regclass);


--
-- TOC entry 3264 (class 2604 OID 16389)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 3469 (class 0 OID 16398)
-- Dependencies: 220
-- Data for Name: brands; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.brands (id, name, is_active) FROM stdin;
1	Toyota	t
2	Volkswagen	t
3	BMW	t
4	Ford	t
5	Kia	t
6	Mercedes-Benz	t
\.


--
-- TOC entry 3474 (class 0 OID 16444)
-- Dependencies: 225
-- Data for Name: car_details; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.car_details (id, car_id, mileage, fuel_type, engine_size, horsepower, transmission, color, description) FROM stdin;
1	22ed4542-871f-40cc-a713-07e29f6a9d47	24976	Gasoline	1.3	101	CVT	White	This 2011 Toyota Prius comes in a beautiful White color. It has a 1.3 L engine with 101 HP and CVT transmission. Fuel type: Gasoline. This car has 24976 kilometers on it and is in good condition.
2	0f1911e7-8c95-427d-b050-4075e77e03a4	19987	Hybrid	2.8	266	Manual	Gray	This 2010 Volkswagen Passat comes in a beautiful Gray color. It has a 2.8 L engine with 266 HP and Manual transmission. Fuel type: Hybrid. This car has 19987 kilometers on it and is in good condition.
3	18909a07-b307-49b7-aa83-f1a354c25814	102339	Diesel	3.2	214	CVT	Blue	This 2018 BMW X3 comes in a beautiful Blue color. It has a 3.2 L engine with 214 HP and CVT transmission. Fuel type: Diesel. This car has 102339 kilometers on it and is in good condition.
4	808b9af3-115f-4ae2-ba3a-b13db7dd01dd	136425	Diesel	2.0	148	Manual	Orange	This 2015 Ford Mondeo comes in a beautiful Orange color. It has a 2 L engine with 148 HP and Manual transmission. Fuel type: Diesel. This car has 136425 kilometers on it and is in good condition.
5	a94ec7ea-7cd3-4dd2-97d9-27970608408c	59697	Other	2.6	215	CVT	Blue	This 2010 Volkswagen Polo comes in a beautiful Blue color. It has a 2.6 L engine with 215 HP and CVT transmission. Fuel type: Other. This car has 59697 kilometers on it and is in good condition.
6	06de160a-ab5d-439b-9f03-c448537fac88	24358	Gasoline	4.6	303	Automatic	Blue	This 2023 Kia Niro comes in a beautiful Blue color. It has a 4.6 L engine with 303 HP and Automatic transmission. Fuel type: Gasoline. This car has 24358 kilometers on it and is in good condition.
7	b1c4007b-e8c4-4b1e-b955-882774a1d86f	183846	Other	3.6	262	CVT	Orange	This 2010 BMW i4 comes in a beautiful Orange color. It has a 3.6 L engine with 262 HP and CVT transmission. Fuel type: Other. This car has 183846 kilometers on it and is in good condition.
8	1eb64a4a-5527-435a-af17-c87c76f6deee	11697	Hybrid	4.7	347	CVT	Green	This 2021 Ford Mustang comes in a beautiful Green color. It has a 4.7 L engine with 347 HP and CVT transmission. Fuel type: Hybrid. This car has 11697 kilometers on it and is in good condition.
9	7cbb7c05-c0c1-4092-b0ac-8f63b641084f	46148	Diesel	2.7	243	Automatic	Gray	This 2021 Mercedes-Benz A-Class comes in a beautiful Gray color. It has a 2.7 L engine with 243 HP and Automatic transmission. Fuel type: Diesel. This car has 46148 kilometers on it and is in good condition.
10	6ee65826-5fb5-440b-86f9-a2d2788b9452	15346	Gasoline	2.4	192	Automatic	Orange	This 2019 Volkswagen ID.4 comes in a beautiful Orange color. It has a 2.4 L engine with 192 HP and Automatic transmission. Fuel type: Gasoline. This car has 15346 kilometers on it and is in good condition.
\.


--
-- TOC entry 3479 (class 0 OID 16487)
-- Dependencies: 230
-- Data for Name: car_tags; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.car_tags (car_id, tag_id) FROM stdin;
\.


--
-- TOC entry 3472 (class 0 OID 16422)
-- Dependencies: 223
-- Data for Name: cars; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.cars (id, model_id, user_id, title, year, price, priority, status, is_active, is_new, added_at) FROM stdin;
22ed4542-871f-40cc-a713-07e29f6a9d47	1	\N	2011 Toyota Prius White	2011	12053.00	3	available	t	f	2025-06-03 17:38:06.592917
0f1911e7-8c95-427d-b050-4075e77e03a4	2	\N	2010 Volkswagen Passat Gray	2010	15514.00	3	available	t	f	2025-06-03 17:38:06.60822
18909a07-b307-49b7-aa83-f1a354c25814	3	\N	2018 BMW X3 Blue	2018	48220.00	3	reserved	t	f	2025-06-03 17:38:06.618343
808b9af3-115f-4ae2-ba3a-b13db7dd01dd	4	\N	2015 Ford Mondeo Orange	2015	23121.00	2	reserved	t	f	2025-06-03 17:38:06.630662
a94ec7ea-7cd3-4dd2-97d9-27970608408c	5	\N	2010 Volkswagen Polo Blue	2010	13796.00	3	available	t	f	2025-06-03 17:38:06.637734
06de160a-ab5d-439b-9f03-c448537fac88	6	\N	2023 Kia Niro Blue	2023	31569.00	2	reserved	t	f	2025-06-03 17:38:06.647149
b1c4007b-e8c4-4b1e-b955-882774a1d86f	7	\N	2010 BMW i4 Orange	2010	29790.00	4	available	t	f	2025-06-03 17:38:06.652349
1eb64a4a-5527-435a-af17-c87c76f6deee	8	\N	2021 Ford Mustang Green	2021	27085.00	2	reserved	t	f	2025-06-03 17:38:06.656783
7cbb7c05-c0c1-4092-b0ac-8f63b641084f	9	\N	2021 Mercedes-Benz A-Class Gray	2021	55550.00	2	sold	t	f	2025-06-03 17:38:06.664812
6ee65826-5fb5-440b-86f9-a2d2788b9452	10	\N	2019 Volkswagen ID.4 Orange	2019	28200.00	3	available	t	f	2025-06-03 17:38:06.670035
\.


--
-- TOC entry 3476 (class 0 OID 16463)
-- Dependencies: 227
-- Data for Name: images; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.images (id, car_id, image_url, alt_text) FROM stdin;
\.


--
-- TOC entry 3471 (class 0 OID 16408)
-- Dependencies: 222
-- Data for Name: models; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.models (id, brand_id, name, is_active) FROM stdin;
1	1	Prius	t
2	2	Passat	t
3	3	X3	t
4	4	Mondeo	t
5	2	Polo	t
6	5	Niro	t
7	3	i4	t
8	4	Mustang	t
9	6	A-Class	t
10	2	ID.4	t
\.


--
-- TOC entry 3478 (class 0 OID 16477)
-- Dependencies: 229
-- Data for Name: tags; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.tags (id, name, description) FROM stdin;
1	sport	High performance sports cars
2	luxury	Luxury vehicles with premium features
3	electric	Electric vehicles
4	hybrid	Hybrid vehicles
5	suv	Sport Utility Vehicles
6	sedan	Sedan body type
7	compact	Compact cars
8	economy	Economical fuel-efficient cars
\.


--
-- TOC entry 3467 (class 0 OID 16386)
-- Dependencies: 218
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.users (id, email, password, role, session_token) FROM stdin;
1	test@test.com	$2y$10$rJy3gLcdgaYkkYKNkNdfkuKv36HdQcMEu9efmeMDLihR904n/xmKG	admin	683f32fa4a16b
\.


--
-- TOC entry 3492 (class 0 OID 0)
-- Dependencies: 219
-- Name: brands_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.brands_id_seq', 6, true);


--
-- TOC entry 3493 (class 0 OID 0)
-- Dependencies: 224
-- Name: car_details_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.car_details_id_seq', 10, true);


--
-- TOC entry 3494 (class 0 OID 0)
-- Dependencies: 226
-- Name: images_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.images_id_seq', 1, false);


--
-- TOC entry 3495 (class 0 OID 0)
-- Dependencies: 221
-- Name: models_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.models_id_seq', 10, true);


--
-- TOC entry 3496 (class 0 OID 0)
-- Dependencies: 228
-- Name: tags_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.tags_id_seq', 8, true);


--
-- TOC entry 3497 (class 0 OID 0)
-- Dependencies: 217
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.users_id_seq', 1, true);


--
-- TOC entry 3287 (class 2606 OID 16406)
-- Name: brands brands_name_key; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.brands
    ADD CONSTRAINT brands_name_key UNIQUE (name);


--
-- TOC entry 3289 (class 2606 OID 16404)
-- Name: brands brands_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.brands
    ADD CONSTRAINT brands_pkey PRIMARY KEY (id);


--
-- TOC entry 3297 (class 2606 OID 16454)
-- Name: car_details car_details_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.car_details
    ADD CONSTRAINT car_details_pkey PRIMARY KEY (id);


--
-- TOC entry 3307 (class 2606 OID 16491)
-- Name: car_tags car_tags_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.car_tags
    ADD CONSTRAINT car_tags_pkey PRIMARY KEY (car_id, tag_id);


--
-- TOC entry 3295 (class 2606 OID 16432)
-- Name: cars cars_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.cars
    ADD CONSTRAINT cars_pkey PRIMARY KEY (id);


--
-- TOC entry 3301 (class 2606 OID 16470)
-- Name: images images_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.images
    ADD CONSTRAINT images_pkey PRIMARY KEY (id);


--
-- TOC entry 3291 (class 2606 OID 16416)
-- Name: models models_brand_id_name_key; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.models
    ADD CONSTRAINT models_brand_id_name_key UNIQUE (brand_id, name);


--
-- TOC entry 3293 (class 2606 OID 16414)
-- Name: models models_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.models
    ADD CONSTRAINT models_pkey PRIMARY KEY (id);


--
-- TOC entry 3303 (class 2606 OID 16486)
-- Name: tags tags_name_key; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.tags
    ADD CONSTRAINT tags_name_key UNIQUE (name);


--
-- TOC entry 3305 (class 2606 OID 16484)
-- Name: tags tags_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.tags
    ADD CONSTRAINT tags_pkey PRIMARY KEY (id);


--
-- TOC entry 3299 (class 2606 OID 16461)
-- Name: car_details unique_car_id; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.car_details
    ADD CONSTRAINT unique_car_id UNIQUE (car_id);


--
-- TOC entry 3283 (class 2606 OID 16396)
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 3285 (class 2606 OID 16394)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 3465 (class 2618 OID 16524)
-- Name: cars_with_tags _RETURN; Type: RULE; Schema: public; Owner: docker
--

CREATE OR REPLACE VIEW public.cars_with_tags AS
 SELECT c.id,
    c.title,
    c.year,
    c.price,
    c.priority,
    c.status,
    c.is_active,
    c.is_new,
    b.name AS brand_name,
    m.name AS model_name,
    cd.horsepower,
    cd.mileage,
    string_agg((t.name)::text, ', '::text ORDER BY (t.name)::text) AS tags
   FROM (((((public.cars c
     JOIN public.models m ON ((c.model_id = m.id)))
     JOIN public.brands b ON ((m.brand_id = b.id)))
     LEFT JOIN public.car_details cd ON ((c.id = cd.car_id)))
     LEFT JOIN public.car_tags ct ON ((c.id = ct.car_id)))
     LEFT JOIN public.tags t ON ((ct.tag_id = t.id)))
  GROUP BY c.id, c.title, c.year, c.price, c.priority, c.status, c.is_active, c.is_new, b.name, m.name, cd.horsepower, cd.mileage
  ORDER BY c.priority DESC, c.added_at DESC;


--
-- TOC entry 3315 (class 2620 OID 16527)
-- Name: cars trigger_auto_priority; Type: TRIGGER; Schema: public; Owner: docker
--

CREATE TRIGGER trigger_auto_priority BEFORE INSERT OR UPDATE ON public.cars FOR EACH ROW EXECUTE FUNCTION public.auto_set_priority();


--
-- TOC entry 3311 (class 2606 OID 16455)
-- Name: car_details car_details_car_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.car_details
    ADD CONSTRAINT car_details_car_id_fkey FOREIGN KEY (car_id) REFERENCES public.cars(id) ON DELETE CASCADE;


--
-- TOC entry 3313 (class 2606 OID 16492)
-- Name: car_tags car_tags_car_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.car_tags
    ADD CONSTRAINT car_tags_car_id_fkey FOREIGN KEY (car_id) REFERENCES public.cars(id) ON DELETE CASCADE;


--
-- TOC entry 3314 (class 2606 OID 16497)
-- Name: car_tags car_tags_tag_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.car_tags
    ADD CONSTRAINT car_tags_tag_id_fkey FOREIGN KEY (tag_id) REFERENCES public.tags(id) ON DELETE CASCADE;


--
-- TOC entry 3309 (class 2606 OID 16433)
-- Name: cars cars_model_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.cars
    ADD CONSTRAINT cars_model_id_fkey FOREIGN KEY (model_id) REFERENCES public.models(id) ON DELETE CASCADE;


--
-- TOC entry 3310 (class 2606 OID 16438)
-- Name: cars cars_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.cars
    ADD CONSTRAINT cars_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 3312 (class 2606 OID 16471)
-- Name: images images_car_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.images
    ADD CONSTRAINT images_car_id_fkey FOREIGN KEY (car_id) REFERENCES public.cars(id) ON DELETE CASCADE;


--
-- TOC entry 3308 (class 2606 OID 16417)
-- Name: models models_brand_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.models
    ADD CONSTRAINT models_brand_id_fkey FOREIGN KEY (brand_id) REFERENCES public.brands(id) ON DELETE CASCADE;


-- Completed on 2025-06-03 18:19:57 UTC

--
-- PostgreSQL database dump complete
--

