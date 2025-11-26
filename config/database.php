<?php
// config/database.php
class Database {
    private static ?PDO $pdo = null;

    public static function connection(): PDO {
        if (self::$pdo === null) {
            $config = include __DIR__ . '/config.php';
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $config['db']['host'],
                $config['db']['name'],
                $config['db']['charset']
            );
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];
            self::$pdo = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $options);
        }
        return self::$pdo;
    }
}
