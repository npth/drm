<?php

declare(strict_types=1);

namespace Action\Access\Role;

use App\Access\Role\Command\Update\Command;
use App\Access\Role\Command\Update\Handler;
use App\Access\Role\Entity\Role;
use App\Http\Action\Access\Role\Transformer;
use App\Http\Test\Action\JsonResponseTrait;
use App\Http\Action\Access\Role\Update;
use App\Http\Response\JsonResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(Update::class)]
final class UpdateTest extends TestCase
{
    use JsonResponseTrait;

    private Handler&MockObject $handler;
    private Update $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = $this->createMock(Handler::class);
        $this->action = new Update($this->handler, new Transformer());
    }

    public function testSuccess(): void
    {
        $expectedCommand = new Command(1, 'Test', 'Test');

        $this->handler
            ->expects($this->once())
            ->method('handle')
            ->with($this->equalTo($expectedCommand))
            ->willReturn(Role::fromValues(1, 'Test Role', 'Test Note'));

        $request = $this->createMock(ServerRequestInterface::class);
        $request
            ->method('getParsedBody')
            ->willReturn([
                'id' => 1,
                'name' => 'Test',
                'note' => 'Test',
            ]);

        $response = $this->action->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertInstanceOf(JsonResponse::class, $response);

        $output = $this->decodeJsonResponse($response);
        $this->assertArrayHasKey('id', $output);
        $this->assertSame(1, $output['id']);
        $this->assertArrayHasKey('name', $output);
        $this->assertSame('Test Role', $output['name']);
        $this->assertArrayHasKey('note', $output);
        $this->assertSame('Test Note', $output['note']);
    }
}
