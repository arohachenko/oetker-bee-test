<?php

namespace App\Tests\Request;

use App\Request\SaveArtistRequest;
use App\Request\SaveRecordRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SaveRecordRequestTest extends TestCase
{
    public function testCreateRequest(): void
    {
        $title = 'a';
        $label = 'b';
        $type = 'c';
        $year = 'd';
        /** @var MockObject|SaveArtistRequest $artist */
        $artist = $this->createMock(SaveArtistRequest::class);

        $request = new SaveRecordRequest(
            $title,
            $label,
            $year,
            $type,
            $artist
        );
        self::assertSame($title, $request->getTitle());
        self::assertSame($year, $request->getYear());
        self::assertSame($label, $request->getLabel());
        self::assertSame($type, $request->getType());
        self::assertSame($artist, $request->getArtist());
    }
}
