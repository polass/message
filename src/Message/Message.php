<?php

namespace Polass\Message;

use Illuminate\Support\HtmlString;
use Polass\Fluent\Model;
use Polass\Message\Contracts\Message as MessageContract;

class Message extends Model implements MessageContract
{
    /**
     * 本文を取得
     *
     * @param string $value
     * @return string
     */
    public function getBodyAttribute($value)
    {
        return $this->escape ? $value : new HtmlString($value);
    }

    /**
     * アイコンは複数指定可能
     *
     * @param (string|array) $value
     * @return void
     */
    protected function setIconAttribute($value)
    {
        $this->attributes['icon'] = $this->joinTokens($value) ?: null;
    }

    /**
     * エスケープするかどうかは `bool` 型
     *
     * @param mixed $value
     * @return void
     */
    protected function setEscapeAttribute($value)
    {
        $this->attributes['escape'] = (bool)$value;
    }

    /**
     * 配列またはスペース区切りの単語を結合
     *
     * @param (string|array) $tokens
     * @return string
     */
    protected function joinTokens($tokens)
    {
        return trim(preg_replace('/\s+/m', ' ', implode(' ', (array)$tokens)));
    }
}
