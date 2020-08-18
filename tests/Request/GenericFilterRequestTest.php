<?php

namespace App\Tests\Request;

use App\Request\GenericFilterRequest;
use PHPUnit\Framework\TestCase;

class GenericFilterRequestTest extends TestCase
{
    public function testCreateRequest()
    {
        $limit = '1';
        $offset = '-2';
        $artist = null;
        $title = 'foo';
        $year = 'bar';

        $request = new GenericFilterRequest(
            $limit,
            $offset,
            $artist,
            $title,
            $year
        );

        $this->assertSame($limit, $request->getLimit());
        $this->assertSame($offset, $request->getOffset());
        $this->assertSame($artist, $request->getArtist());
        $this->assertSame($title, $request->getTitle());
        $this->assertSame($year, $request->getYear());
    }
}
