<?php

namespace Polass\Message\Exceptions;

use LogicException;

class CollectionContainsNonMessage extends LogicException
{
    /**
     * 含まれていた異物の配列
     *
     * @var array
     */
    protected $impurities;

    /**
     * 含まれていた異物を設定
     *
     * @param  array  $impurities
     * @return $this
     */
    public function setImpurities(array $impurities)
    {
        $this->impurities = $impurities;

        return $this;
    }

    /**
     * 含まれていた異物を取得
     *
     * @return array
     */
    public function getImpurities()
    {
        return $this->impurities;
    }
}
