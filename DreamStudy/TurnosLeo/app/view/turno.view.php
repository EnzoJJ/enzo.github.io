<?php
class TurnoView{
    public function formExitoso() {
        include './templates/postTurno.phtml';
    }
    public function showTurnos($turnosAgrupados) {
        include './templates/turnos.phtml';
    }
    public function showForm(){
        include './templates/reservar.phtml';
    }
}

?>