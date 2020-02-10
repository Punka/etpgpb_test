CREATE TABLE public.item
(
    id SERIAL,
    "position" character varying(255) COLLATE pg_catalog."default" NOT NULL,
    title character varying(255) COLLATE pg_catalog."default" NOT NULL,
    value double precision DEFAULT 0,
    parent_id integer DEFAULT 0,
    CONSTRAINT item_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.item
    OWNER to postgres;
