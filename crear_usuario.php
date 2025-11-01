<?php

class Database
{
    private $host = 'localhost';
    private $db_name = 'db_edificio_v5';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function getConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

class PersonaModelo
{
    private $db;
    private $table_name = "persona";
    private $encryption_key;

    public function __construct($db, $encryption_key = null){
        $this->db = $db;
        $this->encryption_key = $encryption_key ?: '1A3F6C9E2B5D8A0C7E4F1A2B3C8D5E6F7A1B2C3D4E5F6A7B8C9D0E1F2A3B4C5D6';

        if (strlen($this->encryption_key) < 32) {
            $this->encryption_key = str_pad($this->encryption_key, 32, "\0");
        } elseif (strlen($this->encryption_key) > 32) {
            $this->encryption_key = substr($this->encryption_key, 0, 32);
        }
    }

    private function encrypt($data) {
        if (empty($data)) return $data;
        try {
            $iv = openssl_random_pseudo_bytes(16);
            $encrypted = openssl_encrypt($data, 'AES-256-CBC', $this->encryption_key, OPENSSL_RAW_DATA, $iv);
            if ($encrypted === false) {
                throw new Exception('Error en cifrado: ' . openssl_error_string());
            }
            return base64_encode($iv . $encrypted);
        } catch (Exception $e) {
            error_log("Error cifrando datos: " . $e->getMessage());
            return false;
        }
    }

    private function decrypt($encrypted_data) {
        if (empty($encrypted_data)) return $encrypted_data;
        try {
            $data = base64_decode($encrypted_data);
            if ($data === false) {
                throw new Exception('Error decodificando base64');
            }
            $iv = substr($data, 0, 16);
            $encrypted = substr($data, 16);
            $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $this->encryption_key, OPENSSL_RAW_DATA, $iv);
            if ($decrypted === false) {
                throw new Exception('Error en descifrado: ' . openssl_error_string());
            }
            return $decrypted;
        } catch (Exception $e) {
            error_log("Error descifrando datos: " . $e->getMessage());
            return false;
        }
    }

    public function verificarCIExistente($ci) {
        $sql = "SELECT id_persona, ci FROM ".$this->table_name;
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $personas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($personas as $persona) {
                $ci_decrypted = $this->decrypt($persona['ci']);
                if ($ci_decrypted === $ci) {
                    return true;
                }
            }
        }
        return false;
    }

    public function verificarUsuarioExistente($username) {
        $sql = "SELECT id_persona FROM ".$this->table_name." WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function verificarEmailExistente($email) {
        $sql = "SELECT id_persona FROM ".$this->table_name." WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function registrarPersona($nombre, $apellido_paterno, $apellido_materno, $ci, $telefono, $email, $username, $password, $id_rol){
        try {
            $nombre_encrypted = $this->encrypt($nombre);
            $apellido_paterno_encrypted = $this->encrypt($apellido_paterno);
            $apellido_materno_encrypted = $this->encrypt($apellido_materno);
            $ci_encrypted = $this->encrypt($ci);

            if (!$nombre_encrypted || !$apellido_paterno_encrypted || !$ci_encrypted) {
                throw new Exception("Error al cifrar los datos");
            }

            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            if (!$password_hash) {
                throw new Exception("Error al generar el hash de la contraseña");
            }

            $query = "INSERT INTO " . $this->table_name . " 
                     (nombre, apellido_paterno, apellido_materno, ci, telefono, email, username, password_hash, tiempo_verificacion, id_rol) 
                     VALUES (:nombre, :apellido_paterno, :apellido_materno, :ci, :telefono, :email, :username, :password_hash,  DATE_ADD(NOW(), INTERVAL 3 DAY), :id_rol)";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":nombre", $nombre_encrypted);
            $stmt->bindParam(":apellido_paterno", $apellido_paterno_encrypted);
            $stmt->bindParam(":apellido_materno", $apellido_materno_encrypted);
            $stmt->bindParam(":ci", $ci_encrypted);
            $stmt->bindParam(":telefono", $telefono);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":password_hash", $password_hash);
            $stmt->bindParam(":id_rol", $id_rol);

            if($stmt->execute()){
                return $this->db->lastInsertId();
            }else{
                error_log("Error en execute: " . implode(", ", $stmt->errorInfo()));
                return false;
            }
        } catch (Exception $e) {
            error_log("Error en registrarPersona: " . $e->getMessage());
            return false;
        }
    }
}

