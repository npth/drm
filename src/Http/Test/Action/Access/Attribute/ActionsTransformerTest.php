<?php

declare(strict_types=1);

namespace Action\Access\Attribute;

use App\Access\Attribute\Entity\Action;
use App\Access\Attribute\Repository\ActionRegistry;
use App\Http\Action\Access\Attribute\ActionsTransformer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ActionsTransformer::class)]
final class ActionsTransformerTest extends TestCase
{
    public function testSuccess(): void
    {
        $registry = $this->createMock(ActionRegistry::class);
        $registry->expects($this->once())
            ->method('getActions')
            ->willReturn([Action::fromValues('code', 'name')]);

        $transformer = new ActionsTransformer();
        $result = $transformer->transform($registry);
        $this->assertIsArray($result);
        $this->assertSame([[
            'code' => 'code',
            'name' => 'name',
        ]], $result);
    }
}
