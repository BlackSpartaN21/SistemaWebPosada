<?php
// controllers/backup.php
declare(strict_types=1);
session_start();

// --- Debug visible durante desarrollo (puedes desactivar luego) ---
ini_set('display_errors', '1');
error_reporting(E_ALL);

// --- Solo requiere sesión iniciada (sin filtro de rol) ---
$logueado = isset($_SESSION['id_usuario']) && !empty($_SESSION['id_usuario']);
if (!$logueado) {
  http_response_code(403);
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(['ok' => false, 'msg' => 'Acceso denegado: inicia sesión.']);
  exit;
}

header('Content-Type: application/json; charset=UTF-8');

// --- Cargar conexión (intenta varias rutas) ---
$loaded = false;
$tryPaths = [
  __DIR__ . '/../config/db.php',
  __DIR__ . '/../db.php',
  __DIR__ . '/../config.php',
];
foreach ($tryPaths as $p) {
  if (is_file($p)) { require_once $p; $loaded = true; break; }
}
if (!$loaded) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'msg' => 'No se encontró archivo de conexión (busqué en config/db.php, db.php, config.php)']);
  exit;
}

// --- Detectar tipo de conexión (estricto) ---
$pdo    = (isset($pdo)    && $pdo    instanceof PDO)    ? $pdo    : null;   // PDO válido
$conn   = (isset($conn)   && $conn   instanceof mysqli) ? $conn   : null;   // MySQLi válido
$mysqli = (isset($mysqli) && $mysqli instanceof mysqli) ? $mysqli : null;   // MySQLi válido

if (!$pdo && !$conn && !$mysqli) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'msg' => 'No hay conexión activa válida: ni PDO ni MySQLi']);
  exit;
}

// --- Utilidades para adaptadores ---
function isPdo($pdo): bool { return $pdo instanceof PDO; }
function isMysqliConn($c): bool { return $c instanceof mysqli; }
function getMysqli($conn, $mysqli): mysqli {
  if ($conn instanceof mysqli)  return $conn;
  if ($mysqli instanceof mysqli) return $mysqli;
  throw new RuntimeException('No hay conexión MySQLi válida');
}

function getDatabaseName($pdo, $conn, $mysqli): string {
  if (isPdo($pdo)) {
    return (string)$pdo->query('SELECT DATABASE()')->fetchColumn();
  }
  $link = getMysqli($conn, $mysqli);
  $res = $link->query('SELECT DATABASE()');
  if (!$res) throw new RuntimeException('SELECT DATABASE() falló: ' . $link->error);
  $row = $res->fetch_row();
  return (string)$row[0];
}

function fetchAllTables($dbName, $pdo, $conn, $mysqli): array {
  $sql = "SHOW TABLES FROM `{$dbName}`";
  if (isPdo($pdo)) {
    $out = [];
    foreach ($pdo->query($sql) as $row) $out[] = array_values($row)[0];
    return $out;
  }
  $link = getMysqli($conn, $mysqli);
  $res = $link->query($sql);
  if (!$res) throw new RuntimeException('SHOW TABLES falló: ' . $link->error);
  $out = [];
  while ($row = $res->fetch_array(MYSQLI_NUM)) $out[] = $row[0];
  return $out;
}

function showCreateTable($table, $pdo, $conn, $mysqli): string {
  $sql = "SHOW CREATE TABLE `{$table}`";
  if (isPdo($pdo)) {
    $row = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    return $row['Create Table'] ?? $row['Create View'] ?? '';
  }
  $link = getMysqli($conn, $mysqli);
  $res = $link->query($sql);
  if (!$res) throw new RuntimeException('SHOW CREATE TABLE falló: ' . $link->error);
  $row = $res->fetch_assoc();
  return $row['Create Table'] ?? $row['Create View'] ?? '';
}

function selectAll($table, $pdo, $conn, $mysqli): array {
  $sql = "SELECT * FROM `{$table}`";
  if (isPdo($pdo)) {
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  $link = getMysqli($conn, $mysqli);
  $res = $link->query($sql);
  if (!$res) throw new RuntimeException('SELECT * falló en ' . $table . ': ' . $link->error);
  $rows = [];
  while ($row = $res->fetch_assoc()) $rows[] = $row;
  return $rows;
}

function quoteValue($v, $pdo, $conn, $mysqli): string {
  if ($v === null) return 'NULL';
  if (is_bool($v)) return $v ? '1' : '0';
  if (is_int($v) || is_float($v)) return (string)$v;
  if (is_string($v) && preg_match('/^-?\d+(\.\d+)?$/', $v)) return $v;
  if ($pdo instanceof PDO) return $pdo->quote((string)$v);
  $link = getMysqli($conn, $mysqli);
  return "'" . $link->real_escape_string((string)$v) . "'";
}

// --- Directorio de backups ---
$backupDir = __DIR__ . '/backups';
if (!is_dir($backupDir)) {
  if (!@mkdir($backupDir, 0775, true)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => 'No se pudo crear la carpeta de backups en: ' . $backupDir]);
    exit;
  }
}
if (!is_writable($backupDir)) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'msg' => 'La carpeta no es escribible: ' . $backupDir]);
  exit;
}

