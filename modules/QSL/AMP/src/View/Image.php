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
class Image extends \XLite\View\Image
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return static::isAMP() ? 'modules/QSL/AMP/common/image.twig' : parent::getDefaultTemplate();
    }

    /**
     * Add CSS styles to the value of "style" attribute of the image tag
     *
     * @param string $style CSS styles to be added to the end of "style" attribute
     *
     * @return void
     */
    protected function addInlineStyle($style)
    {
        if (!$this->isAMP()) {
            parent::addInlineStyle($style);
        }
    }

    /**
     * Get properties
     *
     * @return array
     */
    public function getProperties()
    {
        if ($this->isAMP()) {
            $this->properties['width'];
            $this->properties['height'];
        } else {
            parent::getProperties();
        }

        return $this->properties;
    }
}
