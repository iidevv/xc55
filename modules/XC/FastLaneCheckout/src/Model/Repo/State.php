<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FastLaneCheckout\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * The "State" model repository
 *
 * @Extender\Mixin
 */
class State extends \XLite\Model\Repo\State
{
    /**
     * Define cache cells
     *
     * @return array
     */
    protected function defineCacheCells()
    {
        $list = parent::defineCacheCells();

        $list['all_dto'] = [
            self::RELATION_CACHE_CELL => ['\XLite\Model\Country'],
        ];

        return $list;
    }

    /**
     * findAllStatesDTO. Like findAllStates(), but with DTOs
     *
     * @return array
     */
    public function findAllStatesDTO()
    {
        $cacheKey = 'all_dto';
        $data = $this->getFromCache($cacheKey);

        if (!isset($data)) {
            $states = $this->findAllStates();
            $data = array_map(static function ($item) {
                return [
                    "key" => $item->getStateId(),
                    "name" => $item->getState()
                ];
            }, $states);
            $this->saveToCache($data, $cacheKey);
        }

        return $data;
    }
}
