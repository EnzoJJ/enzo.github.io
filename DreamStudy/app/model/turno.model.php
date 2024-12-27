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
    public function deleteTurno($turno_id) {
        $query = $this->db->prepare('DELETE FROM turnos WHERE id_turno = :turno_id');
        $query->bindParam(':turno_id', $turno_id, PDO::PARAM_INT);
        
        if ($query->execute()) {
            return true; 
        } else {
            error_log("Error al eliminar el turno: " . implode(", ", $query->errorInfo())); // Manejo de errores
            return false; 
        }
    }
    public function eliminarTurno($fecha, $horario) {
        $sql = "DELETE FROM turnos WHERE fecha = ? AND horario = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$fecha, $horario]);
    }
    public function guardarDiaBloqueado($fecha) {
        $query = $this->db->prepare('INSERT INTO dias_bloqueados (fecha) VALUES (?)');
        return $query->execute([$fecha]);
    }
    public function reservarTodosLosTurnosDeDia($fecha) {
        // Asume que hay un horario predefinido para los turnos
        $horarios = ["14:00",
        "14:40",
        "15:20",
        "16:00",
        "16:40",
        "17:20",
        "18:00",
        "18:40",
        "19:20",
        "20:00",];
        foreach ($horarios as $horario) {
            $query = "INSERT INTO turnos (nombre, apellido, email, telefono, servicio, fecha, horario)
                      VALUES ('Cerrado', '', '', '', 'Día bloqueado', :fecha, :horario)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':horario', $horario);
            $stmt->execute();
        }
    }
    
    
}
?>
