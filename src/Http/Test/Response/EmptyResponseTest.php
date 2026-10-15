<?php

declare(strict_types=1);

namespace App\Http\Test\Response;

use App\Http\Response\EmptyResponse;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EmptyResponse::class)]
final class EmptyResponseTest extends TestCase
{
    public function testSuccess(): void
    {
        $response = new EmptyResponse();
        self::assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
        self::assertFalse($response->hasHeader('Content-Type'));

        self::assertSame('', (string)$response->getBody());
        self::assertFalse($response->getBody()->isWritable());
    }

    public function testWithCode(): void
    {
        $response = new EmptyResponse(StatusCodeInterface::STATUS_CREATED);

        self::assertSame(StatusCodeInterface::STATUS_CREATED, $response->getStatusCode());
    }
}
