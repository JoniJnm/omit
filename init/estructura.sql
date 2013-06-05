DROP TABLE IF EXISTS `uni_asignaturas`;
CREATE TABLE IF NOT EXISTS `uni_asignaturas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `curso` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `uni_cursos`;
CREATE TABLE IF NOT EXISTS `uni_cursos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `titulacion` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `uni_preguntas`;
CREATE TABLE IF NOT EXISTS `uni_preguntas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pregunta` varchar(255) NOT NULL,
  `profesor` int(11) NOT NULL,
  `asignatura` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `uni_preguntas_default`;
CREATE TABLE IF NOT EXISTS `uni_preguntas_default` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pregunta` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `uni_titulaciones`;
CREATE TABLE IF NOT EXISTS `uni_titulaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `uni_usuarios`;
CREATE TABLE IF NOT EXISTS `uni_usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(125) NOT NULL,
  `email` varchar(125) NOT NULL,
  `apellido1` varchar(125) NOT NULL,
  `apellido2` varchar(125) NOT NULL,
  `pass` varchar(60) NOT NULL,
  `type` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `uni_usuarios_asignaturas`;
CREATE TABLE IF NOT EXISTS `uni_usuarios_asignaturas` (
  `usuario` int(11) NOT NULL,
  `asignatura` int(11) NOT NULL,
  UNIQUE KEY `profesor` (`usuario`,`asignatura`)
) DEFAULT CHARSET=utf8;
