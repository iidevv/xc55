<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\View\Form\ItemsList;

class Conversation extends \XLite\View\Form\ItemsList\AItemsList
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTarget()
    {
        return 'conversation';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultParams()
    {
        $list = parent::getDefaultParams();
        $list['id'] = \XLite::getController()->getConversationId();

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultClassName()
    {
        return trim(parent::getDefaultClassName() . ' conversation-messages');
    }
}
