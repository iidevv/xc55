<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View\Form;

/**
 * Educational videos search form for customer zone
 */
class Search extends \XLite\View\Form\AForm
{
    protected function getClassName()
    {
        return parent::getClassName() . ' videos__search-form';
    }

    protected function getDefaultAction()
    {
        return 'search';
    }
}