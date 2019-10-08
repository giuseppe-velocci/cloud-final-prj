<?php
declare(strict_types=1);

namespace App\Config;

class Env {
    protected static $basepath;
    protected static $envFile = "config.env";

    protected static $config;
   

    protected static function parseEnv(string $envPath): array {
        if (! file_exists($envPath))
            throw new \Error("Env file not found.");
            
        $arr = file($envPath);
        $config = [];

        foreach ($arr AS $c) {
            $c = trim($c);
            if (substr($c, 0, 1) != "#" && strlen($c) > 0) {
                $data = explode("=", $c);
                $config[trim($data[0])] = trim($data[1]);                
            }
        }

        return $config;
    }


    public static function get(string $key): string {
        $basepath = $basepath ?? dirname(__DIR__, 3);
        try {
            self::$config = self::$config ?? self::parseEnv($basepath . '/' . self::$envFile);
        } catch (\Error $e) {
            die($e->getTrace() . $e->getMessage());
        }
        
        if (! array_key_exists($key, self::$config))
            throw new \InvalidArgumentException("Key not found!");
        
        echo self::$config[$key];
        return filter_var(self::$config[$key], FILTER_SANITIZE_URL);
    }
}
