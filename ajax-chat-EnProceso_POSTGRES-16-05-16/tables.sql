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