<?php
require_once './app/model/turno.model.php';
require_once './app/helpers/auth.helper.php';
require_once './app/view/turno.view.php';

class TurnoController {
    private $model;
    private $view;

    public function __construct() {
        $this->model = new TurnoModel();
        $this->view = new TurnoView();
    }

    public function mostrarFormulario() {
        $this->view->showForm(); // Llama a la vista que muestra el formulario
    }
    
    public function reservar() {
        // Obtener los datos del formulario
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];
        $servicio = $_POST['servicio'];
        $fecha = $_POST['fecha'];
        $horario = $_POST['horario'];

        // Verificar si el turno ya está reservado
        if ($this->model->verificarTurnoReservado($fecha, $horario)) {
            $this->view->formFallido();
            return;
        }

        // Reservar el turno
        $this->model->guardarTurno($nombre, $apellido, $telefono, $email, $servicio, $fecha, $horario);

        $this->view->formExitoso();
        exit();
    }

    public function showTurnos() {
        if (!AuthHelper::verify()) {
            header('Location: ' . BASE_URL . 'index.php?action=login');
            exit();
        }
        
        $turnos = $this->model->obtenerTurnos();
        
        $turnosAgrupados = [];
        $diasEnEspanol = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado'
        ];
    
        foreach ($turnos as $turno) {
            $dia = date('l', strtotime($turno->fecha)); 
            if (array_key_exists($dia, $diasEnEspanol)) { 
                $turnosAgrupados[$diasEnEspanol[$dia]][] = $turno; 
            }
        }
        $this->view->showTurnos($turnosAgrupados);
    }

    // Método para obtener horarios disponibles
    public function obtenerHorariosReservados() {
        if (isset($_GET['fecha'])) {
            $fecha = $_GET['fecha'];
    
            // Llama al modelo para obtener los horarios reservados
            $horariosReservados = $this->model->getHorariosReservadosPorFecha($fecha);
    
            // Formatear los horarios reservados para solo obtener la parte de la hora
            $horariosReservados = array_map(function($horario) {
                return date('H:i', strtotime($horario)); // Formato 'HH:MM'
            }, $horariosReservados);
    
            // Lista de horarios posibles
            $todosLosHorarios = ['14:00', '14:40', '15:20', '16:00', '16:40', '17:20', '18:00', '18:40', '19:20', '20:00'];
    
            // Filtrar los horarios disponibles
            $horariosDisponibles = array_diff($todosLosHorarios, $horariosReservados);
    
            // Devolver los horarios disponibles como JSON
            header('Content-Type: application/json');
            echo json_encode(array_values($horariosReservados)); // Cambia esto para devolver solo los reservados si es necesario
            exit();
        }
    }
    
    
    
    
    

    
    
}
?>
