<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Taxes;

/**
 * Taxes widget (admin)
 */
class TaxClasses extends Settings
{
    /**
     * @return string
     */
    protected function getItemsTemplate()
    {
        return 'tax_classes/classes.twig';
    }

    /**
     * @return string
     */
    protected function getFormTarget()
    {
        return 'tax_classes';
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'tax_settings/style.less';

        return $list;
    }
}
