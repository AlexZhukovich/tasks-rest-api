<?php
    class DatabaseConnector {
        
        private static $writeDBConnection;
        private static $readDBConnection;

        public static function connectWriteDatabase() {
            if (self::$writeDBConnection === null) {
                self::$writeDBConnection = new PDO('mysql:host=localhost;dbname=tasks-db;utf8', 'root', 'root');
                self::$writeDBConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$writeDBConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            }
            return self::$writeDBConnection;
        }

        public static function connectReadDatabase() {
            if (self::$readDBConnection === null) {
                self::$readDBConnection = new PDO('mysql:host=localhost;dbname=tasks-db;utf8', 'root', 'root');
                self::$readDBConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$readDBConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            }
            return self::$readDBConnection;
        }
    }
?>