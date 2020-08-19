<?php

namespace App\Tests\Entity;

use App\Entity\Artist;
use App\Entity\Record;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class ArtistTest extends TestCase
{
    public function testGetSet(): void
    {
        $record1 = new Record();
        $record2 = new Record();

        $entity = (new Artist())
            ->setName('test')
            ->setRecords(new ArrayCollection([$record1, $record2]));

        self::assertSame('test', $entity->getName());
        self::assertSame([$record1, $record2], $entity->getRecords()->toArray());
    }
}
