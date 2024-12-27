<?php
class TurnoModel {
    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;dbname=peluquerialeo;charset=utf8', 'root', '');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Verificar si un turno ya está reservado para una fecha y horario específicos
    public function verificarTurnoReservado($fecha, $horario) {
        $query = $this->db->prepare('SELECT * FROM turnos WHERE fecha = ? AND horario = ?');
        $query->execute([$fecha, $horario]);
        return $query->fetch(PDO::FETCH_ASSOC); // Devuelve true si existe
    }

    // Guardar un nuevo turno en la base de datos
    public function guardarTurno($nombre, $apellido, $telefono, $email, $servicio, $fecha, $horario) {
        $query = $this->db->prepare('INSERT INTO turnos (nombre, apellido, telefono, email, servicio, fecha, horario) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $query->execute([$nombre, $apellido, $telefono, $email, $servicio, $fecha, $horario]);
    }
    public function obtenerTurnos() {
        $query = $this->db->prepare('SELECT * FROM turnos');
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }  
    public function getHorariosReservadosPorFecha($fecha) {
        $query = "SELECT horario FROM turnos WHERE fecha = :fecha"; // Ajusta esto a tu esquema de base de datos
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->execute();
    
        // Retorna los horarios reservados en un array plano
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: []; // Asegúrate de retornar un array vacío si no hay resultados
    }
    
    
    
    

    
}
?>
