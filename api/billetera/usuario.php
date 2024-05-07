<?php
require_once('../helpers/validaciones.php');
require_once('../helpers/connection.php');
require_once('../models/model_usuario.php');

if (isset($_GET['action'])) {
    session_start();
    $usuario = new Usuario;
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'exception' => null, 'dataset' => null, 'username' => null);
    if (isset($_SESSION['id_usuario'])) {
        $result['session'] = 1;
        switch ($_GET['action']) {            
            case 'getUser':
                if (isset($_SESSION['usuario'])) {
                    $result['status'] = 1;
                    $result['username'] = $_SESSION['usuario'];
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
            case 'readMonto':
                if ($result['dataset'] = $usuario->readMonto()) {
                    $result['status'] = 1;
                    $result['message'] ='Se cargo el monto correctamente';
                } else {
                    $result['exception'] = Connection::getException();
                }
                break;            
            default:
                $result['exception'] = 'Acción no disponible dentro de la sesión';
                break;
        }
    } else {
        switch ($_GET['action']) {
            case 'readUsers':
                if ($usuario->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Debe autenticarse para ingresar';
                } else {
                    $result['status'] = 1;
                    $result['message'] = 'Debe autenticarse para ingresar';
                }
                break;
            case 'signup':
                $_POST = Validator::validateForm($_POST);
                if (!$usuario->setNombre($_POST['nombre'])) {
                    $result['exception'] = 'Nombres incorrectos';
                } elseif (!$usuario->setApellido($_POST['apellido'])) {
                    $result['exception'] = 'Apellidos incorrectos';
                } elseif (!$usuario->setCorreo($_POST['correo'])) {
                    $result['exception'] = 'Correo incorrecto';
                } elseif (!$usuario->setAlias($_POST['alias'])) {
                    $result['exception'] = 'Usuario incorrecto';
                } elseif ($_POST['codigo'] != $_POST['confirmar']) {
                    $result['exception'] = 'Claves diferentes';
                } elseif (!$usuario->setClave($_POST['confirmar'])) {
                    $result['exception'] = Validator::getpassError();
                } elseif ($usuario->createRow()) {
                    $result['message'] = 'Usuario registrado correctamente';
                    if (!$usuario->checkUser($_POST['alias'])) {
                        $result['exception'] = 'Usuario o contraseña incorrecta';
                    }elseif (!$usuario->generarCuenta()) {
                        $result['exception'] = 'Error al crear cuenta';
                    }elseif (!$usuario->asignarCuenta()) {
                        $result['exception'] = 'Error al crear cuenta';
                    }elseif ($usuario->checkPassword($_POST['confirmar'])) {
                        $result['status'] = 1;
                        $result['message'] = 'Autenticación correcta';
                        $_SESSION['id_usuario'] = $usuario->getId();
                        $_SESSION['usuario'] = $usuario->getAlias();
                        $_SESSION['ncuenta'] = $usuario->getNcuenta();
                    }
                } else {
                    $result['exception'] = Connection::getException();
                }
                break;            
            case 'login':
                $_POST = Validator::validateForm($_POST);
                if (!$usuario->checkUser($_POST['alias'])) {
                    $result['exception'] = 'Usuario o contraseña incorrecta';
                }elseif (!$usuario->checkNcuenta($_POST['alias'])) {
                    $result['exception'] = 'Usuario o contraseña incorrecta';
                }elseif ($usuario->checkPassword($_POST['clave'])) {
                    $result['status'] = 1;
                    $result['message'] = 'Autenticación correcta';
                    $_SESSION['id_usuario'] = $usuario->getId();
                    $_SESSION['usuario'] = $usuario->getAlias();
                    $_SESSION['ncuenta'] = $usuario->getNcuenta();
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