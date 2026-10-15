<?php

declare(strict_types=1);

namespace Action\Access\Attribute;

use App\Access\Attribute\Query\AttributeTypes\Fetcher;
use App\Http\Test\Action\JsonResponseTrait;
use App\Http\Action\Access\Attribute\AttributeTypes;
use App\Http\Response\JsonResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(AttributeTypes::class)]
final class AttributeTypesTest extends TestCase
{
    use JsonResponseTrait;

    private Fetcher&MockObject $fetcher;
    private AttributeTypes $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fetcher = $this->createMock(Fetcher::class);
        $this->action = new AttributeTypes($this->fetcher);
    }
    public function testSuccess(): void
    {
        $expectedData = [
            ['code' => 'type1', 'name' => 'Тип 1', 'operators' => ['=', '>']],
            ['code' => 'type2', 'name' => 'Тип 2', 'operators' => ['in', 'like']],
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
        $this->assertEquals($expectedData, $output['data']);
    }
}
