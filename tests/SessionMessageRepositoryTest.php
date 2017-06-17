<?php

namespace Polass\Tests;

use StdClass;
use PHPUnit\Framework\TestCase;
use Mockery;
use Illuminate\Support\Collection;
use Polass\Message\Message;
use Polass\Message\MessageBottle;
use Polass\Message\MessageManager;
use Polass\Message\SessionMessageRepository;
use Polass\Message\Exceptions\CollectionContainsNonMessage;

class SessionMessageRepositoryTest extends TestCase
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
     * `getSessionKey()` のテスト
     *
     */
    public function testGetSessionKey()
    {
        $repository = new SessionMessageRepository(null);

        $this->assertTrue(is_string($repository->getSessionKey()));
    }

    /**
     * `validate()` の正常系のテスト
     *
     */
    public function testValidate()
    {
        $message = new Message;
        $bottle  = new MessageBottle(null, $message);

        $repository = new SessionMessageRepository(null);


        // 全て Message

        $collection = $repository->validate(new Collection([
            $message, $message, $message
        ]));

        $this->assertInstanceOf(Collection::class, $collection);

        foreach ($collection as $item) {
            $this->assertEquals($message, $item);
        }


        // 混在

        $collection = $repository->validate(new Collection([
            $message, $message, $bottle
        ]));

        $this->assertInstanceOf(Collection::class, $collection);

        foreach ($collection as $item) {
            $this->assertEquals($message, $item);
        }


        // 全て MessageBottle

        $collection = $repository->validate(new Collection([
            $bottle, $bottle, $bottle
        ]));

        $this->assertInstanceOf(Collection::class, $collection);

        foreach ($collection as $item) {
            $this->assertEquals($message, $item);
        }


        // 空っぽ

        $collection = $repository->validate(new Collection);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(0, $collection->count());
    }

    /**
     * `validate()` の異常系のテスト
     *
     * @dataProvider provideInvalidParameters
     * @expectedException \Polass\Message\Exceptions\CollectionContainsNonMessage
     */
    public function testValidateInvalid(Collection $collection)
    {
        $repository = new SessionMessageRepository(null);

        $repository->validate($collection);
    }

    /**
     * `validate()` の異常系テストのためのデータプロバイダ
     *
     */
    public function provideInvalidParameters()
    {
        $message = new Message;
        $bottle  = new MessageBottle(null, $message);
        $invalid = new StdClass;

        return [
            [ new Collection([ $invalid ]) ],
            [ new Collection([ $message, $invalid ]) ],
            [ new Collection([ $bottle, $invalid ]) ],
            [ new Collection([ $message, $bottle, $invalid ]) ],
            [ new Collection([ $invalid, $invalid ]) ],
        ];
    }

    /**
     * `exists()` のテスト
     *
     */
    public function testExists()
    {
        $session = Mockery::mock();
        $session->shouldReceive('has')->once()->andReturn(true);

        $app = [
            'session' => $session,
        ];

        $repository = new SessionMessageRepository($app);

        $this->assertTrue($repository->exists());
    }

    /**
     * `load()` のテスト
     *
     */
    public function testLoad()
    {
        // セッションに存在しない

        $session = Mockery::mock();
        $session->shouldReceive('get')->once()->andReturn(null);

        $app = [
            'session' => $session,
        ];

        $repository = new SessionMessageRepository($app);

        $messages = $repository->load();

        $this->assertInstanceOf(Collection::class, $messages);
        $this->assertEquals(0, $messages->count());


        // セッションに存在する

        $message = new Message;

        $session = Mockery::mock();
        $session->shouldReceive('get')->once()->andReturn([
            $message, $message, $message
        ]);

        $app = [
            'session' => $session,
        ];

        $repository = new SessionMessageRepository($app);

        $messages = $repository->load();

        $this->assertInstanceOf(Collection::class, $messages);
        $this->assertEquals(3, $messages->count());

        foreach ($messages as $item) {
            $this->assertInstanceOf(Message::class, $item);
            $this->assertEquals($message, $item);
        }
    }

    /**
     * `forget()` のテスト
     *
     */
    public function testForget()
    {
        $session = Mockery::mock();
        $session->shouldReceive('forget')->once();

        $app = [
            'session' => $session,
        ];

        $repository = new SessionMessageRepository($app);

        $repository->forget();
    }

    /**
     * `save()` のテスト
     *
     */
    public function testSave()
    {
        $message = new Message;
        $messages = new Collection([ $message, $message, $message ]);

        $session = Mockery::mock();
        $session->shouldReceive('flash')->once()
            ->with((new SessionMessageRepository(null))->getSessionKey(), $messages->all());

        $app = [
            'session' => $session,
        ];

        $repository = new SessionMessageRepository($app);

        $repository->save($messages);
    }

    /**
     * `merge()` のテスト
     *
     */
    public function testMerge()
    {
        $older = new Message([ 'body' => 'OLD' ]);
        $olders = new Collection([ $older, $older, $older ]);

        $newer = new Message([ 'body' => 'NEW' ]);
        $newers = new Collection([ $newer, $newer, $newer ]);

        $sessionKey = (new SessionMessageRepository(null))->getSessionKey();

        $session = Mockery::mock();
        $session->shouldReceive('get')->andReturn($olders->all());
        $session->shouldReceive('flash')->once()
            ->with($sessionKey, Mockery::contains($older, $newer));

        $app = [
            'session' => $session,
        ];

        $repository = new SessionMessageRepository($app);

        $repository->merge($newers);
    }

    /**
     * `push()` のテスト
     *
     */
    public function testPush()
    {
        $older = new Message([ 'body' => 'OLD' ]);
        $olders = new Collection([ $older, $older, $older ]);

        $newer = new Message([ 'body' => 'NEW' ]);

        $sessionKey = (new SessionMessageRepository(null))->getSessionKey();

        $session = Mockery::mock();
        $session->shouldReceive('get')->andReturn($olders->all());
        $session->shouldReceive('flash')->once()
            ->with($sessionKey, Mockery::contains($older, $newer));

        $app = [
            'session' => $session,
        ];

        $repository = new SessionMessageRepository($app);

        $repository->push($newer);
    }
}
