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
use App\Access\Rule\Query\List\Fetcher;
use App\Http\Action\Access\Rule\Transformer;
use App\Http\Test\Action\JsonResponseTrait;
use App\Http\Action\Access\Rule\Listing;
use App\Http\Response\JsonResponse;
use App\FlexConditions\Attribute\AttributeMeta;
use App\FlexConditions\Attribute\Type;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(Listing::class)]
final class ListingTest extends TestCase
{
    use JsonResponseTrait;

    private Fetcher&MockObject $fetcher;
    private Listing $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fetcher = $this->createMock(Fetcher::class);
        $this->action = new Listing($this->fetcher, new Transformer());
    }
    public function testSuccess(): void
    {
        $expectedData = [
            new Rule(
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
            ),
            new Rule(
                Id::fromValue(2),
                RoleId::fromValue(1),
                Name::fromValue('rule 2'),
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
            ),
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
        $this->assertCount(2, $output['data']);

        $datum1 = $output['data'][0];
        $this->assertArrayHasKey('id', $datum1);
        $this->assertEquals(1, $datum1['id']);
        $this->assertArrayHasKey('name', $datum1);
        $this->assertEquals('rule 1', $datum1['name']);

        $datum2 = $output['data'][1];
        $this->assertArrayHasKey('id', $datum2);
        $this->assertEquals(2, $datum2['id']);
        $this->assertArrayHasKey('name', $datum2);
        $this->assertEquals('rule 2', $datum2['name']);
    }
}
