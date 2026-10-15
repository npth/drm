<?php

declare(strict_types=1);

namespace Action\Access\Role;

use App\Access\Role\Command\Delete\Command;
use App\Access\Role\Command\Delete\Handler;
use App\Http\Response\EmptyResponse;
use App\Http\Test\Action\JsonResponseTrait;
use App\Http\Action\Access\Role\Delete;
use App\Http\Response\JsonResponse;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(Delete::class)]
final class DeleteTest extends TestCase
{
    use JsonResponseTrait;

    private Handler&MockObject $handler;
    private Delete $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = $this->createMock(Handler::class);
        $this->action = new Delete($this->handler);
    }
    public function testSuccess(): void
    {
        $expectedCommand = new Command(1);

        $this->handler
            ->expects($this->once())
            ->method('handle')
            ->with($this->equalTo($expectedCommand));

        $request = $this->createMock(ServerRequestInterface::class);
        $request
            ->method('getParsedBody')
            ->willReturn([
                'id' => 1,
            ]);

        $response = $this->action->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
        $this->assertInstanceOf(EmptyResponse::class, $response);
    }
}
