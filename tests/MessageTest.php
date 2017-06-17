<?php

namespace Polass\Tests;

use PHPUnit\Framework\TestCase;
use Illuminate\Support\HtmlString;
use Polass\Message\Message;

class MessageTest extends TestCase
{
    /**
     * `getBodyAttribute()` のテスト
     *
     */
    public function testGetBodyAttribute()
    {
        $escaped = [
            'body'   => 'This string needs to be escaped.',
            'escape' => true,
        ];

        $this->assertEquals(
            (new Message($escaped))->body, $escaped['body']
        );

        $plain = [
            'body'   => 'This string not needs to be escaped.',
            'escape' => false,
        ];

        $this->assertInstanceOf(
            HtmlString::class, (new Message($plain))->body
        );
    }

    /**
     * `setIconAttribute()` のテスト
     *
     */
    public function testSetIconAttribute()
    {
        $this->assertNull(
            (new Message)->icon(null)->icon
        );

        $this->assertNull(
            (new Message)->icon('')->icon
        );

        $this->assertEquals(
            'foo',
            (new Message)->icon('foo')->icon
        );

        $this->assertEquals(
            'foo',
            (new Message)->icon(' foo ')->icon
        );

        $this->assertEquals(
            'foo bar',
            (new Message)->icon('foo  bar')->icon
        );

        $this->assertEquals(
            'foo',
            (new Message)->icon([ 'foo' ])->icon
        );

        $this->assertEquals(
            'foo',
            (new Message)->icon([ ' foo ' ])->icon
        );

        $this->assertEquals(
            'foo bar',
            (new Message)->icon([ 'foo', 'bar' ])->icon
        );

        $this->assertEquals(
            'foo bar baz',
            (new Message)->icon([ 'foo bar', 'baz' ])->icon
        );

        $this->assertEquals(
            'foo bar baz',
            (new Message)->icon([ 'foo  bar', ' baz' ])->icon
        );
    }
}
