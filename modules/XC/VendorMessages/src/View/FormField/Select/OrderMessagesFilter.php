<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\View\FormField\Select;

/**
 * Order messages filter selector
 */
class OrderMessagesFilter extends \XLite\View\FormField\Select\Regular
{
    /**
     * @inheritdoc
     */
    protected function getDefaultOptions()
    {
        $list = [
            ''  => static::t('All communication threads'),
            'U' => static::t('Communication threads with unread messages'),
        ];

        if (\XC\VendorMessages\Main::isAllowDisputes()) {
            $list['D'] = static::t('Communication threads with open disputes');
        }

        return $list;
    }
}
