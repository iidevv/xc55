<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\Product\AttributeValue\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Attribute value (Select)
 * @Extender\Mixin
 */
class Select extends \XLite\View\Product\AttributeValue\Customer\Select
{
    /**
     * @return string
     */
    protected function getOptionTemplate()
    {
        return $this->useBlocksMode()
            ? $this->getDir() . '/block.twig'
            : $this->getDir() . '/option.twig';
    }

    /**
     * Return widget template
     *
     * @return string
     */
    protected function getTemplate()
    {
        if (\XLite\Core\Layout::getInstance()->getZone() === \XLite::ZONE_CUSTOMER) {
            return $this->useBlocksMode()
                ? $this->getDir() . '/blocks.twig'
                : $this->getDir() . '/selectbox.twig';
        }

        return parent::getTemplate();
    }

    protected function getUnavailableTooltipTemplate(): string
    {
        return '';
    }
}
