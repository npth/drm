<?php

declare(strict_types=1);

namespace Action\Access\Role;

use App\Access\Role\Command\Insert\Command;
use App\Access\Role\Command\Insert\Handler;
use App\Access\Role\Entity\Role;
use App\Http\Action\Access\Role\Transformer;
use App\Http\Test\Action\JsonResponseTrait;
use App\Http\Action\Access\Role\Insert;
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
    private Insert $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = $this->createMock(Handler::class);
        $this->action = new Insert($this->handler, new Transformer());
    }

    public function testSuccess(): void
    {
        $expectedCommand = new Command('Test Role', 'Test Note');

        $this->handler
            ->expects($this->once())
            ->method('handle')
            ->with($this->equalTo($expectedCommand))
            ->willReturn(Role::fromValues(123, 'Test Role', 'Test Note'));

        $request = $this->createMock(ServerRequestInterface::class);
        $request
            ->method('getParsedBody')
            ->willReturn([
                'name' => 'Test Role',
                'note' => 'Test Note',
            ]);

        $response = $this->action->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertInstanceOf(JsonResponse::class, $response);

        $output = $this->decodeJsonResponse($response);
        $this->assertArrayHasKey('id', $output);
        $this->assertSame(123, $output['id']);
        $this->assertArrayHasKey('name', $output);
        $this->assertSame('Test Role', $output['name']);
        $this->assertArrayHasKey('note', $output);
        $this->assertSame('Test Note', $output['note']);
    }
}
