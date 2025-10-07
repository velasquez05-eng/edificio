-- ========================================================
-- MANTENER ADMINISTRADOR EXISTENTE
-- ========================================================

-- El administrador ya existe según el script original

-- ========================================================
-- CREAR ROLES ADICIONALES
-- ========================================================

INSERT INTO roles (nombre_rol, descripcion) VALUES
('Mantenimiento', 'Personal encargado de mantenimiento de edificios'),
('Limpieza', 'Personal encargado de limpieza de áreas comunes');

-- ========================================================
-- CREAR 10 PERSONALES (3 MANTENIMIENTO + 8 LIMPIEZA)
-- ========================================================

-- Personas para personal
INSERT INTO persona (nombre, appaterno, apmaterno, fecha_naci, ci, telefono, email) VALUES
-- Personal de Mantenimiento (3)
('Carlos', 'Mendez', 'Rojas', '1985-03-15', '10000001', '70000001', 'carlos.mendez@gmail.com'),
('Javier', 'Lopez', 'Silva', '1990-07-22', '10000002', '70000002', 'javier.lopez@gmail.com'),
('Roberto', 'Garcia', 'Vargas', '1988-11-30', '10000003', '70000003', 'roberto.garcia@gmail.com'),

-- Personal de Limpieza (8)
('Maria', 'Fernandez', 'Lopez', '1992-04-18', '10000004', '70000004', 'maria.fernandez@gmail.com'),
('Ana', 'Martinez', 'Gomez', '1995-09-12', '10000005', '70000005', 'ana.martinez@gmail.com'),
('Luisa', 'Rodriguez', 'Castro', '1993-12-05', '10000006', '70000006', 'luisa.rodriguez@gmail.com'),
('Carmen', 'Diaz', 'Perez', '1987-06-25', '10000007', '70000007', 'carmen.diaz@gmail.com'),
('Elena', 'Sanchez', 'Ruiz', '1991-02-14', '10000008', '70000008', 'elena.sanchez@gmail.com'),
('Rosa', 'Alvarez', 'Moreno', '1994-08-08', '10000009', '70000009', 'rosa.alvarez@gmail.com'),
('Teresa', 'Romero', 'Navarro', '1989-10-20', '10000010', '70000010', 'teresa.romero@gmail.com'),
('Patricia', 'Torres', 'Jimenez', '1996-01-30', '10000011', '70000011', 'patricia.torres@gmail.com');

