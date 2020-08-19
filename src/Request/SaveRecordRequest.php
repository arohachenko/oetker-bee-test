<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class SaveRecordRequest extends RequestDTO
{
    /**
     * @Assert\NotNull(groups={"postRecord", "putRecord"})
     * @Assert\Length(min="1", max="255", groups={"postRecord", "putRecord"})
     * @var mixed|null
     */
    private $title;

    /**
     * @Assert\NotNull(groups={"postRecord", "putRecord"})
     * @Assert\Length(min="1", max="255", groups={"postRecord", "putRecord"})
     * @var mixed|null
     */
    private $label;

    /**
     * @Assert\NotNull(groups={"postRecord", "putRecord"})
     * @Assert\Type(type="numeric", groups={"postRecord", "putRecord"})
     * @Assert\Range(min="1860", max="2020", groups={"postRecord", "putRecord"})
     * @var mixed|null
     */
    private $year;

    /**
     * @Assert\NotNull(groups={"postRecord", "putRecord"})
     * @Assert\Length(min="1", max="50", groups={"postRecord", "putRecord"})
     * @var mixed|null
     */
    private $type;

    /**
     * @Assert\NotNull(groups={"postRecord", "putRecord"})
     * @Assert\Valid(groups={"postRecord", "putRecord", "postArtist", "putArtist"})
     * @var SaveArtistRequest|null
     */
    private ?SaveArtistRequest $artist;

    /**
     * @param mixed|null $title
     * @param mixed|null $label
     * @param mixed|null $year
     * @param mixed|null $type
     * @param SaveArtistRequest|null $artist
     */
    public function __construct(
        $title = null,
        $label = null,
        $year = null,
        $type = null,
        ?SaveArtistRequest $artist = null
    ) {
        $this->title = $title;
        $this->label = $label;
        $this->year = $year;
        $this->type = $type;
        $this->artist = $artist;
    }

    /**
     * @return mixed|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed|null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return mixed|null
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @return mixed|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return SaveArtistRequest|null
     */
    public function getArtist(): ?SaveArtistRequest
    {
        return $this->artist;
    }
}