function nowYmdHis(): string { return date('Ymd_His'); }

function streamDownload(string $fileAbs, string $downloadName): void {
  if (!is_file($fileAbs)) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'msg' => 'Archivo no encontrado']);
    return;
  }
  header_remove('Content-Type');
  header('Content-Description: File Transfer');
  header('Content-Type: application/sql');
  header('Content-Disposition: attachment; filename="' . $downloadName . '"');
  header('Content-Length: ' . filesize($fileAbs));
  readfile($fileAbs);
}

function exportDatabase($dbName, $pdo, $conn, $mysqli): string {
  if (isPdo($pdo)) {
    $pdo->exec('SET NAMES utf8mb4');
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
  } else {
    $link = getMysqli($conn, $mysqli);
    $link->query('SET NAMES utf8mb4');
    $link->query('SET FOREIGN_KEY_CHECKS=0');
  }

  $dump  = "-- Backup generado por Sistema Web Posada Las Mandarinas\n";
  $dump .= "-- Base de datos: `{$dbName}`\n";
  $dump .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n\n";
  $dump .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
  $dump .= "START TRANSACTION;\n";
  $dump .= "SET time_zone = \"+00:00\";\n\n";
  $dump .= "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
  $dump .= "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n";
  $dump .= "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n";
  $dump .= "/*!40101 SET NAMES utf8mb4 */;\n\n";

  $tables = fetchAllTables($dbName, $pdo, $conn, $mysqli);

  foreach ($tables as $table) {
    $create = showCreateTable($table, $pdo, $conn, $mysqli);
    $dump .= "--\n-- Estructura de tabla para `{$table}`\n--\n\n";
    $dump .= "DROP TABLE IF EXISTS `{$table}`;\n";
    $dump .= $create . ";\n\n";

    $dump .= "--\n-- Volcado de datos para la tabla `{$table}`\n--\n\n";
    $rows = selectAll($table, $pdo, $conn, $mysqli);
    if ($rows) {
      $cols = array_map(fn($c) => "`{$c}`", array_keys($rows[0]));
      $colList = '(' . implode(',', $cols) . ')';
      $valuesBatch = [];
      foreach ($rows as $r) {
        $vals = [];
        foreach ($r as $v) $vals[] = quoteValue($v, $pdo, $conn, $mysqli);
        $valuesBatch[] = '(' . implode(',', $vals) . ')';
        if (count($valuesBatch) >= 200) {
          $dump .= "INSERT INTO `{$table}` {$colList} VALUES \n" . implode(",\n", $valuesBatch) . ";\n";
          $valuesBatch = [];
        }
      }
      if ($valuesBatch) {
        $dump .= "INSERT INTO `{$table}` {$colList} VALUES \n" . implode(",\n", $valuesBatch) . ";\n";
      }
      $dump .= "\n";
    }
  }

  if (isPdo($pdo)) $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
  else { $link = getMysqli($conn, $mysqli); $link->query('SET FOREIGN_KEY_CHECKS=1'); }

  $dump .= "COMMIT;\n";
  $dump .= "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET */;\n";
  $dump .= "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n";
  $dump .= "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n";

  return $dump;
}

// --- Ejecutar acción ---
$action = $_GET['action'] ?? 'create';

