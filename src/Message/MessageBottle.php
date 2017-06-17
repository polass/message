<?php

namespace Polass\Message;

use Polass\Message\Contracts\Message as MessageContract;

class MessageBottle
{
    /**
     * アプリケーションインスタンス
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Message のインスタンス
     *
     * @var \Polass\Message\Contracts\Message
     */
    protected $message;

    /**
     * コンストラクタ
     *
     * @param \Illuminate\Foundation\Application  $app
     * @param \Polass\Message\Contracts\Message $message
     */
    public function __construct($app, MessageContract $message)
    {
        $this->app = $app;
        $this->message = $message;
    }

    /**
     * Message のインスタンスを取得
     *
     * @return \Polass\Message\Contracts\Message
     */
    public function retrieve()
    {
        return $this->message;
    }

    /**
     * Message をセッションに保存
     *
     * @return $this
     */
    public function save()
    {
        $this->app['polass.message.repository']->push($this->message);

        return $this;
    }

    /**
     * Message の内容をログに出力
     *
     * @return $this
     */
    public function log($level = 'debug')
    {
        $this->app['log']->$level($this->message->toArray());

        return $this;
    }
}
