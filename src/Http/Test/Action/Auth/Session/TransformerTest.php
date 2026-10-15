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
use App\Http\Action\Auth\Session\Transformer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Transformer::class)]
final class TransformerTest extends TestCase
{
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
        $session = new Session(
            SessionId::fromValue(1),
            $user,
            new Device('platform', 'os', 'browser'),
            new Ip('1.1.1.1'),
            $startedAt = new \DateTimeImmutable(),
            null,
            $user
        );
        $userTransformer = $this->createMock(\App\Http\Action\Auth\User\Transformer::class);
        $userTransformer->expects($this->exactly(2))
            ->method('transformUser')
            ->with($user)
            ->willReturn(['a']);

        $transformer = new Transformer($userTransformer);
        $transformedRow = $transformer->transformSession($session);

        $this->assertSame([
            'id' => 1,
            'user' => ['a'],
            'device' => [
                'platform' => 'platform',
                'os' => 'os',
                'browser' => 'browser',
            ],
            'ip' => '1.1.1.1',
            'started_at' => $startedAt->format('Y-m-d H:i:s'),
            'last_activity_at' => null,
            'starter' => ['a'],
        ], $transformedRow);
    }

    public function testList(): void
    {
        $transformer = $this->getMockBuilder(Transformer::class)
            ->setConstructorArgs([$this->createMock(\App\Http\Action\Auth\User\Transformer::class)])
            ->onlyMethods(['transformSession'])
            ->getMock();
        $transformer->expects($this->once())
            ->method('transformSession')
            ->willReturn(['a']);

        $result = $transformer->transform([$this->createMock(Session::class)]);
        $this->assertSame([['a']], $result);
    }
}
