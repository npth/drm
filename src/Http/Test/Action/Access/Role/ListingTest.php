<?php

declare(strict_types=1);

namespace Action\Access\Role;

use App\Access\Role\Entity\Role;
use App\Access\Role\Query\List\Fetcher;
use App\Auth\Entity\User;
use App\Auth\Entity\UserId;
use App\Http\Action\Access\Role\Transformer;
use App\Http\Test\Action\JsonResponseTrait;
use App\Http\Action\Access\Role\Listing;
use App\Http\Response\JsonResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ServerRequestFactory;

#[CoversClass(Listing::class)]
final class ListingTest extends TestCase
{
    use JsonResponseTrait;

    private Fetcher&MockObject $fetcher;
    private Listing $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fetcher = $this->createMock(Fetcher::class);
        $this->action = new Listing($this->fetcher, new Transformer());
    }
    public function testSuccess(): void
    {
        $expectedData = [
            Role::fromValues(1, 'action1', 'Действие 1'),
            Role::fromValues(2, 'action2', 'Действие 2'),
        ];

        $this->fetcher
            ->expects($this->once())
            ->method('fetch')
            ->with($this->createMock(User::class))
            ->willReturn($expectedData);

        $userMock = $this->createMock(User::class);
        $userMock->method('getId')->willReturn(UserId::fromValue(123));

        $request = (new ServerRequestFactory())->createServerRequest('GET', '/roles');
        $response = $this->action->handle($request->withAttribute('identity', $userMock));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertInstanceOf(JsonResponse::class, $response);
        $output = $this->decodeJsonResponse($response);

        $this->assertArrayHasKey('data', $output);
        $this->assertCount(2, $output['data']);

        $datum1 = $output['data'][0];
        $this->assertArrayHasKey('id', $datum1);
        $this->assertEquals(1, $datum1['id']);
        $this->assertArrayHasKey('name', $datum1);
        $this->assertEquals('action1', $datum1['name']);
        $this->assertArrayHasKey('note', $datum1);
        $this->assertEquals('Действие 1', $datum1['note']);

        $datum2 = $output['data'][1];
        $this->assertArrayHasKey('id', $datum2);
        $this->assertEquals(2, $datum2['id']);
        $this->assertArrayHasKey('name', $datum2);
        $this->assertEquals('action2', $datum2['name']);
        $this->assertArrayHasKey('note', $datum2);
        $this->assertEquals('Действие 2', $datum2['note']);
    }
}
