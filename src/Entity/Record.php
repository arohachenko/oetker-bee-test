<?php

namespace App\Entity;

use App\Repository\RecordRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=RecordRepository::class)
 * @ORM\Table(name="record", indexes={
 *     @ORM\Index(name="title_idx", columns={"title"}),
 *     @ORM\Index(name="year_idx", columns={"year"})
 * })
 */
class Record
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
     * @ORM\Column(name="title", type="string", length=255)
     * @Groups({"getArtist", "getRecord", "putRecord", "postRecord"})
     */
    private string $title;

    /**
     * @var Artist|null
     *
     * @ORM\ManyToOne(targetEntity="Artist", cascade={"persist"})
     * @ORM\JoinColumn(name="artist_id", nullable=true, referencedColumnName="id", onDelete="SET NULL")
     * @Groups({"getRecord", "putRecord", "postRecord"})
     */
    private ?Artist $artist;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255)
     * @Groups({"getArtist", "getRecord", "putRecord", "postRecord"})
     */
    private string $label;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="smallint", options={"unsigned":true})
     * @Groups({"getArtist", "getRecord", "putRecord", "postRecord"})
     */
    private int $year;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=50)
     * @Groups({"getArtist", "getRecord", "putRecord", "postRecord"})
     */
    private string $type;

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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Record
     */
    public function setTitle(string $title): Record
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return Artist|null
     */
    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    /**
     * @param Artist|null $artist
     * @return Record
     */
    public function setArtist(?Artist $artist): Record
    {
        $this->artist = $artist;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return Record
     */
    public function setLabel(string $label): Record
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @param int $year
     * @return Record
     */
    public function setYear(int $year): Record
    {
        $this->year = $year;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Record
     */
    public function setType(string $type): Record
    {
        $this->type = $type;
        return $this;
    }
}
