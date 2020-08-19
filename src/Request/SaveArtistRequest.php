<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class SaveArtistRequest extends RequestDTO
{
    /**
     * @Assert\NotNull(groups={"postArtist", "putArtist"})
     * @Assert\Length(min="1", max="255", groups={"postArtist", "putArtist"})
     * @var mixed|null
     */
    private $name;

    /**
     * @param mixed|null $name
     */
    public function __construct($name = null)
    {
        $this->name = null === $name ? null : trim($name);
    }

    /**
     * @return mixed|null
     */
    public function getName()
    {
        return $this->name;
    }
}
