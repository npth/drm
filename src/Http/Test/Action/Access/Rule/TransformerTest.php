<?php

declare(strict_types=1);

namespace Action\Access\Rule;

use App\Access\Attribute\Entity\Action;
use App\Access\Attribute\Entity\Actions;
use App\Access\Attribute\Entity\CategoryInfo;
use App\Access\Role\Entity\Id as RoleId;
use App\Access\Rule\Entity\Constraint;
use App\Access\Rule\Entity\Constraints;
use App\Access\Rule\Entity\Id;
use App\Access\Rule\Entity\Name;
use App\Access\Rule\Entity\Rule;
use App\Access\Rule\Entity\Status;
use App\Http\Action\Access\Rule\Transformer;
use App\FlexConditions\Attribute\AttributeMeta;
use App\FlexConditions\Attribute\Type;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Transformer::class)]
final class TransformerTest extends TestCase
{
    public function testSuccess(): void
    {
        $rule = new Rule(
            Id::fromValue(1),
            RoleId::fromValue(1),
            Name::fromValue('rule 1'),
            CategoryInfo::fromValues('crm-clients', 'Клиенты'),
            new Constraints([
                new Constraint(
                    CategoryInfo::fromValues('crm-clients', 'Клиенты'),
                    AttributeMeta::fromValues('name', 'Наименование', Type::STRING, false),
                    'like',
                    'менеджер'
                ),
            ]),
            new Actions([Action::fromValues('view', 'смотреть')]),
            new Status(true),
        );
        $transformer = new Transformer();
        $transformedRow = $transformer->transformRule($rule);

        $this->assertSame([
            'id' => 1,
            'name' => 'rule 1',
            'resource_code' => 'crm-clients',
            'resource' => [
                'code' => 'crm-clients',
                'name' => 'Клиенты',
            ],
            'conditions' => [[
                'category' => [
                    'code' => 'crm-clients',
                    'name' => 'Клиенты',
                ],
                'attribute' => [
                    'code' => 'name',
                    'name' => 'Наименование',
                    'type_code' => 'string',
                    'type_name' => 'Строка',
                ],
                'operator' => 'like',
                'value' => 'менеджер',
            ]],
            'actions' => ['view'],
            'status' => true,
        ], $transformedRow);
    }

    public function testList(): void
    {
        $transformer = $this->getMockBuilder(Transformer::class)
            ->onlyMethods(['transformRule'])
            ->getMock();
        $transformer->expects(self::once())
            ->method('transformRule')
            ->willReturn(['a']);

        $result = $transformer->transform([$this->createMock(Rule::class)]);
        $this->assertSame([['a']], $result);
    }
}
