<?php

namespace App\Tests\Entity;

use App\Entity\Artist;
use App\Entity\Record;
use PHPUnit\Framework\TestCase;

class RecordTest extends TestCase
{
    public function testGetSet(): void
    {
        $artist = new Artist();

        $entity = (new Record())
            ->setTitle('title')
            ->setLabel('label')
            ->setYear(1970)
            ->setType('type')
            ->setArtist($artist);

        self::assertSame('title', $entity->getTitle());
        self::assertSame('label', $entity->getLabel());
        self::assertSame('type', $entity->getType());
        self::assertSame(1970, $entity->getYear());
        self::assertSame($artist, $entity->getArtist());
    }
}
