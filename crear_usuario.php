<?php

class Database
{
    private $host = 'localhost';
    private $db_name = 'db_edificio_v1';
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
                return true;
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

class AdminCreator 
{
    private $db;
    private $personaModelo;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->personaModelo = new PersonaModelo($this->db);
    }

    private function crearRolAdministrador() {
        try {
            $query = "SELECT id_rol FROM rol WHERE rol = 'Administrador'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo "✅ El rol 'Administrador' ya existe.\n";
                return $stmt->fetch(PDO::FETCH_ASSOC)['id_rol'];
            }

            $query = "INSERT INTO rol (rol, descripcion) VALUES ('Administrador', 'Usuario con acceso total al sistema')";
            $stmt = $this->db->prepare($query);
            
            if ($stmt->execute()) {
                $id_rol = $this->db->lastInsertId();
                echo "✅ Rol 'Administrador' creado exitosamente. ID: " . $id_rol . "\n";
                return $id_rol;
            } else {
                throw new Exception("Error al crear el rol Administrador");
            }

        } catch (Exception $e) {
            echo "❌ Error al crear el rol: " . $e->getMessage() . "\n";
            return false;
        }
    }

    public function crearAdministrador() {
        echo "🚀 INICIANDO CREACIÓN DE USUARIO ADMINISTRADOR\n";
        echo "=============================================\n\n";

        try {
            $id_rol = $this->crearRolAdministrador();
            
            if (!$id_rol) {
                throw new Exception("No se pudo obtener el ID del rol Administrador");
            }

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

    public function mostrarInfoBaseDatos() {
        try {
            echo "\n📊 INFORMACIÓN DE LA BASE DE DATOS:\n";
            echo "=============================================\n";
            
            $query = "SELECT COUNT(*) as total_roles FROM rol";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $roles = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "   Total de roles: " . $roles['total_roles'] . "\n";

            $query = "SELECT COUNT(*) as total_usuarios FROM persona";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $usuarios = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "   Total de usuarios: " . $usuarios['total_usuarios'] . "\n";

            $query = "SELECT id_rol, rol, descripcion FROM rol ORDER BY id_rol";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "   📋 Roles existentes:\n";
            foreach ($roles as $rol) {
                echo "     - ID: {$rol['id_rol']} | Rol: {$rol['rol']} | Descripción: {$rol['descripcion']}\n";
            }

            $query = "SELECT p.id_persona, p.username, p.email, r.rol 
                     FROM persona p 
                     INNER JOIN rol r ON p.id_rol = r.id_rol 
                     ORDER BY p.id_persona";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "   👥 Usuarios existentes:\n";
            foreach ($usuarios as $usuario) {
                echo "     - ID: {$usuario['id_persona']} | Usuario: {$usuario['username']} | Email: {$usuario['email']} | Rol: {$usuario['rol']}\n";
            }

        } catch (Exception $e) {
            echo "❌ Error al obtener información de la base de datos: " . $e->getMessage() . "\n";
        }
    }
}

// Ejecución principal
echo "=============================================\n";
echo "   SISTEMA DE CREACIÓN DE ADMINISTRADOR\n";
echo "=============================================\n";

try {
    $adminCreator = new AdminCreator();
    
    // Mostrar información actual
    $adminCreator->mostrarInfoBaseDatos();
    
    echo "\n";
    
    // Crear el administrador
    $resultado = $adminCreator->crearAdministrador();
    
    if ($resultado) {
        echo "\n✅ Proceso completado exitosamente.\n";
    } else {
        echo "\n⚠️  El administrador no pudo ser creado (puede que ya exista).\n";
    }
    
    // Mostrar información final
    echo "\n";
    $adminCreator->mostrarInfoBaseDatos();
    
} catch (Exception $e) {
    echo "❌ Error fatal: " . $e->getMessage() . "\n";
}

echo "\n=============================================\n";
echo "   FIN DEL PROCESO\n";
echo "=============================================\n";
?>