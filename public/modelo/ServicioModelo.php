<?php
class ServicioModelo {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Registrar un nuevo servicio
     */
    public function registrarServicio($nombre, $unidad_medida, $costo_unitario, $estado = 'activo') {
        try {
            $sql = "INSERT INTO servicio (nombre, unidad_medida, costo_unitario, estado) 
                    VALUES (:nombre, :unidad_medida, :costo_unitario, :estado)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':unidad_medida', $unidad_medida);
            $stmt->bindParam(':costo_unitario', $costo_unitario);
            $stmt->bindParam(':estado', $estado);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al registrar servicio: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Listar todos los servicios
     */
    public function listarServicios() {
        try {
            $sql = "SELECT * FROM servicio ORDER BY id_servicio DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar servicios: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener un servicio por ID
     */
    public function obtenerServicioPorId($id_servicio) {
        try {
            $sql = "SELECT * FROM servicio WHERE id_servicio = :id_servicio";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_servicio', $id_servicio);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener servicio: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Actualizar un servicio
     */
    public function actualizarServicio($id_servicio, $unidad_medida, $costo_unitario, $estado) {
        try {
            $sql = "UPDATE servicio SET unidad_medida = :unidad_medida, costo_unitario = :costo_unitario, 
                    estado = :estado WHERE id_servicio = :id_servicio";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':unidad_medida', $unidad_medida);
            $stmt->bindParam(':costo_unitario', $costo_unitario);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':id_servicio', $id_servicio);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar servicio: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Asignar servicio a departamento (crear medidor)
     */
    public function asignarServicioDepartamento($id_departamento, $id_servicio, $codigo_medidor, $fecha_instalacion, $estado_medidor = 'activo') {
        try {
            $sql = "INSERT INTO medidor (codigo, id_servicio, id_departamento, fecha_instalacion, estado) 
                    VALUES (:codigo, :id_servicio, :id_departamento, :fecha_instalacion, :estado)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':codigo', $codigo_medidor);
            $stmt->bindParam(':id_servicio', $id_servicio);
            $stmt->bindParam(':id_departamento', $id_departamento);
            $stmt->bindParam(':fecha_instalacion', $fecha_instalacion);
            $stmt->bindParam(':estado', $estado_medidor);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al asignar servicio a departamento: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Editar información del medidor
     */
    public function editarMedidor($id_medidor, $codigo_medidor, $fecha_instalacion, $estado_medidor) {
        try {
            $sql = "UPDATE medidor SET codigo = :codigo, fecha_instalacion = :fecha_instalacion, 
                    estado = :estado WHERE id_medidor = :id_medidor";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':codigo', $codigo_medidor);
            $stmt->bindParam(':fecha_instalacion', $fecha_instalacion);
            $stmt->bindParam(':estado', $estado_medidor);
            $stmt->bindParam(':id_medidor', $id_medidor);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al editar medidor: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar medidor (asignación)
     */
    public function eliminarMedidor($id_medidor) {
        try {
            // Primero eliminar las lecturas de consumo asociadas
            $sql_lecturas = "DELETE FROM lector_sensor_consumo WHERE id_medidor = :id_medidor";
            $stmt_lecturas = $this->db->prepare($sql_lecturas);
            $stmt_lecturas->bindParam(':id_medidor', $id_medidor);
            $stmt_lecturas->execute();

            // Luego eliminar el historial de consumo
            $sql_historial = "DELETE FROM historial_consumo WHERE id_medidor = :id_medidor";
            $stmt_historial = $this->db->prepare($sql_historial);
            $stmt_historial->bindParam(':id_medidor', $id_medidor);
            $stmt_historial->execute();

            // Finalmente eliminar el medidor
            $sql_medidor = "DELETE FROM medidor WHERE id_medidor = :id_medidor";
            $stmt_medidor = $this->db->prepare($sql_medidor);
            $stmt_medidor->bindParam(':id_medidor', $id_medidor);

            return $stmt_medidor->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar medidor: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si ya existe un medidor con el mismo código
     */
    public function existeMedidor($codigo_medidor, $excluir_id = null) {
        try {
            $sql = "SELECT id_medidor FROM medidor WHERE codigo = :codigo";
            if ($excluir_id) {
                $sql .= " AND id_medidor != :excluir_id";
            }

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':codigo', $codigo_medidor);
            if ($excluir_id) {
                $stmt->bindParam(':excluir_id', $excluir_id);
            }
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar medidor: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si ya existe una asignación de servicio a departamento
     */
    public function existeAsignacion($id_departamento, $id_servicio) {
        try {
            $sql = "SELECT id_medidor FROM medidor 
                    WHERE id_departamento = :id_departamento AND id_servicio = :id_servicio";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_departamento', $id_departamento);
            $stmt->bindParam(':id_servicio', $id_servicio);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar asignación: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generar consumo individual (registrar lectura de medidor)
     */
    public function generarConsumo($id_medidor, $consumo, $fecha_hora = null) {
        try {
            if ($fecha_hora === null) {
                $fecha_hora = date('Y-m-d H:i:s');
            }

            $sql = "INSERT INTO lector_sensor_consumo (id_medidor, fecha_hora, consumo) 
                    VALUES (:id_medidor, :fecha_hora, :consumo)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_medidor', $id_medidor);
            $stmt->bindParam(':fecha_hora', $fecha_hora);
            $stmt->bindParam(':consumo', $consumo);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al generar consumo individual: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generar consumos masivos para un mes completo
     */
    public function generarConsumosMasivos($year, $month, $id_departamento = null) {
        try {
            // Obtener departamentos
            if ($id_departamento) {
                $sql_deptos = "SELECT id_departamento, numero, piso FROM departamento 
                              WHERE id_departamento = :id_departamento";
                $stmt_deptos = $this->db->prepare($sql_deptos);
                $stmt_deptos->bindParam(':id_departamento', $id_departamento);
                $stmt_deptos->execute();
                $departamentos = $stmt_deptos->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $sql_deptos = "SELECT id_departamento, numero, piso FROM departamento 
                              ORDER BY piso, numero";
                $stmt_deptos = $this->db->prepare($sql_deptos);
                $stmt_deptos->execute();
                $departamentos = $stmt_deptos->fetchAll(PDO::FETCH_ASSOC);
            }

            $total_general = 0;
            $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            // Rangos por servicio
            $rangos = [
                'agua' => ['min' => 0.1, 'max' => 1.5],
                'luz' => ['min' => 0.1, 'max' => 2.0],
                'gas' => ['min' => 0.01, 'max' => 0.05]
            ];

            foreach ($departamentos as $depto) {
                // Obtener medidores del departamento
                $medidores = $this->obtenerMedidoresDepartamento($depto['id_departamento']);

                if (empty($medidores)) {
                    continue;
                }

                $batch_size = 50;
                $values = [];

                // Generar todos los valores para el mes
                for ($day = 1; $day <= $days_in_month; $day++) {
                    $fecha_hora = sprintf("%04d-%02d-%02d 23:59:00", $year, $month, $day);

                    foreach ($medidores as $medidor) {
                        $servicio = strtolower(trim($medidor['servicio'] ?? ''));
                        
                        // Validar que el servicio existe en los rangos
                        if (!isset($rangos[$servicio])) {
                            error_log("Servicio no encontrado en rangos: " . $servicio);
                            continue; // Saltar este medidor si el servicio no está en los rangos
                        }
                        
                        $rango = $rangos[$servicio];

                        // Generar consumo según el rango específico del servicio
                        $min = $rango['min'] * 100;
                        $max = $rango['max'] * 100;
                        $consumo = round(mt_rand($min, $max) / 100, 2);

                        $values[] = [
                            'id_medidor' => $medidor['id_medidor'],
                            'fecha_hora' => $fecha_hora,
                            'consumo' => $consumo
                        ];
                    }
                }

                // Insertar en lotes
                $total_inserted = 0;
                $chunks = array_chunk($values, $batch_size);

                foreach ($chunks as $chunk) {
                    $placeholders = [];
                    $insert_values = [];

                    foreach ($chunk as $index => $value) {
                        $placeholders[] = "(:id_medidor{$index}, :fecha_hora{$index}, :consumo{$index})";
                        $insert_values[":id_medidor{$index}"] = $value['id_medidor'];
                        $insert_values[":fecha_hora{$index}"] = $value['fecha_hora'];
                        $insert_values[":consumo{$index}"] = $value['consumo'];
                    }

                    $sql = "INSERT INTO lector_sensor_consumo (id_medidor, fecha_hora, consumo) 
                            VALUES " . implode(", ", $placeholders);
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute($insert_values);
                    $total_inserted += $stmt->rowCount();
                }

                $total_general += $total_inserted;
            }

            return $total_general;

        } catch (PDOException $e) {
            error_log("Error al generar consumos masivos: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener medidores de un departamento
     */
    public function obtenerMedidoresDepartamento($id_departamento) {
        try {
            $sql = "SELECT m.*, s.nombre as servicio, s.unidad_medida, s.costo_unitario
                    FROM medidor m 
                    JOIN servicio s ON m.id_servicio = s.id_servicio 
                    WHERE m.id_departamento = :id_departamento 
                    ORDER BY s.nombre";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_departamento', $id_departamento);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener medidores del departamento: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener todos los departamentos
     */
    public function obtenerDepartamentos() {
        try {
            $sql = "SELECT id_departamento, numero, piso FROM departamento ORDER BY piso, numero";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener departamentos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Ver historial de consumo por medidor
     */
    public function verHistorialConsumo($id_medidor, $fecha_inicio = null, $fecha_fin = null) {
        try {
            $sql = "SELECT lc.*, m.codigo as codigo_medidor, s.nombre as servicio, 
                           s.unidad_medida, d.numero as departamento
                    FROM lector_sensor_consumo lc
                    INNER JOIN medidor m ON lc.id_medidor = m.id_medidor
                    INNER JOIN servicio s ON m.id_servicio = s.id_servicio
                    INNER JOIN departamento d ON m.id_departamento = d.id_departamento
                    WHERE lc.id_medidor = :id_medidor";

            $params = [':id_medidor' => $id_medidor];

            if ($fecha_inicio && $fecha_fin) {
                $sql .= " AND DATE(lc.fecha_hora) BETWEEN :fecha_inicio AND :fecha_fin";
                $params[':fecha_inicio'] = $fecha_inicio;
                $params[':fecha_fin'] = $fecha_fin;
            }

            $sql .= " ORDER BY lc.fecha_hora DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener historial de consumo: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Generar resumen en historial_consumo
     */
    public function generarResumenConsumo($id_medidor, $fecha_inicio, $fecha_fin) {
        try {
            // Calcular consumo total en el período
            $sql_consumo = "SELECT SUM(consumo) as consumo_total 
                           FROM lector_sensor_consumo 
                           WHERE id_medidor = :id_medidor 
                           AND fecha_hora BETWEEN :fecha_inicio AND :fecha_fin";
            $stmt = $this->db->prepare($sql_consumo);
            $stmt->bindParam(':id_medidor', $id_medidor);
            $stmt->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt->bindParam(':fecha_fin', $fecha_fin);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $consumo_total = $result['consumo_total'] ?? 0;

            // Insertar en historial_consumo
            $sql_insert = "INSERT INTO historial_consumo (id_medidor, fecha_inicio, fecha_fin, consumo_total) 
                          VALUES (:id_medidor, :fecha_inicio, :fecha_fin, :consumo_total)";
            $stmt_insert = $this->db->prepare($sql_insert);
            $stmt_insert->bindParam(':id_medidor', $id_medidor);
            $stmt_insert->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt_insert->bindParam(':fecha_fin', $fecha_fin);
            $stmt_insert->bindParam(':consumo_total', $consumo_total);

            return $stmt_insert->execute();
        } catch (PDOException $e) {
            error_log("Error al generar resumen de consumo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si ya existe un servicio con el mismo nombre
     */
    public function existeServicio($nombre) {
        try {
            $sql = "SELECT id_servicio FROM servicio WHERE nombre = :nombre";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar servicio: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener medidores por servicio
     */
    public function obtenerMedidoresPorServicio($id_servicio) {
        try {
            $sql = "SELECT m.*, d.numero as departamento, s.nombre as servicio
                    FROM medidor m
                    INNER JOIN departamento d ON m.id_departamento = d.id_departamento
                    INNER JOIN servicio s ON m.id_servicio = s.id_servicio
                    WHERE m.id_servicio = :id_servicio AND m.estado = 'activo'";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_servicio', $id_servicio);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener medidores: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener todos los medidores activos
     */
    public function obtenerTodosMedidores() {
        try {
            $sql = "SELECT m.*, d.numero as departamento, d.piso, s.nombre as servicio, s.unidad_medida
                    FROM medidor m
                    INNER JOIN departamento d ON m.id_departamento = d.id_departamento
                    INNER JOIN servicio s ON m.id_servicio = s.id_servicio
                    ORDER BY d.piso, d.numero, s.nombre";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener todos los medidores: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener medidor por ID
     */
    public function obtenerMedidorPorId($id_medidor) {
        try {
            $sql = "SELECT m.*, d.numero as departamento, d.piso, s.nombre as servicio, s.unidad_medida
                    FROM medidor m
                    INNER JOIN departamento d ON m.id_departamento = d.id_departamento
                    INNER JOIN servicio s ON m.id_servicio = s.id_servicio
                    WHERE m.id_medidor = :id_medidor";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_medidor', $id_medidor);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener medidor: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Eliminar una lectura individual de consumo
     */
    public function eliminarLectura($id_lectura) {
        try {
            $sql = "DELETE FROM lector_sensor_consumo WHERE id_lectura = :id_lectura";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_lectura', $id_lectura);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar lectura: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener una lectura por ID
     */
    public function obtenerLecturaPorId($id_lectura) {
        try {
            $sql = "SELECT * FROM lector_sensor_consumo WHERE id_lectura = :id_lectura";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_lectura', $id_lectura);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener lectura: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener último consumo de un medidor
     */
    public function obtenerUltimoConsumo($id_medidor) {
        try {
            $sql = "SELECT * FROM lector_sensor_consumo 
                    WHERE id_medidor = :id_medidor 
                    ORDER BY fecha_hora DESC 
                    LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_medidor', $id_medidor);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener último consumo: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener historial de consumo reciente de un medidor
     */
    public function obtenerHistorialConsumoReciente($id_medidor, $limite = 5) {
        try {
            $sql = "SELECT * FROM lector_sensor_consumo 
                    WHERE id_medidor = :id_medidor 
                    ORDER BY fecha_hora DESC 
                    LIMIT :limite";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_medidor', $id_medidor);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener historial reciente: " . $e->getMessage());
            return [];
        }
    }
}
?>