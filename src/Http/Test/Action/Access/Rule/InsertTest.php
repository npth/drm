<?php

declare(strict_types=1);

namespace Action\Access\Rule;

use App\Access\Rule\Command\Insert\Command;
use App\Access\Rule\Command\Insert\Handler;
use App\Access\Rule\Entity\Rule;
use App\Http\Action\Access\Rule\Transformer;
use App\Http\Test\Action\JsonResponseTrait;
use App\Http\Action\Access\Rule\Insert;
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
            ->method('transformRule')
            ->willReturn(['id' => 123, 'name' => 'abc']);

        $this->handler
            ->expects($this->once())
            ->method('handle')
            ->willReturn($this->createMock(Rule::class));

        $request = $this->createMock(ServerRequestInterface::class);
        $request
            ->method('getParsedBody')
            ->willReturn([
                'role_id' => 1,
                'name' => 'rule',
                'resource_code' => 'resource',
                'conditions' => [[
                    'category_code' => 'category',
                    'attribute_code' => 'attribute',
                    'operator' => 'like',
                    'value' => 'value',
                ]],
                'actions' => ['a', 'b'],
                'active' => true,
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
