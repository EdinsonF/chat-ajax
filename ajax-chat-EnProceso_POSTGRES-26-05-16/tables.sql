--
-- Table structure for table `webchat_lines`
--

CREATE TABLE `webchat_lines` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `author` varchar(16) NOT NULL,
  `gravatar` varchar(32) NOT NULL,
  `text` varchar(255) NOT NULL,
  `ts` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `ts` (`ts`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `webchat_users`
--

CREATE TABLE `webchat_users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(16) NOT NULL,
  `gravatar` varchar(32) NOT NULL,
  `last_activity` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `last_activity` (`last_activity`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



--------------------------   P O S T G R E S Q L ------------------------------

CREATE TABLE webchat_lines
(
  id bigserial NOT NULL,
  author character(1) NOT NULL,
  gravatar character(1) NOT NULL,
  text character(1) NOT NULL,
  ts timestamp without time zone NOT NULL DEFAULT now(),
  CONSTRAINT webchat_lines_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE webchat_lines
  OWNER TO postgres;
-- --------------------------------------------------------

--
-- Table structure for table `webchat_users`
--

CREATE TABLE webchat_users
(
  id bigserial NOT NULL,
  name character varying NOT NULL,
  gravatar character varying NOT NULL,
  last_activity timestamp without time zone NOT NULL DEFAULT now(),
  CONSTRAINT webchat_users_pkey PRIMARY KEY (id),
  CONSTRAINT webchat_users_name_key UNIQUE (name)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE webchat_users
  OWNER TO postgres;



  --------------------------------TABLES NEW ------>>>>>

  CREATE TABLE usuario
(
  id_usuario bigserial NOT NULL,
  usuario character varying NOT NULL,
  contrasena character varying NOT NULL,
  estatus character varying NOT NULL,
  pregunta character varying,
  respuesta character varying,
  CONSTRAINT pk_usuario PRIMARY KEY (id_usuario),
  CONSTRAINT uk_usuario UNIQUE (usuario)

);

CREATE TABLE grupo(
codigo_grupo bigserial NOT NULL primary key,
nombre_grupo character varying NOT NULL,
estatus character varying,
fecha_creacion timestamp without time zone NOT NULL DEFAULT now()

);


CREATE TABLE inter_usuario_grupo(

  id_inter_usuario_grupo bigserial NOT NULL primary key,
  codigo_grupo int NOT NULL,
  id_usuario_add int NOT NULL,
  usuario_patrocinador int NOT NULL,
  fecha_ingreso timestamp without time zone NOT NULL DEFAULT now(),
  estatus character varying,
  CONSTRAINT fk_id_usuarioAgregado FOREIGN KEY (id_usuario_add) 
      REFERENCES usuario (id_usuario) MATCH FULL
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_id_usuarioAgregador FOREIGN KEY (usuario_patrocinador) 
      REFERENCES usuario (id_usuario) MATCH FULL
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_codigoGrupo FOREIGN KEY (codigo_grupo) 
      REFERENCES grupo (codigo_grupo) MATCH FULL
      ON UPDATE CASCADE ON DELETE CASCADE

);



CREATE TABLE conversacion(
  codigo_conversacion bigserial NOT NULL PRIMARY key,
  id_inter_usuario_grupo int NOT NULL,
  mensaje_texto character varying NOT NULL,
  fecha_envio timestamp without time zone NOT NULL DEFAULT now(), 
  estatus character varying,
  CONSTRAINT fk_intermedia FOREIGN KEY (id_inter_usuario_grupo)
      REFERENCES inter_usuario_grupo (id_inter_usuario_grupo) MATCH FULL
      ON UPDATE CASCADE ON DELETE CASCADE
    
);