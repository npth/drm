<?php

declare(strict_types=1);

namespace App\Http\Test\Action\Access\Attribute;

use App\Access\Attribute\Repository\AttributeRegistry;
use App\Http\Action\Access\Attribute\CategoriesTransformer;
use App\Http\Test\Action\JsonResponseTrait;
use App\Access\Attribute\Query\Categories\Fetcher;
use App\Http\Action\Access\Attribute\Categories;
use App\Http\Response\JsonResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(Categories::class)]
final class CategoriesTest extends TestCase
{
    use JsonResponseTrait;

    private Fetcher&MockObject $fetcher;
    private CategoriesTransformer&MockObject $transformer;
    private Categories $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fetcher = $this->createMock(Fetcher::class);
        $this->transformer = $this->createMock(CategoriesTransformer::class);
        $this->action = new Categories($this->fetcher, $this->transformer);
    }
    public function testSuccess(): void
    {
        $registry = $this->createMock(AttributeRegistry::class);
        $this->fetcher
            ->expects($this->once())
            ->method('fetch')
            ->willReturn($registry);

        $expectedData = [
            ['code' => 'category1', 'name' => 'Категория 1'],
            ['code' => 'category2', 'name' => 'Категория 2'],
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
