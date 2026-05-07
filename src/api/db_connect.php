<?php
function getEnvValue($key) {
    $value = getenv($key);
    if ($value !== false) {
        return $value;
    }

    if (isset($_ENV[$key])) {
        return $_ENV[$key];
    }

    if (isset($_SERVER[$key])) {
        return $_SERVER[$key];
    }

    return null;
}

function getLocalDbOverrides() {
    $localConfigPath = __DIR__ . '/db.local.php';
    if (!file_exists($localConfigPath)) {
        return [];
    }

    $overrides = require $localConfigPath;
    return is_array($overrides) ? $overrides : [];
}

function getDbConfig() {
    $localOverrides = getLocalDbOverrides();
    $host = trim((string)(getEnvValue('DB_HOST') ?? ''));
    $dbname = trim((string)(getEnvValue('DB_NAME') ?? ''));
    $username = trim((string)(getEnvValue('DB_USER') ?? ''));

    // Keep env-driven config; empty/missing values fall back to local defaults.
    if ($host === '') {
        $host = 'localhost';
    }
    if ($dbname === '') {
        $dbname = 'vintage_library';
    }
    if ($username === '') {
        $username = 'root';
    }

    if (isset($localOverrides['host']) && trim((string)$localOverrides['host']) !== '') {
        $host = trim((string)$localOverrides['host']);
    }
    if (isset($localOverrides['dbname']) && trim((string)$localOverrides['dbname']) !== '') {
        $dbname = trim((string)$localOverrides['dbname']);
    }
    if (isset($localOverrides['username']) && trim((string)$localOverrides['username']) !== '') {
        $username = trim((string)$localOverrides['username']);
    }

    $passwordCandidates = [];
    foreach (['DB_PASS', 'MYSQL_PASSWORD', 'MYSQL_ROOT_PASSWORD', 'MARIADB_ROOT_PASSWORD'] as $passwordKey) {
        $passwordValue = getEnvValue($passwordKey);
        if ($passwordValue !== null) {
            $passwordCandidates[] = (string)$passwordValue;
        }
    }

    // If no password env variable is provided, still try blank password.
    if (empty($passwordCandidates)) {
        $passwordCandidates[] = '';
    }

    if (array_key_exists('password', $localOverrides)) {
        array_unshift($passwordCandidates, (string)$localOverrides['password']);
    }

    $passwordCandidates = array_values(array_unique($passwordCandidates));

    return [
        'host' => $host,
        'dbname' => $dbname,
        'username' => $username,
        'passwordCandidates' => $passwordCandidates
    ];
}

function setDbConnectionError($errorMessage) {
    $GLOBALS['DB_CONNECTION_ERROR'] = $errorMessage;
}

function getDbConnectionErrorMessage() {
    return $GLOBALS['DB_CONNECTION_ERROR'] ?? 'Unable to connect to the database.';
}

function renderDbConnectionErrorPage() {
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=UTF-8');
    }

    $message = htmlspecialchars(getDbConnectionErrorMessage(), ENT_QUOTES, 'UTF-8');
    echo "<h1>Database connection error</h1>";
    echo "<p>{$message}</p>";
    echo "<p>Set DB_HOST, DB_NAME, DB_USER, and DB_PASS in your environment, then reload.</p>";
    exit;
}

function buildDbConnectionError($config, $lastError = null) {
    $baseMessage = "Database connection failed for '{$config['username']}'@'{$config['host']}' to '{$config['dbname']}'.";
    if ($lastError) {
        return $baseMessage . ' ' . $lastError;
    }

    return $baseMessage;
}

// Establish PDO connection for API handlers
function connectDB() {
    $config = getDbConfig();
    $lastError = null;

    foreach ($config['passwordCandidates'] as $password) {
        try {
            $conn = new PDO(
                "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4",
                $config['username'],
                $password
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            $lastError = $e->getMessage();
        }
    }

    setDbConnectionError(buildDbConnectionError($config, $lastError));
    return null;
}

function connectMySQLi() {
    $config = getDbConfig();
    $lastError = null;
    $driver = new mysqli_driver();
    $previousReportMode = $driver->report_mode;
    $driver->report_mode = MYSQLI_REPORT_OFF;

    foreach ($config['passwordCandidates'] as $password) {
        try {
            $conn = @new mysqli($config['host'], $config['username'], $password, $config['dbname']);
            if (!$conn->connect_error) {
                $conn->set_charset('utf8mb4');
                $driver->report_mode = $previousReportMode;
                return $conn;
            }
            $lastError = $conn->connect_error;
        } catch (mysqli_sql_exception $e) {
            $lastError = $e->getMessage();
        }
    }

    $driver->report_mode = $previousReportMode;
    setDbConnectionError(buildDbConnectionError($config, $lastError));
    return null;
}

// Helper function to return JSON response
function returnJSON($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?> 