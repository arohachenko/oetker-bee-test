<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity
 */
class User implements UserInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private string $username;

    /**
     * @param string $username
     */
    public function __construct(string $username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return UserInterface
     */
    public function setUsername(string $username): UserInterface
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return null
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return '';
    }

    /**
     * @return array|string[]
     */
    public function getRoles(): array
    {
        return 'admin' === $this->getUsername()
            ? ['ROLE_ADMIN', 'ROLE_USER']
            : ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
    }
}
