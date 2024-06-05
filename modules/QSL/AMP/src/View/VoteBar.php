<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class VoteBar extends \XLite\View\VoteBar
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return static::isAMP() ? 'modules/QSL/AMP/vote_bar/vote_bar.twig' : parent::getDefaultTemplate();
    }

    /**
     * Get percent
     *
     * @return integer
     */
    protected function getPercent()
    {
        // Percent plus correction (1 pixel per marked star)
        if (static::isAMP()) {
            $val = $this->getParam(self::PARAM_RATE);
            return (round($val * 2) / 2) * 100 / $this->getParam(self::PARAM_MAX) + $this->getParam(self::PARAM_RATE) * static::STARS_KOEFFICIENT . ' ' . $val;
        } else {
            return parent::getPercent();
        }
    }
}
