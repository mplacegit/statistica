--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: widget_day_summary; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE widget_day_summary (
    day date,
    pages_cnt integer,
    request_sum integer,
    max_loaded numeric,
    avg_loaded numeric
);


ALTER TABLE widget_day_summary OWNER TO postgres;

--
-- Name: widget_requests; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE widget_requests (
    day date,
    id_server integer,
    datetime timestamp without time zone,
    hash character varying(32),
    request character varying(1000),
    nosearch smallint DEFAULT 0,
    url character varying DEFAULT 1000,
    loaded numeric(18,4) DEFAULT 0
);


ALTER TABLE widget_requests OWNER TO postgres;

--
-- Name: widget_requests_loaded; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE widget_requests_loaded (
    day date,
    server_id integer,
    ltd character varying(255),
    user_id integer,
    user_name character varying(255),
    cnt integer,
    avg_loaded numeric(18,4),
    max_loaded numeric(18,4)
);


ALTER TABLE widget_requests_loaded OWNER TO postgres;

--
-- Name: widget_requests_server_loaded; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE widget_requests_server_loaded (
    day date,
    server_id integer,
    hash character varying(32),
    cnt integer,
    avg_loaded numeric(18,2),
    max_loaded numeric(18,2),
    url character varying(1000),
    request character varying(1000),
    last_loaded timestamp without time zone,
    datetime_max_last timestamp without time zone
);


ALTER TABLE widget_requests_server_loaded OWNER TO postgres;

--
-- Name: widget_requests_loaded_day_server_uniq; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX widget_requests_loaded_day_server_uniq ON widget_requests_loaded USING btree (day, server_id);


--
-- Name: widget_requests_server_loaded_uniq; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX widget_requests_server_loaded_uniq ON widget_requests_server_loaded USING btree (day, server_id, hash);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

