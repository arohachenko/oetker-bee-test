<?php

namespace App\Tests\Request;

use App\Request\SaveArtistRequest;
use PHPUnit\Framework\TestCase;

class SaveArtistRequestTest extends TestCase
{
    public function testCreateRequest(): void
    {
        $request = new SaveArtistRequest('test');
        self::assertSame('test', $request->getName());
    }
}
