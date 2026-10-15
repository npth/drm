<?php

declare(strict_types=1);

namespace Action\Access\Attribute;

use App\Access\Attribute\Entity\Category;
use App\Access\Attribute\Entity\CategoryName;
use App\Access\Attribute\Entity\CategoryCode;
use App\FlexConditions\Attribute\EntityAttribute;
use App\FlexConditions\Attribute\ResolvedAttribute;
use App\Http\Action\Access\Attribute\AttributesTransformer;
use App\FlexConditions\Attribute\AttributeMeta;
use App\FlexConditions\Attribute\Attributes;
use App\FlexConditions\Attribute\Type;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AttributesTransformer::class)]
final class AttributesTransformerTest extends TestCase
{
    public function testSuccess(): void
    {
        $category = new Category(
            CategoryCode::fromValue('code'),
            CategoryName::fromValue('name'),
            new Attributes([
                new ResolvedAttribute(
                    AttributeMeta::fromValues('acode', 'aname', Type::STRING, false),
                    new EntityAttribute('acode', 'aname'),
                )
            ])
        );
        $transformer = new AttributesTransformer();
        $result = $transformer->transform([$category]);
        $this->assertIsArray($result);
        $this->assertSame([[
            'code' => 'code',
            'name' => 'name',
            'attributes' => [[
                'code' => 'acode',
                'name' => 'aname',
                'type_code' => Type::STRING->value,
                'type_name' => Type::STRING->getName(),
            ]]
        ]], $result);
    }
}
