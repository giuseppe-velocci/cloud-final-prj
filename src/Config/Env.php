<?php
declare(strict_types=1);

namespace App\Config;

class Env {
    protected static $basepath;
    protected static $envFile = "/config/config.env";

    protected static $config;
   

    protected static function parseEnv(string $envPath): array {
        if (! file_exists($envPath))
            throw new \Error("Env file not found.");
            
        $arr = file($envPath);
        $config = [];

        foreach ($arr AS $c) {
            $c = trim($c);
            if (substr($c, 0, 1) != "#" && strlen($c) > 0) {
                $data = explode(" ", preg_replace("/\s+/", ' ', $c));
                $config[trim($data[0])] = trim($data[1]);                
            }
        }

        return $config;
    }


    public static function get(string $key): string {
        $basepath = $basepath ?? dirname(__DIR__, 2);
        try {
            self::$config = self::$config ?? self::parseEnv($basepath . '/' . self::$envFile);
        } catch (\Error $e) {
            die($e->getMessage());
        }
        
        if (! array_key_exists($key, self::$config))
            throw new \InvalidArgumentException("Key not found!");
        
        return filter_var(self::$config[$key], FILTER_SANITIZE_URL);
    }
}