try {
  $dbName = getDatabaseName($pdo, $conn, $mysqli);

  switch ($action) {
    case 'create':
      $sql = exportDatabase($dbName, $pdo, $conn, $mysqli);
      $fileName = "{$dbName}_" . nowYmdHis() . ".sql";
      $fileAbs  = $backupDir . '/' . $fileName;
      if (file_put_contents($fileAbs, $sql) === false)
        echo json_encode(['ok' => false, 'msg' => 'No se pudo escribir el archivo en: ' . $fileAbs]);
      else
        echo json_encode(['ok' => true, 'msg' => 'Copia creada', 'file' => $fileName, 'path' => $fileAbs]);
      break;

    case 'list':
      $files = [];
      foreach (glob($backupDir . '/*.sql') as $f)
        $files[] = ['name' => basename($f), 'size' => filesize($f), 'mtime' => filemtime($f)];
      usort($files, fn($a,$b) => $b['mtime'] <=> $a['mtime']);
      echo json_encode(['ok' => true, 'files' => $files, 'dir' => $backupDir]);
      break;

    case 'download':
      $file = basename($_GET['file'] ?? '');
      streamDownload($backupDir . '/' . $file, $file);
      exit;

    case 'delete':
      $file = basename($_POST['file'] ?? '');
      $fileAbs = $backupDir . '/' . $file;
      if (is_file($fileAbs)) {
        @unlink($fileAbs);
        echo json_encode(['ok' => true, 'msg' => 'Archivo eliminado']);
      } else echo json_encode(['ok' => false, 'msg' => 'Archivo no encontrado']);
      break;

    case 'restore':
      @ini_set('max_execution_time', '300');
      @ini_set('memory_limit', '512M');

      $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
      $contentLen  = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
      $postMax = ini_get('post_max_size') ?: '0';
      $unit = strtoupper(substr($postMax, -1));
      $mult = ($unit === 'G') ? 1024*1024*1024 : (($unit === 'M') ? 1024*1024 : (($unit === 'K') ? 1024 : 1));
      $postMaxBytes = (int)$postMax * $mult;

      if ($contentLen > 0 && $postMaxBytes > 0 && $contentLen > $postMaxBytes) {
        echo json_encode(['ok'=>false,'msg'=>'El cuerpo supera post_max_size (actual: '.$postMax.')']);
        break;
      }
      if ($contentType && stripos($contentType, 'multipart/form-data') === false) {
        echo json_encode(['ok'=>false,'msg'=>'CONTENT_TYPE no es multipart/form-data. Usa form-data con sql_file.']);
        break;
      }
      if (empty($_FILES) || !isset($_FILES['sql_file'])) {
        echo json_encode(['ok'=>false,'msg'=>'No se recibió el archivo (sql_file).']);
        break;
      }

      $err = $_FILES['sql_file']['error'];
      if ($err !== UPLOAD_ERR_OK) {
        $map = [
          UPLOAD_ERR_INI_SIZE=>'El archivo excede upload_max_filesize',
          UPLOAD_ERR_FORM_SIZE=>'El archivo excede MAX_FILE_SIZE',
          UPLOAD_ERR_PARTIAL=>'El archivo se subió parcialmente',
          UPLOAD_ERR_NO_FILE=>'No se subió ningún archivo',
          UPLOAD_ERR_NO_TMP_DIR=>'Falta upload_tmp_dir',
          UPLOAD_ERR_CANT_WRITE=>'No se pudo escribir en disco',
          UPLOAD_ERR_EXTENSION=>'Una extensión detuvo la subida',
        ];
        echo json_encode(['ok'=>false,'msg'=>$map[$err]??'Error al subir archivo ('.$err.')']);
        break;
      }

      $tmp = $_FILES['sql_file']['tmp_name'];
      if (!is_uploaded_file($tmp)) {
        echo json_encode(['ok'=>false,'msg'=>'Archivo temporal inválido']);
        break;
      }

      $sql = @file_get_contents($tmp);
      if ($sql === false || $sql === '') {
        echo json_encode(['ok'=>false,'msg'=>'No se pudo leer el contenido del .sql']);
        break;
      }

      // Normalizar y dividir (sin DELIMITER)
      $sql = str_replace("\r\n","\n",$sql);
      $statements = array_filter(array_map('trim', preg_split('/;\s*(?:\n|$)/',$sql)));
      if (empty($statements)) {
        echo json_encode(['ok'=>false,'msg'=>'El archivo no contiene sentencias SQL válidas']);
        break;
      }

      try {
        if (isPdo($pdo)) {
          // Sin transacciones por DDL (evitamos "There is no active transaction")
          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
          foreach ($statements as $stmt) {
            if ($stmt!=='') $pdo->exec($stmt);
          }
          $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
          echo json_encode(['ok'=>true,'msg'=>'Restauración completada (PDO, sin transacciones)']);
        } else {
          $link = getMysqli($conn, $mysqli);
          // Sin transacciones explícitas por DDL; solo FK OFF/ON
          $link->query('SET FOREIGN_KEY_CHECKS=0');
          foreach ($statements as $stmt) {
            if ($stmt!=='' && !$link->query($stmt)) {
              throw new RuntimeException('Error SQL: '.$link->error);
            }
          }
          $link->query('SET FOREIGN_KEY_CHECKS=1');
          echo json_encode(['ok'=>true,'msg'=>'Restauración completada (MySQLi, sin transacciones)']);
        }
      } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['ok'=>false,'msg'=>'Error al restaurar: '.$e->getMessage()]);
      }
      break;

    default:
      echo json_encode(['ok'=>false,'msg'=>'Acción no válida']);
  }
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'msg'=>'Excepción: '.$e->getMessage()]);
}
