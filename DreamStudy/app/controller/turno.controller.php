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

        $this->view->formExitoso([
            'nombre' => $nombre,
            'apellido' => $apellido,
            'servicio' => $servicio,
            'fecha' => $fecha,
            'horario' => $horario,
        ]);
        exit();
    }

    public function showTurnos() {
        if (!AuthHelper::verify()) {
            header('Location: ' . BASE_URL . 'index.php?action=login');
            exit();
        }
    
        // Obtener los turnos desde el modelo
        $turnos = $this->model->obtenerTurnos();
    
        // Inicializar un array para agrupar los turnos por fecha
        $turnosAgrupados = [];
        $diasEnEspanol = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' =>'Domingo'
        ];
    
        // Agrupar los turnos por fecha exacta
        foreach ($turnos as $turno) {
            $diaIngles = date('l', strtotime($turno->fecha)); 
            $diaEspanol = $diasEnEspanol[$diaIngles];
            $fechaFormateada = date('d-m-Y', strtotime($turno->fecha)); // Fecha en formato dd-mm-aaaa
    
            // Crear una clave que combine el día en español y la fecha
            $diaYFecha = $diaEspanol . ' ' . $fechaFormateada;
            $turnosAgrupados[$diaYFecha][] = $turno;
        }
    
        // Ordenar los turnos por fecha ascendente
        uksort($turnosAgrupados, function($a, $b) {
            // Extraer las fechas de los strings "Lunes 01-01-2024"
            $fechaA = DateTime::createFromFormat('d-m-Y', substr($a, strpos($a, ' ') + 1));
            $fechaB = DateTime::createFromFormat('d-m-Y', substr($b, strpos($b, ' ') + 1));
            return $fechaA <=> $fechaB; // Comparar las fechas
        });
    
        // Pasar los turnos agrupados y ordenados a la vista
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
    public function cancelarTurno() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener el ID del turno a cancelar
            $turno_id = $_POST['turno_id'];
            error_log("ID del turno a cancelar: " . $turno_id); // Verificación
    
            // Llamar al modelo para eliminar el turno
            if ($this->model->deleteTurno($turno_id)) {
                $_SESSION['mensaje'] = "Turno cancelado con éxito.";
            } else {
                $_SESSION['mensaje'] = "Error al cancelar el turno.";
            }
            header('Location: index.php?action=turnos');
            exit;
        }
    }
    public function cancelar() {
        $fecha = $_GET['fecha'];
        $horario = $_GET['horario'];
    
        $this->model->eliminarTurno($fecha, $horario);
        header("Location: /DreamStudy/index.php?action=reservar");
        exit();
    }
    public function reservarTodoDia() {
        if (isset($_POST['fecha'])) {
            $fecha = $_POST['fecha'];
            $this->model->reservarTodosLosTurnosDeDia($fecha);
            $_SESSION['mensaje'] = "Todos los turnos del día $fecha han sido reservados.";
        } else {
            $_SESSION['mensaje'] = "No se pudo reservar el día.";
        }
        header('Location: index.php?action=turnos');
        exit();
    }
    
    
    
    

}
?>
