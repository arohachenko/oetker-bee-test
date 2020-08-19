<?php

namespace App\Tests\Controller;

use App\Controller\AuthController;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthControllerTest extends TestCase
{
    public function testGetTokenAction()
    {
        $controller = new AuthController();
        /** @var MockObject|JWTTokenManagerInterface $tokenManagerMock */
        $tokenManagerMock = $this->createMock(JWTTokenManagerInterface::class);
        /** @var MockObject|User $userMock */
        $userMock = $this->createMock(User::class);

        $tokenManagerMock->expects(self::once())->method('create')->willReturn('');

        $this->assertInstanceOf(JsonResponse::class, $controller->getTokenAction($userMock, $tokenManagerMock));
    }
}
