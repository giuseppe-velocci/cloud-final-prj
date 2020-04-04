<?php
declare(strict_types=1);

namespace App\Helper;

class HashMsg {

    public static function hash(string $msg) :string {
        $options = [
            'cost' => 12,
        ];
        return password_hash($msg, PASSWORD_BCRYPT, $options);
    }

    public static function compareHash(string $msg, string $hash) :bool {
        return password_verify ($msg, $hash);
    }

}