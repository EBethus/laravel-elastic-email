<?php

namespace Chocoholics\LaravelElasticEmail\Tests;

use Chocoholics\LaravelElasticEmail\ElasticTransport;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class ElasticTransportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $container = new Container();
        $container->instance('log', new class {
            public function debug($message, array $context = []): void
            {
            }

            public function warning($message, array $context = []): void
            {
            }

            public function error($message, array $context = []): void
            {
            }
        });

        Facade::setFacadeApplication($container);
    }

    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);

        parent::tearDown();
    }

    public function test_it_sends_postback_when_header_exists(): void
    {
        $postback = 'coupon_id=123;event_id=456;mail_type=invitation';
        $email = $this->makeEmail();
        $email->getHeaders()->addTextHeader('X-ElasticEmail-Postback', $postback);

        [$request, $payload] = $this->sendAndGetPayload($email);

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('https://api.elasticemail.com/v2/email/send', (string) $request->getUri());
        $this->assertSame($postback, $payload['postback'] ?? null);
        $this->assertFalse($email->getHeaders()->has('X-ElasticEmail-Postback'));
    }

    public function test_it_does_not_send_postback_when_header_does_not_exist(): void
    {
        [$request, $payload] = $this->sendAndGetPayload($this->makeEmail());

        $this->assertSame('https://api.elasticemail.com/v2/email/send', (string) $request->getUri());
        $this->assertArrayNotHasKey('postback', $payload);
    }

    private function makeEmail(): Email
    {
        return (new Email())
            ->from('sender@example.com')
            ->to('recipient@example.com')
            ->subject('Test subject')
            ->text('Test body');
    }

    /**
     * @return array{0: \Psr\Http\Message\RequestInterface, 1: array<string, mixed>}
     */
    private function sendAndGetPayload(Email $email): array
    {
        $history = [];
        $mock = new MockHandler([
            new Response(200, [], json_encode(['success' => true])),
        ]);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));

        $transport = new ElasticTransport(new Client(['handler' => $stack]), [
            'key' => 'elastic-key',
            'account' => 'elastic-account',
            'transactional' => true,
        ]);

        $transport->send($email, new Envelope(
            new Address('sender@example.com'),
            [new Address('recipient@example.com')]
        ));

        $this->assertCount(1, $history);

        parse_str((string) $history[0]['request']->getBody(), $payload);

        return [$history[0]['request'], $payload];
    }
}
