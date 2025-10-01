<?php
require_once '../config/db.php'; // Asegúrate de que esta ruta es correcta

class ClienteController {
    private $pdo;

    public function __construct() {
        global $pdo; // Usamos la variable global $pdo definida en db.php
        $this->pdo = $pdo;
    }

    // Obtener todos los clientes
    public function obtenerClientes() {
        $query = "SELECT tipo_documento_cliente, documento_cliente, nombres_cliente, apellidos_cliente, correo_cliente, telefono_cliente, descripcion_cliente, fecha_creacion_cliente FROM clientes";
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }    

    // Obtener un cliente por ID
    public function obtenerClientePorId($id) {
        $stmt = $this->pdo->prepare("SELECT tipo_documento_cliente, documento_cliente, nombres_cliente, apellidos_cliente, correo_cliente, telefono_cliente, descripcion_cliente, fecha_creacion_cliente FROM clientes WHERE id_cliente = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar datos del cliente
    public function actualizarCliente($id, $tipo_doc, $documento, $nombres, $apellidos, $correo, $telefono, $descripcion) {
        $stmt = $this->pdo->prepare("UPDATE clientes SET tipo_documento_cliente = ?, documento_cliente = ?, nombres_cliente = ?, apellidos_cliente = ?, correo_cliente = ?, telefono_cliente = ?, descripcion_cliente = ? WHERE id_cliente = ?");
        return $stmt->execute([$tipo_doc, $documento, $nombres, $apellidos, $correo, $telefono, $descripcion, $id]);
    }

    // Eliminar un cliente
    public function eliminarCliente($id) {
        $stmt = $this->pdo->prepare("DELETE FROM clientes WHERE documento_cliente = ?");
        return $stmt->execute([$id]);
    }
}
