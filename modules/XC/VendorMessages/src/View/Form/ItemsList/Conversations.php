<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\View\Form\ItemsList;

class Conversations extends \XLite\View\Form\ItemsList\AItemsList
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTarget()
    {
        return 'messages';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultClassName()
    {
        return trim(parent::getDefaultClassName() . ' conversations');
    }
}
