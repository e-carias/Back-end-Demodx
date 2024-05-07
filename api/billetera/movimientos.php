<?php
require_once('../helpers/validaciones.php');
require_once('../helpers/connection.php');
require_once('../models/model_movimiento.php');

if (isset($_GET['action'])) {
    session_start();
    $movimiento = new Movimiento ;
    $result = array('status' => 0, 'message' => null, 'exception' => null, 'dataset' => null);
    if (isset($_SESSION['id_usuario']) or true) {
        switch ($_GET['action']) {
            case 'readAll':
                if ($result['dataset'] = $movimiento->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Movimientos cargados correctamente';              
                } elseif (Connection::getException()) {
                    $result['exception'] = Connection::getException();
                } else {
                    $result['exception'] = 'No hay movimientos';
                }
                break;                
            default:
                $result['exception'] = 'Acción no disponible fuera de la sesión';
                break;
        }
    header('content-type: application/json; charset=utf-8');
    print(json_encode($result));
    } else {
        print(json_encode('Acceso denegado'));
    }
} else{
    print(json_encode('Recurso no disponible'));
}    