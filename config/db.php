<?php
// db.php - Conexión a la base de datos

// Parámetros de configuración
$host = 'localhost'; // Cambia esto si tu base de datos está en otro servidor
$dbname = 'posadalasmandarinas_db'; // Nombre de tu base de datos
$username = 'root'; // Usuario de la base de datos
$password = ''; // Contraseña de la base de datos (si aplica)

try {
    // Crear una instancia PDO para conectarse a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Establecer el modo de error para PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En caso de error en la conexión, mostrar el mensaje
    echo "Error de conexión: " . $e->getMessage();
    exit;
}
?>