class DataSeeder
{
    private $db;
    private $personaModelo;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->personaModelo = new PersonaModelo($this->db);
    }

    private function crearRoles() {
        try {
            echo "📋 Creando roles...\n";

            $roles = [
                ['id_rol' => 1, 'rol' => 'Administrador', 'descripcion' => 'Administrador del sistema', 'salario_base' => 5000.00],
                ['id_rol' => 2, 'rol' => 'Residente', 'descripcion' => 'Residente de área', 'salario_base' => 0.00],
                ['id_rol' => 3, 'rol' => 'Soporte Externo', 'descripcion' => 'Soporte técnico externo', 'salario_base' => 0.00],
                ['id_rol' => 4, 'rol' => 'Soporte Interno', 'descripcion' => 'Soporte técnico interno', 'salario_base' => 3200.00],
                ['id_rol' => 5, 'rol' => 'Limpieza', 'descripcion' => 'Personal de limpieza', 'salario_base' => 2650.00],
                ['id_rol' => 6, 'rol' => 'Portero', 'descripcion' => 'Personal de vigilancia', 'salario_base' => 2850.00]
            ];

            foreach ($roles as $rol) {
                $query = "SELECT id_rol FROM rol WHERE id_rol = :id_rol";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id_rol', $rol['id_rol']);
                $stmt->execute();

                if ($stmt->rowCount() == 0) {
                    $query = "INSERT INTO rol (id_rol, rol, descripcion, salario_base) VALUES (:id_rol, :rol, :descripcion, :salario_base)";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':id_rol', $rol['id_rol']);
                    $stmt->bindParam(':rol', $rol['rol']);
                    $stmt->bindParam(':descripcion', $rol['descripcion']);
                    $stmt->bindParam(':salario_base', $rol['salario_base']);

                    if ($stmt->execute()) {
                        echo "✅ Rol '{$rol['rol']}' creado exitosamente.\n";
                    } else {
                        throw new Exception("Error al crear el rol {$rol['rol']}");
                    }
                } else {
                    echo "✅ El rol '{$rol['rol']}' ya existe.\n";
                }
            }
            return true;

        } catch (Exception $e) {
            echo "❌ Error al crear roles: " . $e->getMessage() . "\n";
            return false;
        }
    }

    public function crearAdministrador() {
        echo "🚀 INICIANDO CREACIÓN DE USUARIO ADMINISTRADOR\n";
        echo "=============================================\n\n";

        try {
            // Verificar si el rol Administrador existe
            $query = "SELECT id_rol FROM rol WHERE rol = 'Administrador'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            if ($stmt->rowCount() == 0) {
                throw new Exception("El rol Administrador no existe. Ejecuta primero crearRoles()");
            }

            $id_rol = $stmt->fetch(PDO::FETCH_ASSOC)['id_rol'];

            // Datos del administrador por defecto
            $datosAdmin = [
                'nombre' => 'Admin',
                'apellido_paterno' => 'Sistema',
                'apellido_materno' => 'Principal',
                'ci' => '12345678',
                'telefono' => '77777777',
                'email' => 'admin@sistema.com',
                'username' => 'admin',
                'password' => 'admin123'
            ];

            echo "📝 Verificando datos del administrador...\n";

            if ($this->personaModelo->verificarUsuarioExistente($datosAdmin['username'])) {
                echo "⚠️  El usuario '{$datosAdmin['username']}' ya existe.\n";
                return false;
            }

            if ($this->personaModelo->verificarEmailExistente($datosAdmin['email'])) {
                echo "⚠️  El email '{$datosAdmin['email']}' ya existe.\n";
                return false;
            }

            if ($this->personaModelo->verificarCIExistente($datosAdmin['ci'])) {
                echo "⚠️  El CI '{$datosAdmin['ci']}' ya existe.\n";
                return false;
            }

            echo "✅ Todas las verificaciones pasaron correctamente.\n";
            echo "📦 Registrando administrador en la base de datos...\n";

            $resultado = $this->personaModelo->registrarPersona(
                $datosAdmin['nombre'],
                $datosAdmin['apellido_paterno'],
                $datosAdmin['apellido_materno'],
                $datosAdmin['ci'],
                $datosAdmin['telefono'],
                $datosAdmin['email'],
                $datosAdmin['username'],
                $datosAdmin['password'],
                $id_rol
            );

            if ($resultado) {
                echo "\n🎉 ¡ADMINISTRADOR CREADO EXITOSAMENTE!\n";
                echo "=============================================\n";
                echo "📋 DATOS DE ACCESO:\n";
                echo "   👤 Usuario: {$datosAdmin['username']}\n";
                echo "   🔑 Contraseña: {$datosAdmin['password']}\n";
                echo "   📧 Email: {$datosAdmin['email']}\n";
                echo "   👨‍💼 Nombre: {$datosAdmin['nombre']} {$datosAdmin['apellido_paterno']} {$datosAdmin['apellido_materno']}\n";
                echo "   🆔 CI: {$datosAdmin['ci']}\n";
                echo "   📞 Teléfono: {$datosAdmin['telefono']}\n";
                echo "   🎯 Rol: Administrador\n";
                echo "=============================================\n";
                echo "⚠️  IMPORTANTE: Cambia la contraseña después del primer inicio de sesión.\n";
                return true;
            } else {
                throw new Exception("Error al registrar el administrador en la base de datos");
            }

        } catch (Exception $e) {
            echo "❌ Error al crear el administrador: " . $e->getMessage() . "\n";
            return false;
        }
    }

    private function crearPersonasAdicionales() {
        try {
            echo "👥 Creando personas adicionales...\n";

            $personas = [
                [
                    'nombre' => 'María',
                    'apellido_paterno' => 'Residente',
                    'apellido_materno' => 'López',
                    'ci' => '87654321',
                    'telefono' => '77777771',
                    'email' => 'maria@residente.com',
                    'username' => 'maria',
                    'password' => 'maria123',
                    'id_rol' => 2
                ],
                [
                    'nombre' => 'Juan',
                    'apellido_paterno' => 'Técnico',
                    'apellido_materno' => 'Externo',
                    'ci' => '87654322',
                    'telefono' => '77777772',
                    'email' => 'juan@tecnico.com',
                    'username' => 'juan',
                    'password' => 'juan123',
                    'id_rol' => 3
                ],
                [
                    'nombre' => 'Pedro',
                    'apellido_paterno' => 'Mantenimiento',
                    'apellido_materno' => 'Interno',
                    'ci' => '87654323',
                    'telefono' => '77777773',
                    'email' => 'pedro@mantenimiento.com',
                    'username' => 'pedro',
                    'password' => 'pedro123',
                    'id_rol' => 4
                ],
                [
                    'nombre' => 'Ana',
                    'apellido_paterno' => 'Limpieza',
                    'apellido_materno' => 'García',
                    'ci' => '87654324',
                    'telefono' => '77777774',
                    'email' => 'ana@limpieza.com',
                    'username' => 'ana',
                    'password' => 'ana123',
                    'id_rol' => 5
                ],
                [
                    'nombre' => 'Carlos',
                    'apellido_paterno' => 'Portero',
                    'apellido_materno' => 'Martínez',
                    'ci' => '87654325',
                    'telefono' => '77777775',
                    'email' => 'carlos@portero.com',
                    'username' => 'carlos',
                    'password' => 'carlos123',
                    'id_rol' => 6
                ]
            ];

            $personas_ids = [];

            foreach ($personas as $persona) {
                if (!$this->personaModelo->verificarUsuarioExistente($persona['username'])) {
                    $id_persona = $this->personaModelo->registrarPersona(
                        $persona['nombre'],
                        $persona['apellido_paterno'],
                        $persona['apellido_materno'],
                        $persona['ci'],
                        $persona['telefono'],
                        $persona['email'],
                        $persona['username'],
                        $persona['password'],
                        $persona['id_rol']
                    );

                    if ($id_persona) {
                        $personas_ids[$persona['username']] = $id_persona;
                        echo "✅ Persona '{$persona['nombre']} {$persona['apellido_paterno']}' creada exitosamente. ID: {$id_persona}\n";
                    } else {
                        throw new Exception("Error al crear la persona {$persona['nombre']}");
                    }
                } else {
                    $query = "SELECT id_persona FROM persona WHERE username = :username";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':username', $persona['username']);
                    $stmt->execute();
                    $existing_person = $stmt->fetch(PDO::FETCH_ASSOC);
                    $personas_ids[$persona['username']] = $existing_person['id_persona'];
                    echo "✅ La persona '{$persona['nombre']} {$persona['apellido_paterno']}' ya existe. ID: {$existing_person['id_persona']}\n";
                }
            }
            return $personas_ids;

        } catch (Exception $e) {
            echo "❌ Error al crear personas adicionales: " . $e->getMessage() . "\n";
            return false;
        }
    }

    private function crearDepartamentos() {
        try {
            echo "🏢 Creando departamentos...\n";

            $departamentos = [
                ['id_departamento' => 1, 'numero' => '101', 'piso' => 1, 'estado' => 'ocupado'],
                ['id_departamento' => 2, 'numero' => '102', 'piso' => 1, 'estado' => 'ocupado'],
                ['id_departamento' => 3, 'numero' => '201', 'piso' => 2, 'estado' => 'ocupado'],
                ['id_departamento' => 4, 'numero' => '202', 'piso' => 2, 'estado' => 'ocupado']
            ];

            foreach ($departamentos as $depto) {
                $query = "SELECT id_departamento FROM departamento WHERE id_departamento = :id_departamento";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id_departamento', $depto['id_departamento']);
                $stmt->execute();

                if ($stmt->rowCount() == 0) {
                    $query = "INSERT INTO departamento (id_departamento, numero, piso, estado) VALUES (:id_departamento, :numero, :piso, :estado)";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':id_departamento', $depto['id_departamento']);
                    $stmt->bindParam(':numero', $depto['numero']);
                    $stmt->bindParam(':piso', $depto['piso']);
                    $stmt->bindParam(':estado', $depto['estado']);

                    if ($stmt->execute()) {
                        echo "✅ Departamento {$depto['numero']} creado exitosamente.\n";
                    } else {
                        throw new Exception("Error al crear el departamento {$depto['numero']}");
                    }
                } else {
                    echo "✅ El departamento {$depto['numero']} ya existe.\n";
                }
            }
            return true;

        } catch (Exception $e) {
            echo "❌ Error al crear departamentos: " . $e->getMessage() . "\n";
            return false;
        }
    }

    private function crearAreasComunes() {
        try {
            echo "🏊 Creando áreas comunes...\n";

            $areas = [
                [
                    'id_area' => 1,
                    'nombre' => 'Sala de Eventos',
                    'descripcion' => 'Área para eventos sociales',
                    'capacidad' => 50,
                    'costo_reserva' => 100.00,
                    'estado' => 'disponible'
                ],
                [
                    'id_area' => 2,
                    'nombre' => 'Piscina',
                    'descripcion' => 'Piscina climatizada',
                    'capacidad' => 20,
                    'costo_reserva' => 50.00,
                    'estado' => 'disponible'
                ],
                [
                    'id_area' => 3,
                    'nombre' => 'Gimnasio',
                    'descripcion' => 'Gimnasio equipado',
                    'capacidad' => 15,
                    'costo_reserva' => 30.00,
                    'estado' => 'disponible'
                ]
            ];

            foreach ($areas as $area) {
                $query = "SELECT id_area FROM area_comun WHERE id_area = :id_area";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id_area', $area['id_area']);
                $stmt->execute();

                if ($stmt->rowCount() == 0) {
                    $query = "INSERT INTO area_comun (id_area, nombre, descripcion, capacidad, costo_reserva, estado) 
                             VALUES (:id_area, :nombre, :descripcion, :capacidad, :costo_reserva, :estado)";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':id_area', $area['id_area']);
                    $stmt->bindParam(':nombre', $area['nombre']);
                    $stmt->bindParam(':descripcion', $area['descripcion']);
                    $stmt->bindParam(':capacidad', $area['capacidad']);
                    $stmt->bindParam(':costo_reserva', $area['costo_reserva']);
                    $stmt->bindParam(':estado', $area['estado']);

                    if ($stmt->execute()) {
                        echo "✅ Área común '{$area['nombre']}' creada exitosamente.\n";
                    } else {
                        throw new Exception("Error al crear el área común {$area['nombre']}");
                    }
                } else {
                    echo "✅ El área común '{$area['nombre']}' ya existe.\n";
                }
            }
            return true;

        } catch (Exception $e) {
            echo "❌ Error al crear áreas comunes: " . $e->getMessage() . "\n";
            return false;
        }
    }

    private function crearResidentesAdicionales($personas_ids) {
        try {
            echo "🏠 Creando residentes adicionales...\n";

            $residentes = [
                [
                    'nombre' => 'Luis',
                    'apellido_paterno' => 'Gómez',
                    'apellido_materno' => 'Pérez',
                    'ci' => '87654326',
                    'telefono' => '77777776',
                    'email' => 'luis@residente.com',
                    'username' => 'luis',
                    'password' => 'luis123',
                    'id_rol' => 2,
                    'id_departamento' => 2
                ],
                [
                    'nombre' => 'Sofia',
                    'apellido_paterno' => 'Martínez',
                    'apellido_materno' => 'Rodríguez',
                    'ci' => '87654327',
                    'telefono' => '77777777',
                    'email' => 'sofia@residente.com',
                    'username' => 'sofia',
                    'password' => 'sofia123',
                    'id_rol' => 2,
                    'id_departamento' => 3
                ],
                [
                    'nombre' => 'Miguel',
                    'apellido_paterno' => 'Hernández',
                    'apellido_materno' => 'García',
                    'ci' => '87654328',
                    'telefono' => '77777778',
                    'email' => 'miguel@residente.com',
                    'username' => 'miguel',
                    'password' => 'miguel123',
                    'id_rol' => 2,
                    'id_departamento' => 4
                ]
            ];

            $nuevos_ids = [];

            foreach ($residentes as $residente) {
                if (!$this->personaModelo->verificarUsuarioExistente($residente['username'])) {
                    $id_persona = $this->personaModelo->registrarPersona(
                        $residente['nombre'],
                        $residente['apellido_paterno'],
                        $residente['apellido_materno'],
                        $residente['ci'],
                        $residente['telefono'],
                        $residente['email'],
                        $residente['username'],
                        $residente['password'],
                        $residente['id_rol']
                    );

                    if ($id_persona) {
                        $nuevos_ids[$residente['username']] = $id_persona;
                        echo "✅ Residente '{$residente['nombre']} {$residente['apellido_paterno']}' creado exitosamente. ID: {$id_persona}\n";
                    } else {
                        throw new Exception("Error al crear el residente {$residente['nombre']}");
                    }
                } else {
                    $query = "SELECT id_persona FROM persona WHERE username = :username";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':username', $residente['username']);
                    $stmt->execute();
                    $existing_resident = $stmt->fetch(PDO::FETCH_ASSOC);
                    $nuevos_ids[$residente['username']] = $existing_resident['id_persona'];
                    echo "✅ El residente '{$residente['nombre']} {$residente['apellido_paterno']}' ya existe. ID: {$existing_resident['id_persona']}\n";
                }
            }

            return array_merge($personas_ids, $nuevos_ids);

        } catch (Exception $e) {
            echo "❌ Error al crear residentes adicionales: " . $e->getMessage() . "\n";
            return false;
        }
    }

    private function asignarDepartamentos($personas_ids) {
        try {
            echo "🔗 Asignando departamentos a residentes...\n";

            $query = "DELETE FROM tiene_departamento";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            echo "✅ Asignaciones anteriores limpiadas.\n";

            $asignaciones = [
                ['id_departamento' => 1, 'username' => 'maria'],
                ['id_departamento' => 2, 'username' => 'luis'],
                ['id_departamento' => 3, 'username' => 'sofia'],
                ['id_departamento' => 4, 'username' => 'miguel']
            ];

            foreach ($asignaciones as $asignacion) {
                if (isset($personas_ids[$asignacion['username']])) {
                    $id_persona = $personas_ids[$asignacion['username']];

                    $query = "INSERT INTO tiene_departamento (id_departamento, id_persona, estado) VALUES (:id_departamento, :id_persona, 'activo')";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':id_departamento', $asignacion['id_departamento']);
                    $stmt->bindParam(':id_persona', $id_persona);

                    if ($stmt->execute()) {
                        echo "✅ Departamento {$asignacion['id_departamento']} asignado a {$asignacion['username']} (ID: {$id_persona}).\n";
                    } else {
                        throw new Exception("Error al asignar departamento {$asignacion['id_departamento']} a {$asignacion['username']}");
                    }
                } else {
                    throw new Exception("No se encontró el ID para el usuario: {$asignacion['username']}");
                }
            }
            return true;

        } catch (Exception $e) {
            echo "❌ Error al asignar departamentos: " . $e->getMessage() . "\n";
            return false;
        }
    }

    private function crearServicios() {
        try {
            echo "💧 Creando servicios (agua, luz, gas)...\n";

            $servicios = [
                ['id_servicio' => 1, 'nombre' => 'agua', 'unidad_medida' => 'm³', 'costo_unitario' => 2.00, 'estado' => 'activo'],
                ['id_servicio' => 2, 'nombre' => 'luz', 'unidad_medida' => 'kWh', 'costo_unitario' => 2.00, 'estado' => 'activo'],
                ['id_servicio' => 3, 'nombre' => 'gas', 'unidad_medida' => 'm³', 'costo_unitario' => 22.50, 'estado' => 'activo']
            ];

            foreach ($servicios as $servicio) {
                $query = "SELECT id_servicio FROM servicio WHERE id_servicio = :id_servicio";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id_servicio', $servicio['id_servicio']);
                $stmt->execute();

                if ($stmt->rowCount() == 0) {
                    $query = "INSERT INTO servicio (id_servicio, nombre, unidad_medida, costo_unitario, estado) 
                             VALUES (:id_servicio, :nombre, :unidad_medida, :costo_unitario, :estado)";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':id_servicio', $servicio['id_servicio']);
                    $stmt->bindParam(':nombre', $servicio['nombre']);
                    $stmt->bindParam(':unidad_medida', $servicio['unidad_medida']);
                    $stmt->bindParam(':costo_unitario', $servicio['costo_unitario']);
                    $stmt->bindParam(':estado', $servicio['estado']);

                    if ($stmt->execute()) {
                        echo "✅ Servicio '{$servicio['nombre']}' creado exitosamente.\n";
                    } else {
                        throw new Exception("Error al crear el servicio {$servicio['nombre']}");
                    }
                } else {
                    echo "✅ El servicio '{$servicio['nombre']}' ya existe.\n";
                }
            }
            return true;

        } catch (Exception $e) {
            echo "❌ Error al crear servicios: " . $e->getMessage() . "\n";
            return false;
        }
    }

    private function crearMedidores() {
        try {
            echo "📊 Creando medidores para cada departamento y servicio...\n";

            $medidores = [
                // Departamento 101 (agua, luz, gas)
                ['id_medidor' => 1, 'codigo' => 'AGUA-101-001', 'id_servicio' => 1, 'id_departamento' => 1, 'fecha_instalacion' => '2024-01-15', 'estado' => 'activo'],
                ['id_medidor' => 2, 'codigo' => 'LUZ-101-001', 'id_servicio' => 2, 'id_departamento' => 1, 'fecha_instalacion' => '2024-01-15', 'estado' => 'activo'],
                ['id_medidor' => 3, 'codigo' => 'GAS-101-001', 'id_servicio' => 3, 'id_departamento' => 1, 'fecha_instalacion' => '2024-01-15', 'estado' => 'activo'],

                // Departamento 102 (agua, luz, gas)
                ['id_medidor' => 4, 'codigo' => 'AGUA-102-001', 'id_servicio' => 1, 'id_departamento' => 2, 'fecha_instalacion' => '2024-01-15', 'estado' => 'activo'],
                ['id_medidor' => 5, 'codigo' => 'LUZ-102-001', 'id_servicio' => 2, 'id_departamento' => 2, 'fecha_instalacion' => '2024-01-15', 'estado' => 'activo'],
                ['id_medidor' => 6, 'codigo' => 'GAS-102-001', 'id_servicio' => 3, 'id_departamento' => 2, 'fecha_instalacion' => '2024-01-15', 'estado' => 'activo'],

                // Departamento 201 (agua, luz, gas)
                ['id_medidor' => 7, 'codigo' => 'AGUA-201-001', 'id_servicio' => 1, 'id_departamento' => 3, 'fecha_instalacion' => '2024-01-15', 'estado' => 'activo'],
                ['id_medidor' => 8, 'codigo' => 'LUZ-201-001', 'id_servicio' => 2, 'id_departamento' => 3, 'fecha_instalacion' => '2024-01-15', 'estado' => 'activo'],
                ['id_medidor' => 9, 'codigo' => 'GAS-201-001', 'id_servicio' => 3, 'id_departamento' => 3, 'fecha_instalacion' => '2024-01-15', 'estado' => 'activo'],

                // Departamento 202 (agua, luz, gas)
                ['id_medidor' => 10, 'codigo' => 'AGUA-202-001', 'id_servicio' => 1, 'id_departamento' => 4, 'fecha_instalacion' => '2024-01-15', 'estado' => 'activo'],
                ['id_medidor' => 11, 'codigo' => 'LUZ-202-001', 'id_servicio' => 2, 'id_departamento' => 4, 'fecha_instalacion' => '2024-01-15', 'estado' => 'activo'],
                ['id_medidor' => 12, 'codigo' => 'GAS-202-001', 'id_servicio' => 3, 'id_departamento' => 4, 'fecha_instalacion' => '2024-01-15', 'estado' => 'activo']
            ];

            foreach ($medidores as $medidor) {
                $query = "SELECT id_medidor FROM medidor WHERE id_medidor = :id_medidor";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id_medidor', $medidor['id_medidor']);
                $stmt->execute();

                if ($stmt->rowCount() == 0) {
                    $query = "INSERT INTO medidor (id_medidor, codigo, id_servicio, id_departamento, fecha_instalacion, estado) 
                             VALUES (:id_medidor, :codigo, :id_servicio, :id_departamento, :fecha_instalacion, :estado)";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':id_medidor', $medidor['id_medidor']);
                    $stmt->bindParam(':codigo', $medidor['codigo']);
                    $stmt->bindParam(':id_servicio', $medidor['id_servicio']);
                    $stmt->bindParam(':id_departamento', $medidor['id_departamento']);
                    $stmt->bindParam(':fecha_instalacion', $medidor['fecha_instalacion']);
                    $stmt->bindParam(':estado', $medidor['estado']);

                    if ($stmt->execute()) {
                        echo "✅ Medidor '{$medidor['codigo']}' creado exitosamente.\n";
                    } else {
                        throw new Exception("Error al crear el medidor {$medidor['codigo']}");
                    }
                } else {
                    echo "✅ El medidor '{$medidor['codigo']}' ya existe.\n";
                }
            }
            return true;

        } catch (Exception $e) {
            echo "❌ Error al crear medidores: " . $e->getMessage() . "\n";
            return false;
        }
    }

    public function seedData() {
        echo "🚀 INICIANDO CARGA DE DATOS DE PRUEBA\n";
        echo "=============================================\n\n";

        try {
            // Crear roles primero
            if (!$this->crearRoles()) {
                throw new Exception("Error en la creación de roles");
            }

            // Crear administrador
            $this->crearAdministrador();

            // Crear personas adicionales y obtener sus IDs
            $personas_ids = $this->crearPersonasAdicionales();
            if (!$personas_ids) {
                throw new Exception("Error en la creación de personas");
            }

            // Crear departamentos
            if (!$this->crearDepartamentos()) {
                throw new Exception("Error en la creación de departamentos");
            }

            // Crear áreas comunes
            if (!$this->crearAreasComunes()) {
                throw new Exception("Error en la creación de áreas comunes");
            }

            // Crear residentes adicionales y obtener todos los IDs
            $todos_los_ids = $this->crearResidentesAdicionales($personas_ids);
            if (!$todos_los_ids) {
                throw new Exception("Error en la creación de residentes adicionales");
            }

            // Asignar departamentos
            if (!$this->asignarDepartamentos($todos_los_ids)) {
                throw new Exception("Error en la asignación de departamentos");
            }

            // Crear servicios
            if (!$this->crearServicios()) {
                throw new Exception("Error en la creación de servicios");
            }

            // Crear medidores
            if (!$this->crearMedidores()) {
                throw new Exception("Error en la creación de medidores");
            }

            echo "\n🎉 ¡DATOS DE PRUEBA CARGADOS EXITOSAMENTE!\n";
            echo "=============================================\n";
            echo "📊 RESUMEN:\n";
            echo "   📋 6 roles creados\n";
            echo "   👥 9 personas creadas (1 admin + 5 personal + 3 residentes)\n";
            echo "   🏢 4 departamentos creados (2 por piso)\n";
            echo "   🏊 3 áreas comunes creadas\n";
            echo "   🔗 4 asignaciones de departamentos\n";
            echo "   💧 3 servicios creados (agua, luz, gas)\n";
            echo "   📊 12 medidores creados (3 por departamento)\n";
            echo "=============================================\n";
            return true;

        } catch (Exception $e) {
            echo "❌ Error durante la carga de datos: " . $e->getMessage() . "\n";
            return false;
        }
    }

    public function mostrarInfoBaseDatos() {
        try {
            echo "<h1>INFORMACIÓN DE LA BASE DE DATOS</h1>";

            // Estadísticas generales
            $stats = [
                'roles' => "SELECT COUNT(*) as total FROM rol",
                'usuarios' => "SELECT COUNT(*) as total FROM persona",
                'departamentos' => "SELECT COUNT(*) as total FROM departamento",
                'areas' => "SELECT COUNT(*) as total FROM area_comun",
                'asignaciones' => "SELECT COUNT(*) as total FROM tiene_departamento",
                'servicios' => "SELECT COUNT(*) as total FROM servicio",
                'medidores' => "SELECT COUNT(*) as total FROM medidor"
            ];

            foreach ($stats as $key => $query) {
                $stmt = $this->db->prepare($query);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "<p><strong>Total de " . str_replace('_', ' ', $key) . ":</strong> " . $result['total'] . "</p>";
            }

            // Tabla de Roles
            echo "<h2>TABLA DE ROLES</h2>";
            echo "<table border='1'>";
            echo "<thead>";
            echo "<tr><th>ID</th><th>Rol</th><th>Descripción</th><th>Salario Base</th></tr>";
            echo "</thead>";
            echo "<tbody>";

            $query = "SELECT id_rol, rol, descripcion, salario_base FROM rol ORDER BY id_rol";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($roles as $rol) {
                echo "<tr>";
                echo "<td>{$rol['id_rol']}</td>";
                echo "<td><strong>{$rol['rol']}</strong></td>";
                echo "<td>{$rol['descripcion']}</td>";
                echo "<td>$ {$rol['salario_base']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";

            // Tabla de Usuarios
            echo "<h2>TABLA DE USUARIOS</h2>";
            echo "<table border='1'>";
            echo "<thead>";
            echo "<tr><th>ID</th><th>Usuario</th><th>Email</th><th>Rol</th><th>Estado</th></tr>";
            echo "</thead>";
            echo "<tbody>";

            $query = "SELECT p.id_persona, p.username, p.email, r.rol 
                     FROM persona p 
                     INNER JOIN rol r ON p.id_rol = r.id_rol 
                     ORDER BY p.id_persona";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($usuarios as $usuario) {
                $estado = ($usuario['username'] == 'admin') ? 'Administrador' : 'Activo';
                echo "<tr>";
                echo "<td>{$usuario['id_persona']}</td>";
                echo "<td><strong>{$usuario['username']}</strong></td>";
                echo "<td>{$usuario['email']}</td>";
                echo "<td>{$usuario['rol']}</td>";
                echo "<td>{$estado}</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";

            // Tabla de Departamentos
            echo "<h2>TABLA DE DEPARTAMENTOS</h2>";
            echo "<table border='1'>";
            echo "<thead>";
            echo "<tr><th>ID</th><th>Número</th><th>Piso</th><th>Estado</th><th>Residente</th></tr>";
            echo "</thead>";
            echo "<tbody>";

            $query = "SELECT d.id_departamento, d.numero, d.piso, d.estado, 
                             p.username
                      FROM departamento d 
                      LEFT JOIN tiene_departamento td ON d.id_departamento = td.id_departamento
                      LEFT JOIN persona p ON td.id_persona = p.id_persona
                      ORDER BY d.piso, d.numero";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $deptos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($deptos as $depto) {
                $residente = $depto['username'] ? $depto['username'] : 'Sin asignar';
                echo "<tr>";
                echo "<td>{$depto['id_departamento']}</td>";
                echo "<td><strong>{$depto['numero']}</strong></td>";
                echo "<td>{$depto['piso']}</td>";
                echo "<td>{$depto['estado']}</td>";
                echo "<td>{$residente}</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";

            // Tabla de Áreas Comunes
            echo "<h2>TABLA DE ÁREAS COMUNES</h2>";
            echo "<table border='1'>";
            echo "<thead>";
            echo "<tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Capacidad</th><th>Costo Reserva</th><th>Estado</th></tr>";
            echo "</thead>";
            echo "<tbody>";

            $query = "SELECT id_area, nombre, descripcion, capacidad, costo_reserva, estado 
                      FROM area_comun 
                      ORDER BY id_area";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $areas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($areas as $area) {
                echo "<tr>";
                echo "<td>{$area['id_area']}</td>";
                echo "<td><strong>{$area['nombre']}</strong></td>";
                echo "<td>{$area['descripcion']}</td>";
                echo "<td>{$area['capacidad']} personas</td>";
                echo "<td>$ {$area['costo_reserva']}</td>";
                echo "<td>{$area['estado']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";

            // Tabla de Servicios
            echo "<h2>TABLA DE SERVICIOS</h2>";
            echo "<table border='1'>";
            echo "<thead>";
            echo "<tr><th>ID</th><th>Nombre</th><th>Unidad Medida</th><th>Costo Unitario</th><th>Estado</th></tr>";
            echo "</thead>";
            echo "<tbody>";

            $query = "SELECT id_servicio, nombre, unidad_medida, costo_unitario, estado 
                      FROM servicio 
                      ORDER BY id_servicio";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($servicios as $servicio) {
                echo "<tr>";
                echo "<td>{$servicio['id_servicio']}</td>";
                echo "<td><strong>{$servicio['nombre']}</strong></td>";
                echo "<td>{$servicio['unidad_medida']}</td>";
                echo "<td>$ {$servicio['costo_unitario']}</td>";
                echo "<td>{$servicio['estado']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";

            // Tabla de Medidores
            echo "<h2>TABLA DE MEDIDORES</h2>";
            echo "<table border='1'>";
            echo "<thead>";
            echo "<tr><th>ID</th><th>Código</th><th>Servicio</th><th>Departamento</th><th>Fecha Instalación</th><th>Estado</th></tr>";
            echo "</thead>";
            echo "<tbody>";

            $query = "SELECT m.id_medidor, m.codigo, s.nombre as servicio, 
                             d.numero as departamento, m.fecha_instalacion, m.estado
                      FROM medidor m
                      INNER JOIN servicio s ON m.id_servicio = s.id_servicio
                      INNER JOIN departamento d ON m.id_departamento = d.id_departamento
                      ORDER BY m.id_medidor";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $medidores = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($medidores as $medidor) {
                echo "<tr>";
                echo "<td>{$medidor['id_medidor']}</td>";
                echo "<td><strong>{$medidor['codigo']}</strong></td>";
                echo "<td>{$medidor['servicio']}</td>";
                echo "<td>{$medidor['departamento']}</td>";
                echo "<td>{$medidor['fecha_instalacion']}</td>";
                echo "<td>{$medidor['estado']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";

        } catch (Exception $e) {
            echo "<p>Error al obtener información de la base de datos: " . $e->getMessage() . "</p>";
        }
    }
}

// Ejecución principal
echo "<h1>SISTEMA DE CARGA DE DATOS DE PRUEBA</h1>";
echo "<p>Sistema de gestión de edificios - Base de datos</p>";

try {
    echo "<h2>📊 Mostrando información actual de la base de datos...</h2>";

    $dataSeeder = new DataSeeder();

    // Mostrar información actual en HTML
    $dataSeeder->mostrarInfoBaseDatos();

    echo "<h2>🚀 INICIANDO CARGA DE DATOS DE PRUEBA...</h2>";

    // Cargar datos de prueba
    $resultado = $dataSeeder->seedData();

    if ($resultado) {
        echo "<p>✅ Proceso completado exitosamente.</p>";
    } else {
        echo "<p>⚠️ Hubo problemas al cargar los datos de prueba.</p>";
    }

    // Mostrar información final en HTML
    echo "<h2>📊 Mostrando información final de la base de datos...</h2>";
    $dataSeeder->mostrarInfoBaseDatos();

} catch (Exception $e) {
    echo "<p>❌ Error fatal: " . $e->getMessage() . "</p>";
}
?>