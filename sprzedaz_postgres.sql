

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 211 (class 1255 OID 16441)
-- Name: audit(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.audit() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    BEGIN
		IF (TG_OP = 'UPDATE') THEN
			IF ( NEW.product_count > 10 ) THEN
				INSERT INTO audit(date,description,client_id) 
					VALUES(now(),'Uzupełniono ilość towaru większą niz 10',NEW.client_id);
				RETURN NEW;
			END IF;
        END IF;
		IF (TG_OP = 'INSERT') THEN
			IF ( NEW.product_count > 10 ) THEN
				INSERT INTO audit(date,description,client_id) 
					VALUES(now(),'Dodano ilość towaru wiekszą niż 10',NEW.client_id);
				RETURN NEW;
			END IF;
        END IF;
        RETURN NULL;
    END;
$$;


ALTER FUNCTION public.audit() OWNER TO postgres;

--
-- TOC entry 210 (class 1255 OID 16423)
-- Name: get_users_count(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.get_users_count() RETURNS integer
    LANGUAGE plpgsql
    AS $$
declare
   users_count integer;
begin
   select count(*) 
   into users_count
   from users;   
   return users_count;
end;
$$;


ALTER FUNCTION public.get_users_count() OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 206 (class 1259 OID 16424)
-- Name: audit; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.audit (
    id integer NOT NULL,
    date date NOT NULL,
    description text NOT NULL,
    client_id integer NOT NULL
);


ALTER TABLE public.audit OWNER TO postgres;

--
-- TOC entry 207 (class 1259 OID 16432)
-- Name: audit_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.audit ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.audit_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- TOC entry 201 (class 1259 OID 16403)
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id integer NOT NULL,
    login text,
    name text,
    surname text,
    birth_date date,
    password text,
    email text,
    address text,
    education text,
    interests text,
    status text
);


ALTER TABLE public.users OWNER TO postgres;

--
-- TOC entry 208 (class 1259 OID 16437)
-- Name: audit_view; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.audit_view AS
 SELECT audit.date,
    audit.description,
    users.login,
    users.email
   FROM (public.audit
     LEFT JOIN public.users ON ((audit.client_id = users.id)));


ALTER TABLE public.audit_view OWNER TO postgres;

--
-- TOC entry 202 (class 1259 OID 16411)
-- Name: cart; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cart (
    id integer NOT NULL,
    client_id integer NOT NULL,
    product_id integer NOT NULL,
    product_count integer NOT NULL,
    requested integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.cart OWNER TO postgres;

--
-- TOC entry 204 (class 1259 OID 16418)
-- Name: cart_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.cart ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.cart_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- TOC entry 200 (class 1259 OID 16395)
-- Name: products; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.products (
    id integer NOT NULL,
    name text,
    model text,
    description text,
    price double precision NOT NULL
);


ALTER TABLE public.products OWNER TO postgres;

--
-- TOC entry 209 (class 1259 OID 16449)
-- Name: orders_view; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.orders_view AS
 SELECT users.login,
    users.email,
    products.name,
    cart.product_count,
    (products.price * (cart.product_count)::double precision) AS value,
    cart.requested
   FROM ((public.cart
     JOIN public.users ON ((cart.client_id = users.id)))
     JOIN public.products ON ((cart.product_id = products.id)));


ALTER TABLE public.orders_view OWNER TO postgres;

--
-- TOC entry 203 (class 1259 OID 16416)
-- Name: products_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.products ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.products_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- TOC entry 205 (class 1259 OID 16420)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.users ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- TOC entry 3029 (class 0 OID 16424)
-- Dependencies: 206
-- Data for Name: audit; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.audit (id, date, description, client_id) FROM stdin;
\.


--
-- TOC entry 3025 (class 0 OID 16411)
-- Dependencies: 202
-- Data for Name: cart; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cart (id, client_id, product_id, product_count, requested) FROM stdin;
\.


--
-- TOC entry 3023 (class 0 OID 16395)
-- Dependencies: 200
-- Data for Name: products; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.products (id, name, model, description, price) FROM stdin;
\.


--
-- TOC entry 3024 (class 0 OID 16403)
-- Dependencies: 201
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, login, name, surname, birth_date, password, email, address, education, interests, status) FROM stdin;
\.


--
-- TOC entry 3036 (class 0 OID 0)
-- Dependencies: 207
-- Name: audit_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.audit_id_seq', 30, true);


--
-- TOC entry 3037 (class 0 OID 0)
-- Dependencies: 204
-- Name: cart_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.cart_id_seq', 57, true);


--
-- TOC entry 3038 (class 0 OID 0)
-- Dependencies: 203
-- Name: products_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.products_id_seq', 7, true);


--
-- TOC entry 3039 (class 0 OID 0)
-- Dependencies: 205
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 10, true);


--
-- TOC entry 2889 (class 2606 OID 16428)
-- Name: audit audit_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.audit
    ADD CONSTRAINT audit_pkey PRIMARY KEY (id);


--
-- TOC entry 2887 (class 2606 OID 16415)
-- Name: cart cart_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cart
    ADD CONSTRAINT cart_pkey PRIMARY KEY (id);


--
-- TOC entry 2883 (class 2606 OID 16402)
-- Name: products order_details_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT order_details_pk PRIMARY KEY (id);


--
-- TOC entry 2885 (class 2606 OID 16410)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 2890 (class 2620 OID 16442)
-- Name: cart taudit; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER taudit AFTER INSERT OR DELETE OR UPDATE ON public.cart FOR EACH ROW EXECUTE FUNCTION public.audit();


-- Completed on 2021-10-01 18:52:51

--
-- PostgreSQL database dump complete
--

