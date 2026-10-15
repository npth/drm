<?php

declare(strict_types=1);

namespace Action\Access\Role;

use App\Access\Role\Entity\Role;
use App\Http\Action\Access\Role\Transformer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Transformer::class)]
final class TransformerTest extends TestCase
{
    public function testSuccess(): void
    {
        $role1 = Role::fromValues(1, 'name1', 'note1');
        $role2 = Role::fromValues(2, 'name2', 'note2');
        $transformer = new Transformer();
        $transformedRows = $transformer->transform([$role1, $role2]);

        $this->assertCount(2, $transformedRows);
        $this->assertEquals(1, $transformedRows[0]['id']);
        $this->assertEquals('name1', $transformedRows[0]['name']);
        $this->assertEquals('note1', $transformedRows[0]['note']);
        $this->assertEquals(2, $transformedRows[1]['id']);
        $this->assertEquals('name2', $transformedRows[1]['name']);
        $this->assertEquals('note2', $transformedRows[1]['note']);
    }
}
