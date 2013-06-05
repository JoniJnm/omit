TRUNCATE TABLE `uni_asignaturas`;
INSERT INTO `uni_asignaturas` (`id`, `nombre`, `curso`) VALUES
(1, 'Informática', 1),
(2, 'Paradigmas de la programación', 1),
(3, 'Java', 2),
(4, 'Sistemas de información', 2),
(5, 'Filosofía', 3),
(6, 'Ética', 3),
(7, 'Audiovisuales', 4),
(8, 'Objetividad', 4);

TRUNCATE TABLE `uni_cursos`;
INSERT INTO `uni_cursos` (`id`, `nombre`, `titulacion`) VALUES
(1, 'Primero (mañana)', 1),
(2, 'Segundo (tarde)', 1),
(3, 'Tercero (tarde)', 2),
(4, 'Cuarto', 2);

TRUNCATE TABLE `uni_preguntas_default` ;
INSERT INTO `uni_preguntas_default` (`id`, `pregunta`) VALUES
(1, 'El profesor da a conocer a los alumnos la guía docente de la asignatura a principios de curso.'),
(2, 'El profesor ha informado claramente sobre los criterios de evaluación de la asignatura.'),
(3, 'El profesor ha establecido algún sistema de comunicación y tutoria.'),
(4, 'El profesor está disponible para atender a los alumnos.'),
(5, 'El profesor aclara adecuadamente  las dudas de las distintas actividades propuestas en la asignatura.'),
(6, 'El profesor utiliza un material (texto, presentaciones, vídeos, videoconferencias, ...) que facilita el parendizaje de la asignatura.'),
(7, 'Las actividades docentes se ajustan a los objetivos, contenidos y metodología especificada en la guía docente de la asignatura.'),
(8, 'El desarrollo de la asignatura me permite un seguimiento y aprendizaje adecuados.'),
(9, 'Teniendo en cuena todos los apectos mencionados, estoy satisfecho/a con la labor que desarrolla el prodesor.');

TRUNCATE TABLE `uni_titulaciones`;
INSERT INTO `uni_titulaciones` (`id`, `nombre`) VALUES
(1, 'Ingeniería del Software'),
(2, 'Periodismo');

TRUNCATE TABLE `uni_usuarios`;
INSERT INTO `uni_usuarios` (`id`, `nombre`, `email`, `apellido1`, `apellido2`, `pass`, `type`) VALUES
(1, 'Soto', 'soto', '', '', '09aba14b14c1ddb10346068577d21b6b', 1),
(2, 'Pedro', 'pedro', '', '', '09aba14b14c1ddb10346068577d21b6b', 1),
(3, 'Enrique', 'enrique', '', '', '09aba14b14c1ddb10346068577d21b6b', 1),
(4, 'Marta', 'marta', '', '', '09aba14b14c1ddb10346068577d21b6b', 1),
(5, 'María', 'maria', '', '', '09aba14b14c1ddb10346068577d21b6b', 1),
(6, 'Eva', 'eva@gmail.com', '', '', '09aba14b14c1ddb10346068577d21b6b', 1),
(7, 'Antonio', 'antonio', '', '', '09aba14b14c1ddb10346068577d21b6b', 1),
(8, 'Tomás', 'tomas', '', '', '09aba14b14c1ddb10346068577d21b6b', 1),
(9, 'Administrador', 'admin', '', '', '09aba14b14c1ddb10346068577d21b6b', 2),
(10, 'Jónatan', 'joni', '', '', '09aba14b14c1ddb10346068577d21b6b', 0),
(11, 'usuario2', 'usuario2', '', '', '09aba14b14c1ddb10346068577d21b6b', 0),
(18, 'JONATAN', 'rtnex@hotmail.com', 'NUÑEZ', 'MARTIN', '55a797f1d07a39582cbdb0d7128c1e13', 0);

TRUNCATE TABLE `uni_usuarios_asignaturas`;
INSERT INTO `uni_usuarios_asignaturas` (`usuario`, `asignatura`) VALUES
(1, 1),
(2, 1),
(2, 2),
(2, 3),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(10, 1),
(10, 2),
(10, 3),
(10, 4),
(10, 6),
(11, 5),
(11, 7),
(11, 8),
(14, 1),
(14, 2),
(17, 1),
(18, 1);
