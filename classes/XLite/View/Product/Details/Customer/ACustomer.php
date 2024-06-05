<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Details\Customer;

abstract class ACustomer extends \XLite\View\Product\Details\ADetails
{
    /**
     * @param \XLite\Model\Base\Image $image Image
     * @param integer                 $i     Image index OPTIONAL
     *
     * @return string
     */
    public function getAlt($image, $i = null)
    {
        return $image && $image->getAlt()
            ? $image->getAlt()
            : $this->getProduct()->getName();
    }

    /**
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getProduct();
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'js/attributetoform.js'
        ]);
    }
}
