<?php

namespace App\Http\Test\Action;

use App\Http\Response\JsonResponse;
use Psr\Http\Message\StreamInterface;

trait JsonResponseTrait
{
    protected function decodeJsonResponse(JsonResponse $response): array
    {
        $body = $response->getBody();
        $this->ensureStreamIsReadable($body);

        $content = $body->getContents();
        return $this->decodeJson($content);
    }

    private function ensureStreamIsReadable(StreamInterface $stream): void
    {
        $metadata = $stream->getMetadata('seekable');
        if ($metadata && $stream->isSeekable()) {
            $stream->rewind();
        }
    }

    private function decodeJson(string $content): array
    {
        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($data)) {
            throw new \RuntimeException('JSON response is not an object or array');
        }

        return $data;
    }
}
