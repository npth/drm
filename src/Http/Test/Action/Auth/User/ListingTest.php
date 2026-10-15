<?php

declare(strict_types=1);

namespace Action\Auth\User;

use App\Access\Attribute\Entity\ActionCodes;
use App\Auth\Context\UserWithActions;
use App\Auth\Entity\Email;
use App\Auth\Entity\Role;
use App\Auth\Entity\RoleName;
use App\Auth\Entity\User;
use App\Auth\Entity\UserId;
use App\Auth\Query\UserListing\Fetcher;
use App\Http\Action\Auth\User\Listing;
use App\Http\Action\Auth\User\Transformer;
use App\Http\Test\Action\JsonResponseTrait;
use App\Http\Response\JsonResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

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
            new UserWithActions(
                new User(
                    UserId::fromValue(1),
                    new Role(\App\Auth\Entity\RoleId::fromValue(2), RoleName::fromValue('user')),
                    new Email('test1@test.ru'),
                    \App\Auth\Entity\Name::fromValue('user 1'),
                    null,
                    new \App\Auth\Entity\Status(true),
                ),
                new ActionCodes([])
            ),
            new UserWithActions(
                new User(
                    UserId::fromValue(2),
                    new Role(\App\Auth\Entity\RoleId::fromValue(2), RoleName::fromValue('user')),
                    new Email('test2@test.ru'),
                    \App\Auth\Entity\Name::fromValue('user 2'),
                    null,
                    new \App\Auth\Entity\Status(true),
                ),
                new ActionCodes([])
            ),
        ];

        $this->fetcher
            ->expects($this->once())
            ->method('fetch')
            ->willReturn($expectedData);

        $currentUser = new User(
            UserId::fromValue(1),
            new Role(\App\Auth\Entity\RoleId::fromValue(1), RoleName::fromValue('admin')),
            new Email('current@test.ru'),
            \App\Auth\Entity\Name::fromValue('Current User'),
            null,
            new \App\Auth\Entity\Status(true),
        );
        $request = $this->createMock(ServerRequestInterface::class);
        $request
            ->method('getAttribute')
            ->with('identity')
            ->willReturn($currentUser);

        $response = $this->action->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertInstanceOf(JsonResponse::class, $response);
        $output = $this->decodeJsonResponse($response);

        $this->assertArrayHasKey('data', $output);
        $this->assertCount(2, $output['data']);

        $datum1 = $output['data'][0];
        $this->assertArrayHasKey('id', $datum1);
        $this->assertEquals(1, $datum1['id']);
        $this->assertArrayHasKey('name', $datum1);
        $this->assertEquals('user 1', $datum1['name']);

        $datum2 = $output['data'][1];
        $this->assertArrayHasKey('id', $datum2);
        $this->assertEquals(2, $datum2['id']);
        $this->assertArrayHasKey('name', $datum2);
        $this->assertEquals('user 2', $datum2['name']);
    }
}
