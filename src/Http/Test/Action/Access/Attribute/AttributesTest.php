<?php

declare(strict_types=1);

namespace Action\Access\Attribute;

use App\Access\Attribute\Entity\Category;
use App\Access\Attribute\Query\Attributes\Fetcher;
use App\Http\Action\Access\Attribute\AttributesTransformer;
use App\Http\Test\Action\JsonResponseTrait;
use App\Http\Action\Access\Attribute\Attributes;
use App\Http\Response\JsonResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(Attributes::class)]
final class AttributesTest extends TestCase
{
    use JsonResponseTrait;

    private Fetcher&MockObject $fetcher;
    private AttributesTransformer&MockObject $transformer;
    private Attributes $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fetcher = $this->createMock(Fetcher::class);
        $this->transformer = $this->createMock(AttributesTransformer::class);
        $this->action = new Attributes($this->fetcher, $this->transformer);
    }
    public function testSuccess(): void
    {
        $expectCategories = [
            Category::fromValues(
                'code',
                'name',
                new \App\FlexConditions\Attribute\Attributes([])
            )
        ];
        $this->fetcher
            ->expects($this->once())
            ->method('fetch')
            ->with([])
            ->willReturn($expectCategories);

        $expectedData = [
            ['code' => 'attribute1', 'name' => 'Атрибут 1'],
            ['code' => 'attribute2', 'name' => 'Атрибут 2'],
        ];
        $this->transformer->expects($this->once())
            ->method('transform')
            ->with($expectCategories)
            ->willReturn($expectedData);

        $request = $this->createMock(ServerRequestInterface::class);
        $request
            ->method('getQueryParams')
            ->willReturn([]);

        $response = $this->action->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertInstanceOf(JsonResponse::class, $response);
        $output = $this->decodeJsonResponse($response);

        $this->assertArrayHasKey('data', $output);
        $this->assertEquals($expectedData, $output['data']);
    }
}
