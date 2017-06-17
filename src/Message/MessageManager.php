<?php

namespace Polass\Message;

use Polass\Message\MessageBottle;

class MessageManager
{
    /**
     * アプリケーションインスタンス
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * メッセージのプロパティの初期値
     *
     * @var string
     */
    protected $defaults = [
        'icon'   => null,
        'color'  => 'default',
        'body'   => '',
        'escape' => true,
    ];

    /**
     * コンストラクタ
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Message インスタンスを作成する際のデフォルト値をまとめて取得
     *
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Message インスタンスを作成する際のデフォルト値をまとめて設定
     *
     * @param array $defaults
     * @return $this
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;

        return $this;
    }

    /**
     * Message インスタンスを作成する際のデフォルト値を設定
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setDefault($key, $value)
    {
        $this->defaults[$key] = $value;

        return $this;
    }

    /**
     * 新しい Message インスタンスを作成
     *
     * @param array $attributes
     * @return \Polass\Message\Message
     */
    public function newMessage(array $attributes)
    {
        return $this->app['polass.message']->fill($this->defaults)->fill($attributes);
    }

    /**
     * Message インスタンスを MessageBottle に入れる
     *
     * @param \Polass\Message\Message $message
     * @return \Polass\Message\MessageBottle
     */
    public function bottle(Message $message)
    {
        return new MessageBottle($this->app, $message);
    }

    /**
     * 新しい Message インスタンスを作成
     *
     * @param array $attributes
     * @return \Polass\Message\MessageBottle
     */
    public function make(array $attributes)
    {
        return $this->bottle($this->newMessage($attributes));
    }

    /**
     * 新しい Message インスタンスを作成し保存
     *
     * @param array $attributes
     * @return \Polass\Message\Message
     */
    public function create(array $attributes)
    {
        return $this->make($attributes)->save();
    }

    /**
     * 定義されていないメソッド呼び出しは MessageRepository に委譲
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->app['polass.message.repository']->$method(...$parameters);
    }
}
