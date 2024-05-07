<?php
require_once('../helpers/validaciones.php');
require_once('../helpers/connection.php');
require_once('../models/model_cajero.php');

if (isset($_GET['action'])) {
    session_start();
    $cajero = new Cajero;
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'exception' => null, 'dataset' => null, 'username' => null);
    if (isset($_SESSION['id_cajero'])) {
        $result['session'] = 1;
        switch ($_GET['action']) {            
            case 'getUser':
                if (isset($_SESSION['cajero'])) {
                    $result['status'] = 1;
                    $result['username'] = $_SESSION['cajero'];
                } else {
                    $result['exception'] = 'Alias de usuario indefinido';
                }
                break;
            case 'logOut':
                if (session_destroy()) {
                    $result['status'] = 1;
                    $result['message'] = 'Sesión eliminada correctamente';
                } else {
                    $result['exception'] = 'Ocurrió un problema al cerrar la sesión';
                }
                break;            
            case 'search':
                $_POST = Validator::validateForm($_POST);
                if ($_POST['buscar'] == '') {
                    $result['exception'] = 'Ingrese un valor para buscar';
                } elseif ($result['dataset'] = $cajero->searchRows($_POST['buscar'])) {
                    $result['status'] = 1;
                } elseif (Connection::getException()) {
                    $result['exception'] = Connection::getException();
                } else {
                    $result['exception'] = 'No hay coincidencias';
                }
                break;            
            default:
                $result['exception'] = 'Acción no disponible dentro de la sesión';
                break;
        }
    } else {
        switch ($_GET['action']) {
            case 'readUsers':
                if ($cajero->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Debe autenticarse para ingresar';
                } else {
                    $result['status'] = 1;
                    $result['message'] = 'Debe autenticarse para ingresar';
                }
                break;
            case 'login':
                $_POST = Validator::validateForm($_POST);
                if (!$cajero->checkUser($_POST['alias'])) {
                    $result['exception'] = 'Usuario o contraseña incorrecta';
                }elseif ($cajero->checkPassword($_POST['clave'])) {
                    $result['status'] = 1;
                    $result['message'] = 'Autenticación correcta';
                    $_SESSION['id_cajero'] = $cajero->getId();
                    $_SESSION['cajero'] = $cajero->getAlias();
                } else {
                    $result['exception'] = Connection::getException();
                }
                break;
            default:
                $result['exception'] = 'Acción no disponible fuera de la sesión';
                break;
        }
    }
    header('content-type: application/json; charset=utf-8');
    print(json_encode($result));
} else {
    print(json_encode('Recurso no disponible'));
}
?>