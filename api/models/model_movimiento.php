<?php
class Movimiento
{
    protected $id_cliente = null;
    protected $n_cuenta = null;
    protected $monto_cuenta = null;

    public function setId($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_cliente = $value;
            return true;
        } else {
            return false;
        }
    }
    public function setNcuenta($value)
    {
        if (Validator::validateStock($value)) {
            $this->n_cuenta = $value;
            return true;
        } else {
            return false;
        }
    }
    public function setMonto($value)
    {
        if (Validator::validateStock($value)) {
            $this->monto_cuenta = $value;
            return true;
        } else {
            return false;
        }
    }

    public function getId()
    {
        return $this->id_cliente;
    }
    public function getNcuenta()
    {
        return $this->n_cuenta;
    }
    public function getMonto()
    {
        return $this->monto_cuenta;
    }

    public function readAll()
    {
        $sql = 'SELECT C.nombre,C.apellido,M.fecha, M.variacion FROM movimientos M, cajero C WHERE M.id_historial=(SELECT RC.id_historial FROM relacion_clientecuenta RC WHERE RC.id_cliente = ? AND RC.id_cuenta = (SELECT id_cuenta FROM cuenta WHERE n_cuenta = ?)) AND M.id_cajero=C.id_cajero ORDER BY M.id_movimiento DESC LIMIT 10;';
        $params = array($_SESSION['id_usuario'], $_SESSION['ncuenta']);
        return Connection::getRows($sql, $params);
    }

    public function readOne()
    {
        $sql = 'SELECT n_cuenta,saldo_cuenta,RC.id_cliente FROM cuenta CU
        INNER JOIN relacion_clientecuenta RC ON CU.id_cuenta=RC.id_cuenta
        WHERE RC.id_cliente = ?';
        $params = array($this->id_cliente);
        return Connection::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE cuenta 
                SET saldo_cuenta= ?
                WHERE n_cuenta = ?';
        $params = array($this->monto_cuenta, $this->n_cuenta);
        return Connection::executeRow($sql, $params);
    }
    public function asignarCajero()
    {
        $sql = 'UPDATE movimientos SET id_cajero = ? WHERE id_movimiento = (SELECT ultimo_id FROM (SELECT MAX(id_movimiento) AS ultimo_id FROM movimientos) AS ultimo_id_movimiento);';
        $params = array($_SESSION['id_cajero']);
        return Connection::executeRow($sql, $params);
    }
}
?>