-- Personal de Mantenimiento
INSERT INTO personal (cargo, username, password_hash, fecha_contratacion, id_persona, id_rol) VALUES
('Técnico Mantenimiento', 'cmendez', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', '2020-01-15', 2, 2),
('Supervisor Mantenimiento', 'jlopez', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', '2019-08-10', 3, 2),
('Jefe Mantenimiento', 'rgarcia', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', '2018-03-22', 4, 2);

-- Personal de Limpieza
INSERT INTO personal (cargo, username, password_hash, fecha_contratacion, id_persona, id_rol) VALUES
('Supervisora Limpieza', 'mfernandez', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', '2020-02-01', 5, 3),
('Limpieza Turno Mañana', 'amartinez', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', '2020-02-01', 6, 3),
('Limpieza Turno Tarde', 'lrodriguez', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', '2020-02-01', 7, 3),
('Limpieza Áreas Comunes', 'cdiaz', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', '2020-02-01', 8, 3),
('Limpieza Turno Noche', 'esanchez', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', '2020-02-01', 9, 3),
('Auxiliar Limpieza', 'ralvarez', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', '2020-02-01', 10, 3),
('Limpieza Especial', 'tromero', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', '2020-02-01', 11, 3),
('Coordinadora Limpieza', 'ptorres', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', '2020-02-01', 12, 3);

-- ========================================================
-- CREAR 50 USUARIOS/RESIDENTES
-- ========================================================

-- Insertar 50 personas
INSERT INTO persona (nombre, appaterno, apmaterno, fecha_naci, ci, telefono, email) VALUES
('Juan', 'Perez', 'Gonzales', '1990-05-15', '20000001', '60000001', 'juan.perez@gmail.com'),
('Maria', 'Lopez', 'Martinez', '1985-08-20', '20000002', '60000002', 'maria.lopez@gmail.com'),
('Carlos', 'Garcia', 'Rodriguez', '1992-03-10', '20000003', '60000003', 'carlos.garcia@gmail.com'),
('Ana', 'Martinez', 'Silva', '1988-11-25', '20000004', '60000004', 'ana.martinez@gmail.com'),
('Luis', 'Fernandez', 'Diaz', '1991-07-12', '20000005', '60000005', 'luis.fernandez@gmail.com'),
('Elena', 'Ramirez', 'Castro', '1987-09-30', '20000006', '60000006', 'elena.ramirez@gmail.com'),
('Pedro', 'Sanchez', 'Vargas', '1993-12-05', '20000007', '60000007', 'pedro.sanchez@gmail.com'),
('Laura', 'Torres', 'Mendez', '1989-04-18', '20000008', '60000008', 'laura.torres@gmail.com'),
('Roberto', 'Castro', 'Rojas', '1994-06-22', '20000009', '60000009', 'roberto.castro@gmail.com'),
('Carmen', 'Ortega', 'Navarro', '1986-01-14', '20000010', '60000010', 'carmen.ortega@gmail.com'),
('Jorge', 'Mendoza', 'Paredes', '1990-10-08', '20000011', '60000011', 'jorge.mendoza@gmail.com'),
('Sofia', 'Rios', 'Quiroga', '1995-02-28', '20000012', '60000012', 'sofia.rios@gmail.com'),
('Miguel', 'Chavez', 'Salazar', '1988-07-17', '20000013', '60000013', 'miguel.chavez@gmail.com'),
('Isabel', 'Flores', 'Zambrana', '1992-11-03', '20000014', '60000014', 'isabel.flores@gmail.com'),
('Fernando', 'Aguilar', 'Velasco', '1987-05-19', '20000015', '60000015', 'fernando.aguilar@gmail.com'),
('Gabriela', 'Paredes', 'Yujra', '1993-09-14', '20000016', '60000016', 'gabriela.paredes@gmail.com'),
('Ricardo', 'Quispe', 'Ticona', '1991-12-25', '20000017', '60000017', 'ricardo.quispe@gmail.com'),
('Daniela', 'Zambrana', 'Arce', '1989-08-07', '20000018', '60000018', 'daniela.zambrana@gmail.com'),
('Oscar', 'Velasco', 'Burgoa', '1994-04-11', '20000019', '60000019', 'oscar.velasco@gmail.com'),
('Natalia', 'Yujra', 'Callisaya', '1990-06-29', '20000020', '60000020', 'natalia.yujra@gmail.com'),
('Hector', 'Ticona', 'Choque', '1986-03-08', '20000021', '60000021', 'hector.ticona@gmail.com'),
('Valeria', 'Arce', 'Espinoza', '1992-10-16', '20000022', '60000022', 'valeria.arce@gmail.com'),
('Sergio', 'Burgoa', 'Fuentes', '1988-01-23', '20000023', '60000023', 'sergio.burgoa@gmail.com'),
('Camila', 'Callisaya', 'Gutierrez', '1995-07-04', '20000024', '60000024', 'camila.callisaya@gmail.com'),
('Andres', 'Choque', 'Huanca', '1991-11-12', '20000025', '60000025', 'andres.choque@gmail.com'),
('Lucia', 'Espinoza', 'Ibanez', '1987-02-19', '20000026', '60000026', 'lucia.espinoza@gmail.com'),
('Raul', 'Fuentes', 'Jaldin', '1993-05-27', '20000027', '60000027', 'raul.fuentes@gmail.com'),
('Monica', 'Gutierrez', 'Llanos', '1989-09-06', '20000028', '60000028', 'monica.gutierrez@gmail.com'),
('Pablo', 'Huanca', 'Mamani', '1994-12-15', '20000029', '60000029', 'pablo.huanca@gmail.com'),
('Veronica', 'Ibanez', 'Nina', '1990-04-02', '20000030', '60000030', 'veronica.ibanez@gmail.com'),
('Gustavo', 'Jaldin', 'Orellana', '1986-08-21', '20000031', '60000031', 'gustavo.jaldin@gmail.com'),
('Adriana', 'Llanos', 'Pacheco', '1992-01-09', '20000032', '60000032', 'adriana.llanos@gmail.com'),
('Mario', 'Mamani', 'Quenta', '1988-06-13', '20000033', '60000033', 'mario.mamani@gmail.com'),
('Claudia', 'Nina', 'Ramos', '1995-03-26', '20000034', '60000034', 'claudia.nina@gmail.com'),
('Javier', 'Orellana', 'Soria', '1991-10-31', '20000035', '60000035', 'javier.orellana@gmail.com'),
('Rocio', 'Pacheco', 'Tapia', '1987-07-18', '20000036', '60000036', 'rocio.pacheco@gmail.com'),
('Alberto', 'Quenta', 'Urquizo', '1993-02-24', '20000037', '60000037', 'alberto.quenta@gmail.com'),
('Silvia', 'Ramos', 'Villarroel', '1989-05-11', '20000038', '60000038', 'silvia.ramos@gmail.com'),
('Victor', 'Soria', 'Yucra', '1994-08-28', '20000039', '60000039', 'victor.soria@gmail.com'),
('Carolina', 'Tapia', 'Zegarra', '1990-12-07', '20000040', '60000040', 'carolina.tapia@gmail.com'),
('Felipe', 'Urquizo', 'Alvarez', '1986-09-22', '20000041', '60000041', 'felipe.urquizo@gmail.com'),
('Diana', 'Villarroel', 'Barrios', '1992-04-05', '20000042', '60000042', 'diana.villarroel@gmail.com'),
('Hugo', 'Yucra', 'Cabrera', '1988-11-16', '20000043', '60000043', 'hugo.yucra@gmail.com'),
('Paola', 'Zegarra', 'Duran', '1995-06-09', '20000044', '60000044', 'paola.zegarra@gmail.com'),
('Ramon', 'Alvarez', 'Escobar', '1991-03-01', '20000045', '60000045', 'ramon.alvarez@gmail.com'),
('Tatiana', 'Barrios', 'Franco', '1987-10-14', '20000046', '60000046', 'tatiana.barrios@gmail.com'),
('Walter', 'Cabrera', 'Guzman', '1993-01-27', '20000047', '60000047', 'walter.cabrera@gmail.com'),
('Ximena', 'Duran', 'Herrera', '1989-08-03', '20000048', '60000048', 'ximena.duran@gmail.com'),
('Yamil', 'Escobar', 'Iriarte', '1994-05-20', '20000049', '60000049', 'yamil.escobar@gmail.com'),
('Zoe', 'Franco', 'Jaimes', '1990-02-13', '20000050', '60000050', 'zoe.franco@gmail.com');

-- Insertar 50 usuarios
INSERT INTO usuario (username, password_hash, id_persona) VALUES
('jperez', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 13),
('mlopez', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 14),
('cgarcia', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 15),
('amartinez', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 16),
('lfernandez', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 17),
('eramirez', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 18),
('psanchez', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 19),
('ltorres', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 20),
('rcastro', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 21),
('cortega', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 22),
('jmendoza', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 23),
('srios', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 24),
('mchavez', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 25),
('iflores', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 26),
('faguilar', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 27),
('gparedes', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 28),
('rquispe', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 29),
('dzambrana', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 30),
('ovelasco', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 31),
('nyujra', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 32),
('hticona', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 33),
('varce', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 34),
('sburgoa', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 35),
('ccallisaya', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 36),
('achoque', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 37),
('lespinoza', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 38),
('rfuentes', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 39),
('mgutierrez', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 40),
('phuanca', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 41),
('vibanez', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 42),
('gjaldin', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 43),
('allanos', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 44),
('mmamani', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 45),
('cnina', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 46),
('jorellana', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 47),
('rpacheco', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 48),
('aquenta', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 49),
('sramos', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 50),
('vsoria', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 51),
('ctapia', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 52),
('furquizo', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 53),
('dvillarroel', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 54),
('hyucra', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 55),
('pzegarra', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 56),
('ralvarez', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 57),
('tbarrios', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 58),
('wcabrera', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 59),
('xduran', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 60),
('yescobar', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 61),
('zfranco', '$2y$10$rntHQpg.VgUTEkmA0W9jhebtI5OllyXuiUCTw/sLeMBnUOF/O88Wu', 62);

-- ========================================================
-- CREAR 50 DEPARTAMENTOS (25 PISOS, 2 DEPARTAMENTOS POR PISO)
-- ========================================================

-- Insertar 50 departamentos
INSERT INTO departamento (numero, piso, id_edificio) VALUES
('101', 1, 1), ('102', 1, 1),
('201', 2, 1), ('202', 2, 1),
('301', 3, 1), ('302', 3, 1),
('401', 4, 1), ('402', 4, 1),
('501', 5, 1), ('502', 5, 1),
('601', 6, 1), ('602', 6, 1),
('701', 7, 1), ('702', 7, 1),
('801', 8, 1), ('802', 8, 1),
('901', 9, 1), ('902', 9, 1),
('1001', 10, 1), ('1002', 10, 1),
('1101', 11, 1), ('1102', 11, 1),
('1201', 12, 1), ('1202', 12, 1),
('1301', 13, 1), ('1302', 13, 1),
('1401', 14, 1), ('1402', 14, 1),
('1501', 15, 1), ('1502', 15, 1),
('1601', 16, 1), ('1602', 16, 1),
('1701', 17, 1), ('1702', 17, 1),
('1801', 18, 1), ('1802', 18, 1),
('1901', 19, 1), ('1902', 19, 1),
('2001', 20, 1), ('2002', 20, 1),
('2101', 21, 1), ('2102', 21, 1),
('2201', 22, 1), ('2202', 22, 1),
('2301', 23, 1), ('2302', 23, 1),
('2401', 24, 1), ('2402', 24, 1),
('2501', 25, 1), ('2502', 25, 1);

-- ========================================================
-- ASIGNAR USUARIOS A DEPARTAMENTOS
-- ========================================================

INSERT INTO pertenece_dep (id_usuario, id_departamento, fecha_inicio, fecha_fin, estado) VALUES
(1, 1, '2020-01-01', NULL, 'activo'),
(2, 2, '2020-01-01', NULL, 'activo'),
(3, 3, '2020-01-01', NULL, 'activo'),
(4, 4, '2020-01-01', NULL, 'activo'),
(5, 5, '2020-01-01', NULL, 'activo'),
(6, 6, '2020-01-01', NULL, 'activo'),
(7, 7, '2020-01-01', NULL, 'activo'),
(8, 8, '2020-01-01', NULL, 'activo'),
(9, 9, '2020-01-01', NULL, 'activo'),
(10, 10, '2020-01-01', NULL, 'activo'),
(11, 11, '2020-01-01', NULL, 'activo'),
(12, 12, '2020-01-01', NULL, 'activo'),
(13, 13, '2020-01-01', NULL, 'activo'),
(14, 14, '2020-01-01', NULL, 'activo'),
(15, 15, '2020-01-01', NULL, 'activo'),
(16, 16, '2020-01-01', NULL, 'activo'),
(17, 17, '2020-01-01', NULL, 'activo'),
(18, 18, '2020-01-01', NULL, 'activo'),
(19, 19, '2020-01-01', NULL, 'activo'),
(20, 20, '2020-01-01', NULL, 'activo'),
(21, 21, '2020-01-01', NULL, 'activo'),
(22, 22, '2020-01-01', NULL, 'activo'),
(23, 23, '2020-01-01', NULL, 'activo'),
(24, 24, '2020-01-01', NULL, 'activo'),
(25, 25, '2020-01-01', NULL, 'activo'),
(26, 26, '2020-01-01', NULL, 'activo'),
(27, 27, '2020-01-01', NULL, 'activo'),
(28, 28, '2020-01-01', NULL, 'activo'),
(29, 29, '2020-01-01', NULL, 'activo'),
(30, 30, '2020-01-01', NULL, 'activo'),
(31, 31, '2020-01-01', NULL, 'activo'),
(32, 32, '2020-01-01', NULL, 'activo'),
(33, 33, '2020-01-01', NULL, 'activo'),
(34, 34, '2020-01-01', NULL, 'activo'),
(35, 35, '2020-01-01', NULL, 'activo'),
(36, 36, '2020-01-01', NULL, 'activo'),
(37, 37, '2020-01-01', NULL, 'activo'),
(38, 38, '2020-01-01', NULL, 'activo'),
(39, 39, '2020-01-01', NULL, 'activo'),
(40, 40, '2020-01-01', NULL, 'activo'),
(41, 41, '2020-01-01', NULL, 'activo'),
(42, 42, '2020-01-01', NULL, 'activo'),
(43, 43, '2020-01-01', NULL, 'activo'),
(44, 44, '2020-01-01', NULL, 'activo'),
(45, 45, '2020-01-01', NULL, 'activo'),
(46, 46, '2020-01-01', NULL, 'activo'),
(47, 47, '2020-01-01', NULL, 'activo'),
(48, 48, '2020-01-01', NULL, 'activo'),
(49, 49, '2020-01-01', NULL, 'activo'),
(50, 50, '2020-01-01', NULL, 'activo');

-- ========================================================
-- CREAR SERVICIOS (AGUA Y LUZ)
-- ========================================================

INSERT INTO servicio (nombre_servicio, descripcion, tipo_tarifa, costo_unitario, tarifa_fija, unidad_medida) VALUES
('Agua', 'Servicio de agua potable', 'medidor', 1.50, NULL, 'm³'),
('Luz', 'Servicio de energía eléctrica', 'medidor', 0.80, NULL, 'kWh');

-- ========================================================
-- ASIGNAR SERVICIOS A DEPARTAMENTOS
-- ========================================================

-- Asignar servicios a todos los departamentos desde 2020
INSERT INTO tiene_serv (id_departamento, id_servicio, fecha_inicio_servicio, fecha_fin_servicio)
SELECT id_departamento, 1, '2020-01-01', '2025-12-31' FROM departamento;

INSERT INTO tiene_serv (id_departamento, id_servicio, fecha_inicio_servicio, fecha_fin_servicio)
SELECT id_departamento, 2, '2020-01-01', '2025-12-31' FROM departamento;

-- ========================================================
-- CREAR 5 ÁREAS COMUNES
-- ========================================================

CREATE TABLE area_comun (
  id_area_comun int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nombre varchar(100) NOT NULL,
  descripcion text DEFAULT NULL,
  capacidad int(11) DEFAULT NULL,
  id_edificio int(11) NOT NULL,
  FOREIGN KEY (id_edificio) REFERENCES edificio(id_edificio)
);

CREATE TABLE reserva (
  id_usuario int(11) NOT NULL,
  id_area_comun int(11) NOT NULL,
  fecha_inicio datetime NOT NULL,
  fecha_fin datetime NOT NULL,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
  FOREIGN KEY (id_area_comun) REFERENCES area_comun(id_area_comun)
);




INSERT INTO area_comun (nombre, descripcion, capacidad, id_edificio) VALUES
('Sala de Eventos', 'Amplio espacio para eventos sociales', 100, 1),
('Gimnasio', 'Equipamiento completo para ejercicio', 20, 1),
('Piscina', 'Piscina climatizada', 30, 1),
('Terraza', 'Terraza con vista panorámica', 50, 1),
('Salón de Juegos', 'Mesas de billar y juegos de mesa', 25, 1);

-- ========================================================
-- CREAR RESERVAS DE ÁREAS COMUNES
-- ========================================================

INSERT INTO reserva (id_usuario, id_area_comun, fecha_inicio, fecha_fin) VALUES
(1, 1, '2024-01-15 10:00:00', '2024-01-15 12:00:00'),
(2, 2, '2024-01-16 08:00:00', '2024-01-16 10:00:00'),
(3, 3, '2024-01-17 14:00:00', '2024-01-17 16:00:00'),
(4, 4, '2024-01-18 18:00:00', '2024-01-18 20:00:00'),
(5, 5, '2024-01-19 16:00:00', '2024-01-19 18:00:00'),
(6, 1, '2024-01-20 10:00:00', '2024-01-20 12:00:00'),
(7, 2, '2024-01-21 08:00:00', '2024-01-21 10:00:00'),
(8, 3, '2024-01-22 14:00:00', '2024-01-22 16:00:00'),
(9, 4, '2024-01-23 18:00:00', '2024-01-23 20:00:00'),
(10, 5, '2024-01-24 16:00:00', '2024-01-24 18:00:00');

-- ========================================================
-- CREAR INCIDENTES
-- ========================================================

-- Primero, asegurarnos de que las tablas tengan la estructura correcta
CREATE TABLE incidente (
  id_incidente INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  titulo_incidente VARCHAR(100) NOT NULL,
  descripcion TEXT DEFAULT NULL,
  fecha_creacion DATETIME NOT NULL,
  estado VARCHAR(50) DEFAULT NULL,
  id_departamento INT(11) NOT NULL,
  FOREIGN KEY (id_departamento) REFERENCES departamento(id_departamento)
);

CREATE TABLE atiende_incidente (
  id_incidente INT(11) NOT NULL,
  id_personal INT(11) NOT NULL,
  fecha_atendido DATETIME NOT NULL,
  acciones_tomadas TEXT DEFAULT NULL,
  FOREIGN KEY (id_incidente) REFERENCES incidente(id_incidente),
  FOREIGN KEY (id_personal) REFERENCES personal(id_personal)
);

CREATE TABLE historial_incidentes (
  id_historial_incidente INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fecha_creacion DATETIME NOT NULL,
  fecha_atendido DATETIME NOT NULL,
  acciones_tomadas TEXT DEFAULT NULL,
  id_incidente INT(11) NOT NULL,
  id_personal INT(11) NOT NULL,
  FOREIGN KEY (id_incidente) REFERENCES incidente(id_incidente),
  FOREIGN KEY (id_personal) REFERENCES personal(id_personal)
);

-- Insertar incidentes CORREGIDOS (sin id_usuario y sin prioridad)
INSERT INTO incidente (titulo_incidente, descripcion, fecha_creacion, estado, id_departamento) VALUES
('Fuga de agua en el baño', 'Fuga de agua en la tubería principal del baño', '2024-01-05 10:30:00', 'resuelto', 1),
('Problema con el interruptor de luz', 'Interruptor de la sala no funciona correctamente', '2024-01-06 14:15:00', 'resuelto', 2),
('Puerta del departamento no cierra bien', 'Problema con el mecanismo de cierre de la puerta principal', '2024-01-07 09:45:00', 'en proceso', 3),
('Calefacción no funciona', 'Sistema de calefacción no enciende', '2024-01-08 16:20:00', 'pendiente', 4),
('Problema con toma corriente', 'Toma corriente en la cocina hace cortocircuito', '2024-01-09 11:10:00', 'resuelto', 5),
('Ventana rota', 'Vidrio de la ventana del dormitorio principal roto', '2024-01-10 13:25:00', 'en proceso', 6),
('Filtración en el techo', 'Manchas de humedad en el techo del baño', '2024-01-11 08:40:00', 'pendiente', 7),
('Aire acondicionado no enfría', 'Equipo de aire acondicionado no refrigera adecuadamente', '2024-01-12 15:30:00', 'resuelto', 8),
('Problema con la cerradura', 'Cerradura de la puerta principal dañada', '2024-01-13 10:15:00', 'resuelto', 9),
('Tubería tapada en cocina', 'Desagüe de la cocina completamente tapado', '2024-01-14 12:50:00', 'en proceso', 10);

-- Insertar datos en atiende_incidente
INSERT INTO atiende_incidente (id_incidente, id_personal, fecha_atendido, acciones_tomadas) VALUES
(1, 1, '2024-01-05 11:00:00', 'Reparada fuga en tubería principal y revisado sistema completo'),
(2, 2, '2024-01-06 15:30:00', 'Reemplazado interruptor dañado y revisado cableado eléctrico'),
(3, 3, '2024-01-07 10:15:00', 'Ajustado bisagras y lubricado mecanismo de cierre'),
(5, 2, '2024-01-09 12:30:00', 'Reemplazada toma corriente y revisada instalación eléctrica'),
(8, 1, '2024-01-12 16:45:00', 'Recargado gas refrigerante y limpiado filtros del equipo'),
(9, 3, '2024-01-13 11:30:00', 'Reemplazada cerradura completa y probado funcionamiento'),
(10, 1, '2024-01-14 14:20:00', 'Desatascada tubería con equipo especializado');

-- Insertar datos en historial_incidentes (solo para incidentes resueltos)
INSERT INTO historial_incidentes (fecha_creacion, fecha_atendido, acciones_tomadas, id_incidente, id_personal) VALUES
('2024-01-05 10:30:00', '2024-01-05 11:00:00', 'Reparada fuga en tubería principal y revisado sistema completo', 1, 1),
('2024-01-06 14:15:00', '2024-01-06 15:30:00', 'Reemplazado interruptor dañado y revisado cableado eléctrico', 2, 2),
('2024-01-09 11:10:00', '2024-01-09 12:30:00', 'Reemplazada toma corriente y revisada instalación eléctrica', 5, 2),
('2024-01-12 15:30:00', '2024-01-12 16:45:00', 'Recargado gas refrigerante y limpiado filtros del equipo', 8, 1),
('2024-01-13 10:15:00', '2024-01-13 11:30:00', 'Reemplazada cerradura completa y probado funcionamiento', 9, 3);

-- Insertar más incidentes históricos para tener datos suficientes para el dashboard
INSERT INTO incidente (titulo_incidente, descripcion, fecha_creacion, estado, id_departamento) VALUES
-- Incidentes 2023
('Ascensor atascado', 'Ascensor se atascó entre el 3er y 4to piso', '2023-03-15 08:30:00', 'resuelto', 15),
('Fuga en sótano', 'Fuga de agua en tubería del sótano', '2023-04-20 14:00:00', 'resuelto', 20),
('Problema iluminación', 'Lámparas del pasillo no encienden', '2023-05-10 09:15:00', 'resuelto', 25),
('Puerta eléctrica', 'Puerta eléctrica principal no funciona', '2023-06-05 16:45:00', 'resuelto', 30),
('Inundación garaje', 'Inundación en área de estacionamiento', '2023-07-12 11:20:00', 'resuelto', 35),

-- Incidentes 2024 (adicionales)
('Cableado expuesto', 'Cableado eléctrico expuesto en pasillo', '2024-02-01 13:10:00', 'pendiente', 12),
('Caldera sin presión', 'Caldera sin presión de agua', '2024-02-03 10:45:00', 'en proceso', 18),
('Persiana rota', 'Mecanismo de persiana dañado', '2024-02-05 15:30:00', 'pendiente', 22),
('Olor a gas', 'Olor a gas en área común', '2024-02-07 08:20:00', 'resuelto', 28),
('Techo desprendido', 'Parte del techo desprendido en hall', '2024-02-09 12:15:00', 'en proceso', 32);

-- Más asignaciones de personal
INSERT INTO atiende_incidente (id_incidente, id_personal, fecha_atendido, acciones_tomadas) VALUES
(11, 2, '2023-03-15 09:45:00', 'Liberado ascensor y revisado sistema de seguridad'),
(12, 1, '2023-04-20 15:30:00', 'Reparada tubería rota y limpiada área afectada'),
(13, 3, '2023-05-10 10:30:00', 'Reemplazadas lámparas y revisado circuito eléctrico'),
(14, 2, '2023-06-05 17:30:00', 'Reparado motor de puerta y ajustado sensores'),
(15, 1, '2023-07-12 12:45:00', 'Drenada agua y revisado sistema de desagüe'),
(19, 1, '2024-02-07 09:15:00', 'Revisada instalación de gas y selladas fugas');

-- Más historial de incidentes
INSERT INTO historial_incidentes (fecha_creacion, fecha_atendido, acciones_tomadas, id_incidente, id_personal) VALUES
('2023-03-15 08:30:00', '2023-03-15 09:45:00', 'Liberado ascensor y revisado sistema de seguridad', 11, 2),
('2023-04-20 14:00:00', '2023-04-20 15:30:00', 'Reparada tubería rota y limpiada área afectada', 12, 1),
('2023-05-10 09:15:00', '2023-05-10 10:30:00', 'Reemplazadas lámparas y revisado circuito eléctrico', 13, 3),
('2023-06-05 16:45:00', '2023-06-05 17:30:00', 'Reparado motor de puerta y ajustado sensores', 14, 2),
('2023-07-12 11:20:00', '2023-07-12 12:45:00', 'Drenada agua y revisado sistema de desagüe', 15, 1),
('2024-02-07 08:20:00', '2024-02-07 09:15:00', 'Revisada instalación de gas y selladas fugas', 19, 1);

-- ========================================================
-- CREAR HISTORIAL DE CONSUMO Y FACTURAS (5 AÑOS: 2020-2024)
-- ========================================================

-- Procedimiento para generar datos de consumo y facturas
DELIMITER //

CREATE PROCEDURE GenerarDatosConsumoFacturas()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE dept_id INT;
    DECLARE cur CURSOR FOR SELECT id_departamento FROM departamento;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN cur;
    
    read_loop: LOOP
        FETCH cur INTO dept_id;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        -- Generar datos para cada mes desde enero 2020 hasta diciembre 2024
        SET @year_start = 2020;
        WHILE @year_start <= 2024 DO
            SET @month = 1;
            WHILE @month <= 12 DO
                -- Generar consumo de agua (entre 10-30 m³)
                SET @consumo_agua = FLOOR(10 + RAND() * 21);
                
                -- Generar consumo de luz (entre 100-400 kWh)
                SET @consumo_luz = FLOOR(100 + RAND() * 301);
                
                -- Fechas
                SET @fecha_inicio_lectura = CONCAT(@year_start, '-', LPAD(@month, 2, '0'), '-01 00:00:00');
                SET @fecha_fin_lectura = LAST_DAY(@fecha_inicio_lectura);
                SET @fecha_emision = DATE_ADD(@fecha_fin_lectura, INTERVAL 2 DAY);
                SET @fecha_vencimiento = DATE_ADD(@fecha_emision, INTERVAL 10 DAY);
                
                -- Insertar lecturas de sensores para agua
                INSERT INTO lectura_sensor (hora_lectura, consumo, id_departamento, id_servicio)
                VALUES (@fecha_fin_lectura, @consumo_agua, dept_id, 1);
                
                SET @lectura_agua_id = LAST_INSERT_ID();
                
                -- Insertar historial de consumo para agua
                INSERT INTO historial_consumo (fecha_inicio_lectura, fecha_fin_lectura, consumo_total, id_lectura)
                VALUES (@fecha_inicio_lectura, @fecha_fin_lectura, @consumo_agua, @lectura_agua_id);
                
                -- Insertar lecturas de sensores para luz
                INSERT INTO lectura_sensor (hora_lectura, consumo, id_departamento, id_servicio)
                VALUES (@fecha_fin_lectura, @consumo_luz, dept_id, 2);
                
                SET @lectura_luz_id = LAST_INSERT_ID();
                
                -- Insertar historial de consumo para luz
                INSERT INTO historial_consumo (fecha_inicio_lectura, fecha_fin_lectura, consumo_total, id_lectura)
                VALUES (@fecha_inicio_lectura, @fecha_fin_lectura, @consumo_luz, @lectura_luz_id);
                
                -- Calcular montos (asumiendo que servicio 1 es agua y 2 es luz)
                SET @monto_agua = @consumo_agua * (SELECT costo_unitario FROM servicio WHERE id_servicio = 1);
                SET @monto_luz = @consumo_luz * (SELECT costo_unitario FROM servicio WHERE id_servicio = 2);
                SET @monto_total = @monto_agua + @monto_luz;
                
                -- Obtener el último id_historial_consumo insertado (para luz)
                SET @historial_id = LAST_INSERT_ID();
                
                -- Insertar factura
                INSERT INTO factura (fecha_emision, fecha_vencimiento, monto, estado_pago, id_historial_consumo)
                VALUES (@fecha_emision, @fecha_vencimiento, @monto_total, 'pagado', @historial_id);
                
                -- Obtener el ID de la factura recién insertada
                SET @factura_id = LAST_INSERT_ID();
                
                -- Fecha de pago aleatoria (entre 0-15 días después de emisión)
                SET @dias_pago = FLOOR(RAND() * 16);
                SET @fecha_pago = DATE_ADD(@fecha_emision, INTERVAL @dias_pago DAY);
                
                -- Método de pago aleatorio
                SET @metodo_pago = ELT(FLOOR(1 + RAND() * 3), 'QR', 'Transferencia Bancaria', 'Depósito Bancario');
                
                -- Obtener un usuario asociado al departamento
                SET @usuario_id = (SELECT id_usuario FROM pertenece_dep WHERE id_departamento = dept_id AND estado = 'activo' LIMIT 1);
                
                -- Insertar pago si existe un usuario
                IF @usuario_id IS NOT NULL THEN
                    INSERT INTO pago (id_usuario, id_factura, fecha_pago, metodo_pago, referencia_bancaria)
                    VALUES (@usuario_id, @factura_id, @fecha_pago, @metodo_pago, CONCAT('REF', @factura_id));
                    
                    -- Insertar historial de pagos
                    INSERT INTO historial_pagos (id_factura, fecha_emision, fecha_vencimiento, monto_total, fecha_pago, metodo_pago)
                    VALUES (@factura_id, @fecha_emision, @fecha_vencimiento, @monto_total, @fecha_pago, @metodo_pago);
                END IF;
                
                SET @month = @month + 1;
            END WHILE;
            SET @year_start = @year_start + 1;
        END WHILE;
    END LOOP;
    
    CLOSE cur;
END //

DELIMITER ;

-- Ejecutar el procedimiento para generar datos
CALL GenerarDatosConsumoFacturas();

-- Eliminar el procedimiento después de usarlo
DROP PROCEDURE GenerarDatosConsumoFacturas;

-- ========================================================
-- ACTUALIZAR ALGUNAS FACTURAS COMO PENDIENTES PARA 2024
-- ========================================================


-- ========================================================
-- VERIFICACIÓN DE DATOS INSERTADOS
-- ========================================================

-- Mostrar conteos para verificar
SELECT 
    (SELECT COUNT(*) FROM personal) as total_personal,
    (SELECT COUNT(*) FROM usuario) as total_usuarios,
    (SELECT COUNT(*) FROM departamento) as total_departamentos,
    (SELECT COUNT(*) FROM servicio) as total_servicios,
    (SELECT COUNT(*) FROM area_comun) as total_areas_comunes,
    (SELECT COUNT(*) FROM reserva) as total_reservas,
    (SELECT COUNT(*) FROM incidente) as total_incidentes,
    (SELECT COUNT(*) FROM historial_consumo) as total_consumos,
    (SELECT COUNT(*) FROM factura) as total_facturas,
    (SELECT COUNT(*) FROM pago) as total_pagos;



-- ========================================================
-- LLENAR TABLAS DE INCIDENTES (CORREGIDAS)
-- ========================================================

-- Insertar incidentes
INSERT INTO incidente (titulo_incidente, descripcion, fecha_creacion, estado, id_departamento)
VALUES
-- Incidentes resueltos
('Fuga de Agua', 'Fuga de agua en el baño del departamento', '2023-01-15 10:30:00', 'resuelto', 1),
('Problema Eléctrico', 'Interruptor de luz no funciona en la sala', '2023-02-20 14:15:00', 'resuelto', 2),
('Puerta Atascada', 'Puerta principal difícil de abrir', '2023-03-10 09:45:00', 'resuelto', 3),
('Filtración Techo', 'Manchas de humedad en el techo de la cocina', '2023-04-05 16:20:00', 'resuelto', 4),
('Aire Acondicionado', 'Aire acondicionado no enfría adecuadamente', '2023-05-12 11:10:00', 'resuelto', 5),

-- Incidentes en proceso
('Ventana Rota', 'Vidrio de ventana roto en dormitorio', '2024-01-08 13:25:00', 'en proceso', 6),
('Problema Fontanería', 'Tubería tapada en cocina', '2024-01-10 08:40:00', 'en proceso', 7),
('Cerradura Dañada', 'Cerradura de puerta principal dañada', '2024-01-12 15:30:00', 'en proceso', 8),
('Olor a Gas', 'Olor a gas en el área de la cocina', '2024-01-14 10:15:00', 'en proceso', 9),
('Calefacción', 'Sistema de calefacción no funciona', '2024-01-16 12:50:00', 'en proceso', 10),

-- Incidentes pendientes
('Pintura Desgastada', 'Pintura de paredes desgastada en sala', '2024-01-18 09:20:00', 'pendiente', 11),
('Cortocircuito', 'Toma corriente en habitación hace cortocircuito', '2024-01-20 17:45:00', 'pendiente', 12),
('Inundación', 'Inundación menor en baño por fuga', '2024-01-22 14:30:00', 'pendiente', 13),
('Ascensor Atascado', 'Ascensor se atascó entre pisos 5 y 6', '2024-01-24 16:10:00', 'pendiente', 14),
('Ruido Extraño', 'Ruido constante proveniente del sistema de ventilación', '2024-01-26 11:05:00', 'pendiente', 15);

-- Asignar personal a incidentes (atiende_incidente)
INSERT INTO atiende_incidente (id_incidente, id_personal, fecha_atendido, acciones_tomadas)
VALUES
-- Incidentes resueltos (asignados a personal de mantenimiento)
(1, 1, '2023-01-15 11:00:00', 'Reparada fuga de agua en tubería principal del baño'),
(2, 2, '2023-02-20 15:30:00', 'Reemplazado interruptor dañado y revisado cableado'),
(3, 3, '2023-03-10 10:15:00', 'Ajustado bisagras y lubricado mecanismo de cierre'),
(4, 1, '2023-04-05 17:00:00', 'Selladas filtraciones y aplicado tratamiento antihumedad'),
(5, 2, '2023-05-12 12:30:00', 'Recargado gas refrigerante y limpiado filtros'),

-- Incidentes en proceso
(6, 3, '2024-01-08 14:00:00', 'Tomadas medidas de ventana para reemplazo de vidrio'),
(7, 1, '2024-01-10 09:15:00', 'Aplicado desatascador químico y revisado tuberías'),
(8, 2, '2024-01-12 16:00:00', 'Diagnosticado falla en mecanismo de cerradura'),
(9, 3, '2024-01-14 11:00:00', 'Revisada instalación de gas y detectadas fugas menores'),
(10, 1, '2024-01-16 13:30:00', 'Verificado termostato y revisado sistema de calefacción');

-- Historial de incidentes (para incidentes resueltos)
INSERT INTO historial_incidentes (fecha_creacion, fecha_atendido, acciones_tomadas, id_incidente, id_personal)
SELECT 
    i.fecha_creacion,
    ai.fecha_atendido,
    ai.acciones_tomadas,
    i.id_incidente,
    ai.id_personal
FROM incidente i
JOIN atiende_incidente ai ON i.id_incidente = ai.id_incidente
WHERE i.estado = 'resuelto';

-- ========================================================
-- CREAR MÁS INCIDENTES HISTÓRICOS (2020-2023)
-- ========================================================

INSERT INTO incidente (titulo_incidente, descripcion, fecha_creacion, estado, id_departamento)
SELECT 
    CASE 
        WHEN RAND() < 0.2 THEN 'Fuga de Agua'
        WHEN RAND() < 0.4 THEN 'Problema Eléctrico'
        WHEN RAND() < 0.6 THEN 'Puerta Atascada'
        WHEN RAND() < 0.8 THEN 'Avería Ascensor'
        ELSE 'Problema Fontanería'
    END as titulo_incidente,
    CASE 
        WHEN RAND() < 0.2 THEN 'Fuga menor en cocina'
        WHEN RAND() < 0.4 THEN 'Problema con toma corriente'
        WHEN RAND() < 0.6 THEN 'Puerta difícil de cerrar'
        WHEN RAND() < 0.8 THEN 'Ascensor presenta fallas'
        ELSE 'Tubería tapada en baño'
    END as descripcion,
    DATE_ADD('2020-01-01', INTERVAL FLOOR(RAND() * 1460) DAY) as fecha_creacion,
    'resuelto' as estado,
    FLOOR(1 + RAND() * 50) as id_departamento
FROM (
    SELECT 1 as n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 
    UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10
    UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15
    UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION SELECT 20
) numbers
LIMIT 50;

-- Asignar personal a incidentes históricos
INSERT INTO atiende_incidente (id_incidente, id_personal, fecha_atendido, acciones_tomadas)
SELECT 
    i.id_incidente,
    FLOOR(1 + RAND() * 3) as id_personal,  -- Personal de mantenimiento (1-3)
    DATE_ADD(i.fecha_creacion, INTERVAL FLOOR(1 + RAND() * 3) DAY) as fecha_atendido,
    CASE 
        WHEN RAND() < 0.2 THEN 'Reparada fuga y revisado sistema'
        WHEN RAND() < 0.4 THEN 'Reemplazado componente eléctrico'
        WHEN RAND() < 0.6 THEN 'Ajustado mecanismo de puerta'
        WHEN RAND() < 0.8 THEN 'Reparado sistema de ascensor'
        ELSE 'Desatascada tubería y revisado sistema'
    END as acciones_tomadas
FROM incidente i
WHERE i.id_incidente > 15  -- Los incidentes después de los primeros 15
AND i.estado = 'resuelto';

-- Historial para incidentes históricos
INSERT INTO historial_incidentes (fecha_creacion, fecha_atendido, acciones_tomadas, id_incidente, id_personal)
SELECT 
    i.fecha_creacion,
    ai.fecha_atendido,
    ai.acciones_tomadas,
    i.id_incidente,
    ai.id_personal
FROM incidente i
JOIN atiende_incidente ai ON i.id_incidente = ai.id_incidente
WHERE i.id_incidente > 15
AND i.estado = 'resuelto';


