<?php

namespace App\Entity;

use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArtistRepository::class)
 * @ORM\Table(name="artist", indexes={
 *     @ORM\Index(name="name_idx", columns={"name"})
 * })
 */
class Artist
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private string $name;

    /**
     * @var Collection|Record[]
     *
     * @ORM\OneToMany(targetEntity="Record", mappedBy="artist")
     */
    private Collection $records;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Artist
     */
    public function setName(string $name): Artist
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Record[]|Collection
     */
    public function getRecords(): Collection
    {
        return $this->records;
    }

    /**
     * @param Record[]|Collection $records
     * @return Artist
     */
    public function setRecords(Collection $records): Collection
    {
        $this->records = $records;
        return $this;
    }
}
