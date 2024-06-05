<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ProductAdvisor\View\Label;

class ComingSoonLabel extends \XLite\View\AView
{
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/ProductAdvisor/parts/coming_soon_label.twig';
    }

    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            'modules/CDev/ProductAdvisor/parts/coming_soon_label.less'
        ]);
    }
}
