<?php
declare(strict_types=1);

namespace App\Helper;

class Sanitizer implements ISanitizer {
    public function clean(string $data) :string {
        return addslashes(strip_tags($data));
    }
}