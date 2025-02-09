<?php
declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Psr\Log\NullLogger;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class AbstractApiTest extends ApiTestCase
{
    use ResetDatabase;

    public function setUp(): void
    {
        self::bootKernel([
        ]);

        // Disable Symfony verbose logging during tests
        // Replace the logger with a NullLogger to suppress logs
        $container = self::getContainer();
        $container->set('logger', new NullLogger());
    }

    protected function performJsonApiRequest(string $method, string $url, array $payload = []): ResponseInterface
    {
        return static::createApiClient()
            ->request(method: $method, url: $url, options: [
                'json' => $payload,
            ]);
    }

    protected function createApiClient(): Client
    {
        return static::createClient([
        ], [
            'headers' => [
                'Accept'       => 'application/vnd.api+json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }
}
