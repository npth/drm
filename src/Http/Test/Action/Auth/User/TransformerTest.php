<?php

declare(strict_types=1);

namespace Action\Auth\User;

use App\Access\Attribute\Entity\ActionCode;
use App\Access\Attribute\Entity\ActionCodes;
use App\Auth\Context\UserWithActions;
use App\Auth\Entity\Email;
use App\Auth\Entity\Name;
use App\Auth\Entity\Role;
use App\Auth\Entity\RoleId;
use App\Auth\Entity\RoleName;
use App\Auth\Entity\Status;
use App\Auth\Entity\User;
use App\Auth\Entity\UserId;
use App\Http\Action\Auth\User\Transformer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Transformer::class)]
final class TransformerTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = new UserWithActions(
            new User(
                UserId::fromValue(1),
                new Role(RoleId::fromValue(2), RoleName::fromValue('user')),
                new Email('test@test.ru'),
                Name::fromValue('user 1'),
                null,
                new Status(true),
            ),
            new ActionCodes([ActionCode::fromValue('edit')])
        );
        $transformer = new Transformer();
        $transformedRow = $transformer->transformUserWithActions($user);

        $this->assertSame([
            'id' => 1,
            'role' => [
                'id' => 2,
                'name' => 'user',
            ],
            'name' => 'user 1',
            'email' => 'test@test.ru',
            'status' => true,
            'actions' => ['edit'],
        ], $transformedRow);
    }

    public function testList(): void
    {
        $transformer = $this->getMockBuilder(Transformer::class)
            ->onlyMethods(['transformUserWithActions'])
            ->getMock();
        $transformer->expects(self::once())
            ->method('transformUserWithActions')
            ->willReturn(['a']);

        $result = $transformer->transform([$this->createMock(UserWithActions::class)]);
        $this->assertSame([['a']], $result);
    }
}
