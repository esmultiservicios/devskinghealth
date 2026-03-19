<?php
class BaseDataAccess {
    private $conn;

    public function __construct() {
        // Incluir el archivo de configuración
        require_once '../conf/configAPP.php';
        
        // Crear la conexión
        $this->conn = new mysqli(SERVER, USER, PASS, DB);

        // Verificar la conexión
        if ($this->conn->connect_error) {
            die("Conexión fallida: {$this->conn->connect_error}");
        }

        // Establecer el conjunto de caracteres a UTF-8
        if (!$this->conn->set_charset("utf8")) {
            die("Error al cargar el conjunto de caracteres utf8: {$this->conn->error}");
        }
    }

    // Método para ejecutar consultas que no retornan datos
    public function executeNonQuery($query, $parameters) {
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            die("Error en la preparación de la consulta: {$this->conn->error}");
        }

        // Vincular los parámetros
        $types = str_repeat('s', count($parameters)); // Asumimos que todos los parámetros son strings
        $paramValues = array_values($parameters); // Almacenar en una variable
        $stmt->bind_param($types, ...$paramValues);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            die("Error en la ejecución de la consulta: {$stmt->error}");
        }
    }

    // Método para ejecutar consultas que retornan datos
    public function executeScalarQuery($query, $parameters) {   
        $stmt = $this->conn->prepare($query);
    
        if (!$stmt) {
            die("Error en la preparación de la consulta: {$this->conn->error}");
        }
    
        // Si hay parámetros, vinculamos
        if (!empty($parameters)) {
            $types = $this->getParamTypes($parameters);
            $paramValues = array_values($parameters);
            $stmt->bind_param($types, ...$paramValues);
        }
    
        // Ejecutar la consulta
        $stmt->execute();
        $result = $stmt->get_result();
    
        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
    
        $stmt->close();
        return $data;
    }

    // Método para obtener tipos de parámetros
    private function getParamTypes($parameters) {
        $types = '';
        foreach ($parameters as $param) {
            switch (gettype($param)) {
                case 'integer':
                    $types .= 'i'; // Integer
                    break;
                case 'double':
                    $types .= 'd'; // Double
                    break;
                case 'string':
                    $types .= 's'; // String
                    break;
                case 'boolean':
                    $types .= 'i'; // Boolean se trata como Integer
                    break;
                default:
                    $types .= 's'; // Default to string
                    break;
            }
        }
        return $types;
    }

     // Método para obtener el siguiente correlativo
     public function getCorrelativo($field, $table) {
        // Consultar el máximo valor del campo
        $query = "SELECT MAX($field) AS max_value FROM $table";
        
        $result = $this->executeScalarQuery($query, []);
    
        if (empty($result)) {
            return 1; // Si no hay registros, el correlativo empieza en 1
        }
    
        $max_value = $result[0]['max_value'];
        return $max_value + 1;
    }    
}