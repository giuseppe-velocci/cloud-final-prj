<?php
declare(strict_types=1);

namespace App\Helper;

class Sanitizer implements ISanitizer {
    public function clean($data) {
        if (is_string($data)) {
            return addslashes(strip_tags($data));
        }
        return $data;
    }
}