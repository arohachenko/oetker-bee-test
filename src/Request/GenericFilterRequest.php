<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class GenericFilterRequest
{
    /**
     * @Assert\Type(type="numeric", groups={"getArtist", "getRecord"})
     * @Assert\PositiveOrZero(groups={"getArtist", "getRecord"})
     * @var string
     */
    private string $limit;

    /**
     * @Assert\Type(type="numeric", groups={"getArtist", "getRecord"})
     * @Assert\PositiveOrZero(groups={"getArtist", "getRecord"})
     * @var string
     */
    private string $offset;

    /**
     * @Assert\Length(min="2", max="255", groups={"getRecord"})
     * @Assert\IsNull(groups={"getArtist"}, message="Filtering is not supported here.")
     * @var string|null
     */
    private ?string $artist;

    /**
     * @Assert\Length(min="2", max="255", groups={"getRecord"})
     * @Assert\IsNull(groups={"getArtist"}, message="Filtering is not supported here.")
     * @var string|null
     */
    private ?string $title;

    /**
     * @Assert\Type(type="numeric", groups={"getRecord"})
     * @Assert\Range(min="1860", max="2020", groups={"getRecord"})
     * @Assert\IsNull(groups={"getArtist"}, message="Filtering is not supported here.")
     * @var string|null
     */
    private ?string $year;

    /**
     * @param string $limit
     * @param string $offset
     * @param string|null $artist
     * @param string|null $title
     * @param string|null $year
     */
    public function __construct(
        string $limit,
        string $offset,
        ?string $artist = null,
        ?string $title = null,
        ?string $year = null
    ) {
        $this->limit = $limit;
        $this->offset = $offset;
        $this->artist = $artist;
        $this->title = $title;
        $this->year = $year;
    }

    /**
     * @return string
     */
    public function getLimit(): string
    {
        return $this->limit;
    }

    /**
     * @return string
     */
    public function getOffset(): string
    {
        return $this->offset;
    }

    /**
     * @return string|null
     */
    public function getArtist(): ?string
    {
        return $this->artist;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getYear(): ?string
    {
        return $this->year;
    }
}
