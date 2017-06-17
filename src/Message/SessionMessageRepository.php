<?php

namespace Polass\Message;

use Illuminate\Support\Collection;
use Polass\Message\Contracts\Message as MessageContract;
use Polass\Message\Contracts\MessageRepository as MessageRepositoryContract;
use Polass\Message\Exceptions\CollectionContainsNonMessage;
use Polass\Message\MessageBottle;

class SessionMessageRepository implements MessageRepositoryContract
{
    /**
     * メッセージをセッションに保存する際のキー
     *
     * @var string
     */
    const SESSION_KEY = 'messages';

    /**
     * アプリケーションインスタンス
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * コンストラクタ
     *
     * @param \Illuminate\Foundation\Application  $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * メッセージをセッションに保存する際のキーを取得
     *
     * @return string
     */
    public function getSessionKey()
    {
        return static::SESSION_KEY;
    }

    /**
     * Collection に含まれるアイテムをバリデーション
     *
     * @param \Illuminate\Support\Collection $messages
     * @return void
     */
    public function validate(Collection $messages)
    {
        $impurities = $messages->reject(function($value, $key) {
            return $value instanceOf MessageContract || $value instanceOf MessageBottle;
        });

        if ($impurities->count() > 0) {
            throw (new CollectionContainsNonMessage)->setImpurities($impurities->all());
        }

        return $messages->map(function($message) {
            if ($message instanceOf MessageBottle) {
                return $message->retrieve();
            }

            return $message;
        });
    }

    /**
     * セッションにメッセージが存在するかどうか
     *
     * @return bool
     */
    public function exists()
    {
        return $this->app['session']->has($this->getSessionKey());
    }

    /**
     * セッションからメッセージを読み込む
     *
     * @return \Illuminate\Support\Collection
     */
    public function load()
    {
        $messages = $this->app['session']->get($this->getSessionKey());

        $instances = new Collection;

        if ($messages !== null) {
            foreach ($messages as $message) {
                $instances->push($message);
            }
        }

        return $instances;
    }

    /**
     * メッセージリストをクリア
     *
     * @return void
     */
    public function forget()
    {
        $this->app['session']->forget($this->getSessionKey());
    }

    /**
     * メッセージをセッションに保存
     *
     * @param \Illuminate\Support\Collection $messages
     * @return void
     */
    public function save(Collection $messages)
    {
        $messages = $this->validate($messages);

        $this->app['session']->flash($this->getSessionKey(), $messages->all());
    }

    /**
     * 複数のメッセージをセッションに追加
     *
     * @param \Illuminate\Support\Collection $messages
     * @return void
     */
    public function merge(Collection $messages)
    {
        $messages = $this->validate($messages);

        $this->save($this->load()->merge($messages));
    }

    /**
     * メッセージをセッションに追加
     *
     * @param \Polass\Message\Contracts\Message $message
     * @return void
     */
    public function push(MessageContract $message)
    {
        $this->save($this->load()->push($message));
    }
}
