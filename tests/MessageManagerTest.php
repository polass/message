<?php

namespace Polass\Tests;

use PHPUnit\Framework\TestCase;
use Mockery;
use Polass\Message\Message;
use Polass\Message\MessageBottle;
use Polass\Message\MessageManager;
use Polass\Message\SessionMessageRepository;

class MessageManagerTest extends TestCase
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
     * `getDefaults()` のテスト
     *
     */
    public function testGetDefaults()
    {
        $this->assertTrue(is_array(
            (new MessageManager(null))->getDefaults()
        ));
    }

    /**
     * `setDefaults()` のテスト
     *
     */
    public function testSetDefaults()
    {
        $defaults = [
            'body'  => 'BODY',
            'escape' => false,
        ];

        $manager = new MessageManager(null);
        $manager->setDefaults($defaults);

        $this->assertEquals(
            $defaults, $manager->getDefaults()
        );
    }

    /**
     * `setDefault()` のテスト
     *
     */
    public function testSetDefault()
    {
        $key = 'KEY';
        $value = 'VALUE';

        $manager = new MessageManager(null);
        $manager->setDefault($key, $value);

        $this->assertArrayHasKey(
            $key, $manager->getDefaults()
        );

        $this->assertEquals(
            $value, $manager->getDefaults()[$key]
        );
    }

    /**
     * `newMessage()` のテスト
     *
     */
    public function testNewMessage()
    {
        $attributes = [
            'body'   => 'BODY',
            'escape' => false,
        ];

        $defaults = [
            'body'   => 'default-body',
            'icon'   => 'default-icon',
            'escape' => true,
        ];

        $app = [
            'polass.message' => new Message,
        ];

        $manager = (new MessageManager($app))->setDefaults($defaults);
        $message = $manager->newMessage($attributes);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals(
            $message->getAttributes(), [
                'body'   => 'BODY',
                'icon'   => 'default-icon',
                'escape' => false,
            ]
        );
    }

    /**
     * `bottle()` のテスト
     *
     */
    public function testBottle()
    {
        $app = [];

        $manager = new MessageManager($app);
        $message = new Message;

        $bottle = $manager->bottle($message);

        $this->assertInstanceOf(
            MessageBottle::class, $bottle
        );

        $this->assertEquals(
            $bottle->retrieve(), $message
        );
    }

    /**
     * `make()` のテスト
     *
     */
    public function testMake()
    {
        $attributes = [
            'body'   => 'BODY',
            'escape' => false,
        ];

        $app = [
            'polass.message' => new Message,
        ];

        $manager = (new MessageManager($app))->setDefaults([]);

        $bottle = $manager->make($attributes);

        $this->assertInstanceOf(MessageBottle::class, $bottle);
        $this->assertInstanceOf(Message::class, $bottle->retrieve());
        $this->assertEquals($attributes, $bottle->retrieve()->getAttributes());
    }

    /**
     * `create()` のテスト
     *
     */
    public function testCreate()
    {
        $attributes = [
            'body'   => 'BODY',
            'escape' => false,
        ];

        $repository = Mockery::mock(SessionMessageRepository::class);
        $repository->shouldReceive('push')->once();

        $app = [
            'polass.message' => new Message,
            'polass.message.repository' => $repository,
        ];

        $manager = (new MessageManager($app))->setDefaults([]);

        $bottle = $manager->create($attributes);

        $this->assertInstanceOf(MessageBottle::class, $bottle);
        $this->assertInstanceOf(Message::class, $bottle->retrieve());
        $this->assertEquals($attributes, $bottle->retrieve()->getAttributes());
    }

    /**
     * `__call()` のテスト
     *
     */
    public function testCall()
    {
        $repository = Mockery::mock(SessionMessageRepository::class);
        $repository->shouldReceive('exists')->once()->andReturn(true);

        $app = [
            'polass.message.repository' => $repository,
        ];

        $manager = new MessageManager($app);

        $this->assertTrue($manager->exists());
    }
}
