<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class SaveArtistRequest extends RequestDTO
{
    /**
     * @Assert\NotNull(groups={"postArtist", "putArtist"})
     * @Assert\Length(min="1", max="255", groups={"postArtist", "putArtist"})
     * @var string|null
     */
    private ?string $name;

    /**
     * @param string|null $name
     */
    public function __construct(?string $name = null)
    {
        $this->name = null === $name ? null : trim($name);
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}
