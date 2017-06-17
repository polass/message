<?php

namespace Polass\Message\Contracts;

use Illuminate\Support\Collection;
use Polass\Message\Contracts\Message as MessageContract;

interface MessageRepository
{
    /**
     * セッションにメッセージが存在するかどうか
     *
     * @return bool
     */
    public function exists();

    /**
     * セッションからメッセージを読み込む
     *
     * @return \Illuminate\Support\Collection
     */
    public function load();

    /**
     * メッセージリストをクリア
     *
     * @return void
     */
    public function forget();

    /**
     * メッセージをセッションに保存
     *
     * @param \Illuminate\Support\Collection $messages
     * @return void
     */
    public function save(Collection $messages);

    /**
     * 複数のメッセージをセッションに追加
     *
     * @param \Illuminate\Support\Collection $messages
     * @return void
     */
    public function merge(Collection $messages);

    /**
     * メッセージをセッションに追加
     *
     * @param \Polass\Message\Contracts\Message $message
     * @return void
     */
    public function push(MessageContract $messages);
}
