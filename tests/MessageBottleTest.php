<?php

namespace Polass\Tests;

use PHPUnit\Framework\TestCase;
use Mockery;
use Polass\Message\Message;
use Polass\Message\MessageBottle;
use Polass\Message\SessionMessageRepository;

class MessageBottleTest extends TestCase
{
    /**
     * テストの後始末
     *
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * `retrieve()` のテスト
     *
     */
    public function testRetrieve()
    {
        $message = new Message;
        $bottle = new MessageBottle(null, $message);

        $this->assertEquals($message, $bottle->retrieve());
    }

    /**
     * `save()` のテスト
     *
     */
    public function testSave()
    {
        $message = new Message;

        $repository = Mockery::mock(SessionMessageRepository::class);
        $repository->shouldReceive('push')->once()->with($message);

        $app = [
            'polass.message.repository' => $repository,
        ];

        $bottle = new MessageBottle($app, $message);

        $this->assertEquals($bottle, $bottle->save());
    }

    /**
     * `log()` のテスト
     *
     */
    public function testLog()
    {
        $message = new Message;

        $logger = Mockery::mock();
        $logger->shouldReceive('debug')->once()->with($message->toArray());
        $logger->shouldReceive('info')->once()->with($message->toArray());

        $app = [
            'log' => $logger,
        ];

        $bottle = new MessageBottle($app, $message);

        $this->assertEquals($bottle, $bottle->log());
        $this->assertEquals($bottle, $bottle->log('info'));
    }
}
