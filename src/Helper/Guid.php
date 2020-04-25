<?php
declare(strict_types=1);

namespace App\Helper;

class Guid {
    const ENTROPY = 8;

    public function generate() :string {
        $bytes = random_bytes(self::ENTROPY);
        return bin2hex($bytes);
    }
    
}