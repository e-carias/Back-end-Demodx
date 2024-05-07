<?php

class Usuario
{
    protected $id = null;
    protected $nombre = null;
    protected $apellido = null;
    protected $correo = null;
    protected $alias = null;
    protected $clave = null;
    protected $ncuenta = null;

    public function setId($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setNombre($value)
    {
        if (Validator::validateAlphabetic($value, 1, 60)) {
            $this->nombre = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setApellido($value)
    {
        if (Validator::validateAlphabetic($value, 1, 60)) {
            $this->apellido = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setCorreo($value)
    {
        if (Validator::validateEmail($value)) {
            $this->correo = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setAlias($value)
    {
        if (Validator::validateAlphanumeric($value, 1, 40)) {
            $this->alias = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setClave($value)
    {
        if (Validator::validatePassword($value, $this->nombre, $this->apellido, $this->alias)) {
            $this->clave = password_hash($value, PASSWORD_DEFAULT);
            return true;
        } else {
            return false;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getApellido()
    {
        return $this->apellido;
    }

    public function getCorreo()
    {
        return $this->correo;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function getClave()
    {
        return $this->clave;
    }
    public function getNcuenta()
    {
        return $this->ncuenta;
    }

    public function readAll()
    {
        $sql = 'SELECT id_cliente, nombre, apellido, usuario, correo, contrasena FROM cliente ORDER BY nombre';
        return Connection::getRows($sql, null);
    }

    public function checkUser($alias)
    {
        $sql = 'SELECT id_cliente FROM cliente
                WHERE usuario = ?';
        $params = array($alias);
        if ($data = Connection::getRow($sql, $params)) {
            $this->id = $data['id_cliente'];
            $this->alias = $alias;
            return true;
        } else {
            return false;
        }
    }

    public function checkNcuenta($alias)
    {
        $sql = 'SELECT C.n_cuenta FROM cuenta C JOIN relacion_clientecuenta RCC ON RCC.id_cuenta = C.id_cuenta 
                WHERE RCC.id_cliente = (SELECT id_cliente FROM cliente WHERE usuario = ?);';
        $params = array($alias);
        if ($data = Connection::getRow($sql, $params)) {
            $this->ncuenta = $data['n_cuenta'];
            $this->alias = $alias;
            return true;
        } else {
            return false;
        }
    }

    public function checkPassword($password)
    {
        $sql = 'SELECT contrasena FROM cliente
        WHERE id_cliente = ?';
        $params = array($this->id);
        $data = Connection::getRow($sql, $params);
        if (password_verify($password, $data['contrasena'])) {
            return true;
        } else {            
            return false;
        }
    }

    public function generarCuenta()
    {
        $longitud = 8;
        $cadena_numeros = '';

        for ($i = 0; $i < $longitud; $i++) {
            $numero_aleatorio = rand(0, 9);
            $cadena_numeros .= $numero_aleatorio;
        }      
        $sql = 'INSERT INTO cuenta( n_cuenta, saldo_cuenta) VALUES (?,?)';
        $this->ncuenta = $cadena_numeros;
        $params = array($cadena_numeros, 0);
        return Connection::executeRow($sql, $params);
    }

    public function createRow()
    {        
        $sql = 'INSERT INTO cliente(nombre, apellido, correo, usuario, contrasena)
                VALUES(?, ?, ?, ?, ?)';
        $params = array($this->nombre, $this->apellido, $this->correo, $this->alias, $this->clave);
        return Connection::executeRow($sql, $params);
    }

    public function asignarCuenta()
    {        
        $sql = 'INSERT INTO relacion_clientecuenta( id_cliente, id_cuenta) VALUES (?, (SELECT id_cuenta FROM cuenta WHERE n_cuenta = ?))';
        $params = array($this->id, $this->ncuenta);
        return Connection::executeRow($sql, $params);
    }

    public function readMonto()
    {        
        $sql = 'SELECT C.saldo_cuenta FROM relacion_clientecuenta RCC, cuenta C WHERE C.n_cuenta = ? AND RCC.id_cliente = ?';
        $params = array($_SESSION['ncuenta'], $_SESSION['id_usuario']);
        return Connection::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE usuarios 
                SET nombre_usuario = ?, apellido_usuario = ?, correo = ?
                WHERE id_usuario = ?';
        $params = array($this->nombres, $this->apellidos, $this->correo, $this->id);
        return Connection::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM usuarios
                WHERE id_usuario = ?';
        $params = array($this->id);
        return Connection::executeRow($sql, $params);
    }
}
?>