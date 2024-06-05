<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\View\Form;

use Qualiteam\SkinActMagicImages\Traits\MagicImagesTrait;
use XLite\Core\Request;

/**
 * Product tab form
 *
 */
class Product extends \XLite\View\Form\AForm
{
    use MagicImagesTrait;

    /**
     * Get default target
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return $this->getTargetController();
    }

    /**
     * Get default action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update_magic360';
    }

    /**
     * Get default params
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        return parent::getDefaultParams() + ['id' => Request::getInstance()->id, 'product_id' => Request::getInstance()->product_id];
    }

    protected function getClassName()
    {
        return parent::getClassName() . ' form-horizontal magic-images-form';
    }
}
