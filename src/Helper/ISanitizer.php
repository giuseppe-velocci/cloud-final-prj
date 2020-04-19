<?php
declare(strict_types=1);

namespace App\Helper;

interface ISanitizer {
    public function clean($data);
}