INSERT INTO `uni_asignaturas` (`id`, `nombre`, `curso`) VALUES
(1, 'Informática', 1),
(2, 'Paradigmas de la programación', 1),
(3, 'Java', 2),
(4, 'Sistemas de información', 2),
(5, 'Filosofía', 3),
(6, 'Ética', 3),
(7, 'Audiovisuales', 4),
(8, 'Objetividad', 4);

INSERT INTO `uni_cursos` (`id`, `nombre`, `titulacion`) VALUES
(1, 'Primero (mañana)', 1),
(2, 'Segundo (tarde)', 1),
(3, 'Tercero (tarde)', 2),
(4, 'Cuarto', 2);

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

INSERT INTO `uni_profesores_asignaturas` (`profesor`, `asignatura`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8);

INSERT INTO `uni_titulaciones` (`id`, `nombre`) VALUES
(1, 'Ingeniería del Software'),
(2, 'Periodismo');

INSERT INTO `uni_usuarios` (`id`, `name`, `username`, `pass`, `type`) VALUES
(1, 'Pedro', 'pedro', '09aba14b14c1ddb10346068577d21b6b', 1),
(2, 'Soto', 'soto', '09aba14b14c1ddb10346068577d21b6b', 1),
(3, 'Enrique', 'enrique', '09aba14b14c1ddb10346068577d21b6b', 1),
(4, 'Marta', 'marta', '09aba14b14c1ddb10346068577d21b6b', 1),
(5, 'María', 'maria', '09aba14b14c1ddb10346068577d21b6b', 1),
(6, 'Eva', 'eva', '09aba14b14c1ddb10346068577d21b6b', 1),
(7, 'Antonio', 'antonio', '09aba14b14c1ddb10346068577d21b6b', 1),
(8, 'Tomás', 'tomas', '09aba14b14c1ddb10346068577d21b6b', 1),
(9, 'Administrador', 'admin', '09aba14b14c1ddb10346068577d21b6b', 2),
(10, 'Jónatan', 'joni', '09aba14b14c1ddb10346068577d21b6b', 0);
