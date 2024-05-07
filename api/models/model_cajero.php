<?php

class Cajero
{
    protected $id = null;
    protected $nombre = null;
    protected $apellido = null;
    protected $correo = null;
    protected $alias = null;
    protected $clave = null;
    protected $id_movimiento = null;

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
        $sql = 'SELECT id_cajero, nombre, apellido, alias, correo, contrasena FROM cajero ORDER BY nombre';
        return Connection::getRows($sql, null);
    }

    public function checkUser($alias)
    {
        $sql = 'SELECT id_cajero FROM cajero
                WHERE alias = ?';
        $params = array($alias);
        if ($data = Connection::getRow($sql, $params)) {
            $this->id = $data['id_cajero'];
            $this->alias = $alias;
            return true;
        } else {
            return false;
        }
    }

    public function checkPassword($password)
    {
        $sql = 'SELECT contrasena FROM cajero
        WHERE id_cajero = ?';
        $params = array($this->id);
        $data = Connection::getRow($sql, $params);
        if (password_verify($password, $data['contrasena'])) {
            return true;
        } else {            
            return false;
        }
    }

    public function createRow()
    {        
        $sql = 'INSERT INTO cliente(nombre, apellido, correo, usuario, contrasena)
                VALUES(?, ?, ?, ?, ?)';
        $params = array($this->nombre, $this->apellido, $this->correo, $this->alias, $this->clave);
        return Connection::executeRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE usuarios 
                SET nombre_usuario = ?, apellido_usuario = ?, correo = ?
                WHERE id_usuario = ?';
        $params = array($this->nombres, $this->apellidos, $this->correo, $this->id);
        return Connection::executeRow($sql, $params);
    }

    public function searchRows($value)
    {
        $sql = 'SELECT CL.id_cliente, CL.usuario, CL.nombre, CL.apellido, CU.n_cuenta, CU.saldo_cuenta 
                FROM relacion_clientecuenta RCC 
                INNER JOIN cliente CL ON RCC.id_cliente=CL.id_cliente 
                INNER JOIN cuenta CU ON RCC.id_cuenta=CU.id_cuenta 
                WHERE CL.usuario LIKE ? ORDER BY CL.usuario;';
        $params = array("%$value%");
        return Connection::getRows($sql, $params);
    }
}
?>