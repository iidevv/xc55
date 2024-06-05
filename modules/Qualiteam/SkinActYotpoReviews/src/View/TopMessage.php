<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\View;

use Qualiteam\SkinActYotpoReviews\Core\TopMessage as TopMessageCustomCore;
use XCart\Extender\Mapping\Extender;
use XLite\Core\TopMessage as TopMessageCore;

/**
 * @Extender\Mixin
 */
class TopMessage extends \XLite\View\TopMessage
{
    /**
     * Get message prefix
     *
     * @param array $data Message
     *
     * @return string|void
     */
    protected function getPrefix(array $data)
    {
        $result = parent::getPrefix($data);

        if (empty($result)
            && $data[TopMessageCore::FIELD_TYPE] === TopMessageCustomCore::YOTPO_ERROR
        ) {
            $prefix = 'SkinActYotpoReviews top message error prefix';
            return static::t($prefix) . ':';
        }

        return $result;
    }
}