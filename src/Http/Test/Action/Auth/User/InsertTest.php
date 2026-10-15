<?php

declare(strict_types=1);

namespace Action\Auth\User;

use App\Auth\Command\UserInsert\Handler;
use App\Auth\Context\UserWithActions;
use App\Http\Action\Auth\User\Insert;
use App\Http\Action\Auth\User\Transformer;
use App\Http\Test\Action\JsonResponseTrait;
use App\Http\Response\JsonResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(Insert::class)]
final class InsertTest extends TestCase
{
    use JsonResponseTrait;

    private Handler&MockObject $handler;
    private Transformer&MockObject $transformer;
    private Insert $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = $this->createMock(Handler::class);
        $this->transformer = $this->createMock(Transformer::class);
        $this->action = new Insert($this->handler, $this->transformer);
    }

    public function testSuccess(): void
    {
        $this->transformer->expects($this->once())
            ->method('transformUserWithActions')
            ->willReturn(['id' => 123, 'name' => 'abc']);

        $this->handler
            ->expects($this->once())
            ->method('handle')
            ->willReturn($this->createMock(UserWithActions::class));

        $request = $this->createMock(ServerRequestInterface::class);
        $request
            ->method('getParsedBody')
            ->willReturn([
                'role_id' => 1,
                'email' => 'test@test.ru',
                'name' => 'rule',
                'password' => null,
                'status' => true,
            ]);

        $response = $this->action->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertInstanceOf(JsonResponse::class, $response);

        $output = $this->decodeJsonResponse($response);
        $this->assertArrayHasKey('id', $output);
        $this->assertSame(123, $output['id']);
        $this->assertArrayHasKey('name', $output);
        $this->assertSame('abc', $output['name']);
    }
}
