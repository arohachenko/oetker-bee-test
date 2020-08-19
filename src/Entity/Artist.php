<?php

namespace App\Entity;

use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ArtistRepository::class)
 * @UniqueEntity("name")
 * @ORM\Table(name="artist", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="name_uniq", columns={"name"})
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
     * @Groups({"getArtist", "getRecord"})
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"getArtist", "putArtist", "postArtist", "getRecord", "putRecord", "postRecord"})
     */
    private string $name;

    /**
     * @var Collection|Record[]
     *
     * @ORM\OneToMany(targetEntity="Record", mappedBy="artist")
     * @Groups({"getArtist"})
     */
    private Collection $records;

    public function __construct()
    {
        $this->records = new ArrayCollection();
    }

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
    public function setRecords(Collection $records): Artist
    {
        $this->records = $records;
        return $this;
    }
}
