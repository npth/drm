<?php

declare(strict_types=1);

namespace Action\Access\Attribute;

use App\Access\Attribute\Query\Actions\Fetcher;
use App\Access\Attribute\Repository\ActionRegistry;
use App\Http\Action\Access\Attribute\ActionsTransformer;
use App\Http\Test\Action\JsonResponseTrait;
use App\Http\Action\Access\Attribute\Actions;
use App\Http\Response\JsonResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(Actions::class)]
final class ActionsTest extends TestCase
{
    use JsonResponseTrait;

    private Fetcher&MockObject $fetcher;
    private ActionsTransformer&MockObject $transformer;
    private Actions $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fetcher = $this->createMock(Fetcher::class);
        $this->transformer = $this->createMock(ActionsTransformer::class);
        $this->action = new Actions($this->fetcher, $this->transformer);
    }
    public function testSuccess(): void
    {
        $registry = $this->createMock(ActionRegistry::class);
        $this->fetcher
            ->expects($this->once())
            ->method('fetch')
            ->willReturn($registry);

        $expectedData = [
            ['code' => 'action1', 'name' => 'Действие 1'],
            ['code' => 'action2', 'name' => 'Действие 2'],
        ];
        $this->transformer
            ->expects($this->once())
            ->method('transform')
            ->with($registry)
            ->willReturn($expectedData);

        $response = $this->action->handle($this->createMock(ServerRequestInterface::class));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertInstanceOf(JsonResponse::class, $response);
        $output = $this->decodeJsonResponse($response);

        $this->assertArrayHasKey('data', $output);
        $this->assertEquals($expectedData, $output['data']);
    }
}
