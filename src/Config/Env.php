<?php
declare(strict_types=1);

namespace App\Config;

class Env {
    protected static $basepath;
    protected static $envFile = "/config/config.env";
    protected static $apiFile = './config/api.php';

    protected static $config;
   

    protected static function parseEnv(string $envPath): array {
        if (! file_exists($envPath))
            throw new \Error("Env file not found.");
          
        $arr = file($envPath);
        $config = [];
        $limit = count($arr);
        $subArrayKey = '';

        for ($i = 0; $i < $limit; $i++) {
            $c = trim($arr[$i]);

            if (substr($c, 0, 1) != "#" && strlen($c) > 0) {
                $data = explode(" ", preg_replace("/\s+/", ' ', $c));

                if (count($data) > 1 && $data[1] == '[') {
                    $subArrayKey = trim($data[0]);
                    $config[$subArrayKey] = [];
                    continue;

                } elseif(strpos($c, ']') !== false) {
                    $subArrayKey = '';
                    continue;
                } 

                if (strlen($subArrayKey) > 0) {
                    $config[$subArrayKey][trim($data[0])] = trim($data[1]); 

                } else {
                    $config[trim($data[0])] = trim($data[1]); 
                }
            }
        }

        return $config;
    }


    public static function get(string $key) {
        $basepath = $basepath ?? dirname(__DIR__, 2);
        try {
            self::$config = self::$config ?? self::parseEnv($basepath . '/' . self::$envFile);
            self::$config['API'] = require_once(self::$apiFile);

        } catch (\Error $e) {
            die($e->getMessage());
        }
        
        if (! array_key_exists($key, self::$config))
            throw new \InvalidArgumentException("Key not found!");
        
        if (is_array(self::$config[$key])) {
            return filter_var_array(self::$config[$key]);
        }

        return filter_var(self::$config[$key], FILTER_SANITIZE_URL);
    }
}
