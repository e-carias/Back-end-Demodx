<?php
require_once('../helpers/validaciones.php');
require_once('../helpers/connection.php');
require_once('../models/model_movimiento.php');

if (isset($_GET['action'])) {
    session_start();
    $movimiento = new Movimiento ;
    $result = array('status' => 0, 'message' => null, 'exception' => null, 'dataset' => null);
    if (isset($_SESSION['id_cajero']) or true) {
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
            case 'readOne':
                if (!$movimiento->setId($_POST['id_cliente'])) {
                    $result['exception'] = 'Usuario incorrecto';
                } elseif ($result['dataset'] = $movimiento->readOne()) {
                    $result['status'] = 1;
                } elseif (Connection::getException()) {
                    $result['exception'] = Connection::getException();
                } else {
                    $result['exception'] = 'Usuario inexistente';
                }
                break;
            case 'update':
                $_POST = Validator::validateForm($_POST);
                if (!$movimiento->setId($_POST['id'])) {
                    $result['exception'] = 'cliente incorrecto';
                } elseif (!$movimiento->readOne()) {
                    $result['exception'] = 'cliente inexistente';
                } elseif (!$movimiento->setNcuenta($_POST['ncuenta'])) {
                    $result['exception'] = 'Numero de cuenta incorrecta';
                }elseif (!$movimiento->setMonto($_POST['monto'])) {
                    $result['exception'] = 'Monto no válido';
                } elseif ($movimiento->updateRow() && $movimiento->asignarCajero()) {
                    $result['status'] = 1;
                    $result['message'] = 'Monto modificado correctamente';
                } else {
                    $result['exception'] = Connection::getException();
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