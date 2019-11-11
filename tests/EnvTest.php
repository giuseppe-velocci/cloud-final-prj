<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Config\Env;

final class EnvTest extends TestCase
{
    public function testCanReturnValidUrl(): void
    {
        $this->assertTrue(
            (bool) filter_var(Env::get('DB_HOST'), FILTER_VALIDATE_URL)
        );
    }
    public function testKeyNotFound(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Env::get('invalid');
    }

}
