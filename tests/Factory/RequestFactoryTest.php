<?php

namespace App\Tests\Factory;

use App\Factory\RequestFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class RequestFactoryTest extends TestCase
{
    /**
     * @var MockObject|SerializerInterface
     */
    private MockObject $serializerMock;

    private RequestFactory $requestFactory;

    public function setUp()
    {
        $this->serializerMock = $this->createMock(SerializerInterface::class);
        $this->requestFactory = new RequestFactory($this->serializerMock);
    }

    /**
     * @param array $queryParams
     * @param $expectedLimit
     * @param $expectedOffset
     * @param $expectedAuthor
     * @param $expectedTitle
     * @param $expectedYear
     * @dataProvider createGenericFilterRequestProvider
     */
    public function testCreateGenericFilterRequest(
        array $queryParams,
        $expectedLimit,
        $expectedOffset,
        $expectedAuthor,
        $expectedTitle,
        $expectedYear
    ) {
        $queryBag = new InputBag($queryParams);

        /** @var MockObject|Request $requestMock */
        $requestMock = $this->createMock(Request::class);
        $requestMock->query = $queryBag;

        $actual = $this->requestFactory->createGenericFilterRequest($requestMock, 1, 2);
        self::assertSame($expectedLimit, $actual->getLimit());
        self::assertSame($expectedOffset, $actual->getOffset());
        self::assertSame($expectedAuthor, $actual->getArtist());
        self::assertSame($expectedTitle, $actual->getTitle());
        self::assertSame($expectedYear, $actual->getYear());
    }

    /**
     * The query array is not validated in any way before this, so we should test thoroughly.
     * We only know that the values are always strings.
     *
     * @return array[]
     */
    public function createGenericFilterRequestProvider(): array
    {
        return [
            [
                [],
                '1',
                '2',
                null,
                null,
                null,
            ],
            [
                ['limit' => 10],
                '10',
                '2',
                null,
                null,
                null,
            ],
            [
                ['offset' => 42],
                '1',
                '42',
                null,
                null,
                null,
            ],
            [
                [
                    'artist' => 'Bob',
                    'title' => '            '
                ],
                '1',
                '2',
                'Bob',
                '',
                null,
            ],
            [
                [
                    'limit' => '99',
                    'offset' => '999',
                    'artist' => 'John Doe',
                    'title' => 'To Mary Sue',
                    'year' => '1234'
                ],
                '99',
                '999',
                'John Doe',
                'To Mary Sue',
                '1234',
            ],
            [
                [
                    'artist' => '   John Doe ',
                    'title' => 'Foo Bar             ',
                ],
                '1',
                '2',
                'John Doe',
                'Foo Bar',
                null,
            ],
        ];
    }
}
