<?php

declare(strict_types=1);

namespace Action\Access\Attribute;

use App\Access\Attribute\Entity\Categories;
use App\Access\Attribute\Entity\Category;
use App\Access\Attribute\Entity\CategoryCode;
use App\Access\Attribute\Entity\Group;
use App\Access\Attribute\Repository\AttributeRegistry;
use App\Http\Action\Access\Attribute\CategoriesTransformer;
use App\FlexConditions\Attribute\Attributes;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(CategoriesTransformer::class)]
final class CategoriesTransformerTest extends TestCase
{
    private AttributeRegistry&MockObject $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry = $this->createMock(AttributeRegistry::class);
    }

    public function testSuccess(): void
    {
        // Создаём тестовые данные
        $contextGroup = Group::fromValues(
            'context',
            'Контекст',
            new Categories([
                Category::fromValues(CategoryCode::ENVIRONMENT, 'Среда', new Attributes([])),
                Category::fromValues(CategoryCode::SUBJECT, 'Субъект', new Attributes([])),
            ])
        );

        $resourceGroup = Group::fromValues(
            'resource',
            'Ресурс',
            new Categories([
                Category::fromValues('access-rules', 'Правила', new Attributes([])),
            ])
        );

        $this->registry
            ->method('getGroups')
            ->willReturn([
                'context' => $contextGroup,
                'resource' => $resourceGroup,
            ]);

        $transformer = new CategoriesTransformer();
        $result = $transformer->transform($this->registry);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('context', $result);
        $this->assertArrayHasKey('resource', $result);

        $this->assertIsArray($result['context']);
        $this->assertSame('Контекст', $result['context']['name']);
        $this->assertSame('context', $result['context']['code']);
        $this->assertCount(2, $result['context']['categories']);

        $this->assertSame('Среда', $result['context']['categories'][0]['name']);
        $this->assertSame(CategoryCode::ENVIRONMENT, $result['context']['categories'][0]['code']);
        $this->assertSame('Субъект', $result['context']['categories'][1]['name']);
        $this->assertSame(CategoryCode::SUBJECT, $result['context']['categories'][1]['code']);

        $this->assertIsArray($result['resource']);
        $this->assertSame('Ресурс', $result['resource']['name']);
        $this->assertSame('resource', $result['resource']['code']);
        $this->assertCount(1, $result['resource']['categories']);

        $this->assertSame('Правила', $result['resource']['categories'][0]['name']);
        $this->assertSame('access-rules', $result['resource']['categories'][0]['code']);
    }

    public function testTransformReturnsEmptyCategoriesWhenNoCategories(): void
    {
        $emptyGroup = Group::fromValues('empty', 'Пустая', new Categories([]));

        $this->registry
            ->method('getGroups')
            ->willReturn(['empty' => $emptyGroup]);

        $transformer = new CategoriesTransformer();
        $result = $transformer->transform($this->registry);

        $this->assertArrayHasKey('empty', $result);
        $this->assertIsArray($result['empty']['categories']);
        $this->assertCount(0, $result['empty']['categories']);
    }

    public function testTransformReturnsEmptyArrayWhenRegistryReturnsEmpty(): void
    {
        $this->registry
            ->method('getGroups')
            ->willReturn([]);

        $transformer = new CategoriesTransformer();
        $result = $transformer->transform($this->registry);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testTransformHandlesSpecialCharactersInNames(): void
    {
        $group = Group::fromValues(
            'crm-clients',
            'CRM \ Клиенты',
            new Categories([
                Category::fromValues('email', 'Электронная почта', new Attributes([])),
            ])
        );

        $this->registry
            ->method('getGroups')
            ->willReturn(['crm' => $group]);

        $transformer = new CategoriesTransformer();
        $result = $transformer->transform($this->registry);

        $this->assertSame('CRM \ Клиенты', $result['crm']['name']);
        $this->assertSame('email', $result['crm']['categories'][0]['code']);
        $this->assertSame('Электронная почта', $result['crm']['categories'][0]['name']);
    }
}
