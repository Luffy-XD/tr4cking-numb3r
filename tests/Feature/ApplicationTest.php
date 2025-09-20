<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ApplicationTest extends TestCase
{
    public function testBasicMath(): void
    {
        $this->assertSame(4, 2 + 2);
    }
}
