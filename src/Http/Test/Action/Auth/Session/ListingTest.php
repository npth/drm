<?php

declare(strict_types=1);

namespace Action\Auth\Session;

use App\Auth\Entity\Device;
use App\Auth\Entity\Email;
use App\Auth\Entity\Ip;
use App\Auth\Entity\Name;
use App\Auth\Entity\Role;
use App\Auth\Entity\RoleId;
use App\Auth\Entity\RoleName;
use App\Auth\Entity\Session;
use App\Auth\Entity\SessionId;
use App\Auth\Entity\Status;
use App\Auth\Entity\User;
use App\Auth\Entity\UserId;
use App\Auth\Query\SessionListing\Fetcher;
use App\Http\Action\Auth\Session\Listing;
use App\Http\Action\Auth\Session\Transformer;
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
        $this->action = new Listing(
            $this->fetcher,
            new Transformer(new \App\Http\Action\Auth\User\Transformer())
        );
    }
    public function testSuccess(): void
    {
        $user = new User(
            UserId::fromValue(1),
            new Role(RoleId::fromValue(2), RoleName::fromValue('user')),
            new Email('test@test.ru'),
            Name::fromValue('user 1'),
            null,
            new Status(true),
        );
        $expectedData = [
            new Session(
                SessionId::fromValue(1),
                $user,
                new Device('platform', 'os', 'browser'),
                new Ip('1.1.1.1'),
                $startedAt = new \DateTimeImmutable(),
                null,
                $user
            ),
            new Session(
                SessionId::fromValue(2),
                $user,
                new Device('platform', 'os', 'browser'),
                new Ip('2.2.2.2'),
                $startedAt = new \DateTimeImmutable(),
                null,
                $user
            ),
        ];

        $this->fetcher
            ->expects($this->once())
            ->method('fetch')
            ->willReturn($expectedData);

        $response = $this->action->handle($this->createMock(ServerRequestInterface::class));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertInstanceOf(JsonResponse::class, $response);
        $output = $this->decodeJsonResponse($response);

        $this->assertArrayHasKey('data', $output);
        $this->assertCount(2, $output['data']);

        $datum1 = $output['data'][0];
        $this->assertArrayHasKey('id', $datum1);
        $this->assertEquals(1, $datum1['id']);
        $this->assertArrayHasKey('ip', $datum1);
        $this->assertEquals('1.1.1.1', $datum1['ip']);

        $datum2 = $output['data'][1];
        $this->assertArrayHasKey('id', $datum2);
        $this->assertEquals(2, $datum2['id']);
        $this->assertArrayHasKey('ip', $datum2);
        $this->assertEquals('2.2.2.2', $datum2['ip']);
    }
}
