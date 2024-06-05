<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActOrderMessaging\View\ItemsList\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Conversations
 * @Extender\Mixin
 */
abstract class Conversations extends \XC\VendorMessages\View\ItemsList\Admin\Conversations
{
    /**
     * Get order number
     *
     * @param \XC\VendorMessages\Model\Conversation $conversation
     *
     * @return string
     */
    protected function getOrderNumber($conversation)
    {
        $return = parent::getOrderNumber($conversation);

        if (!$return) {
            $return = static::t('SkinActOrderMessaging Conversation');
        }

        return $return;
    }

    /**
     * Get order url
     *
     * @param \XC\VendorMessages\Model\Conversation $conversation
     *
     * @return string
     */
    protected function getOrderLink($conversation)
    {
        return $conversation->getOrder()
            ? parent::getOrderLink($conversation)
            : $this->buildURL('conversation', '', ['id'=>$conversation->getId()]);
    }


    /**
     * Get row label
     *
     * @param \XC\VendorMessages\Model\Conversation $conversation
     *
     * @return string
     */
    protected function getLabel($conversation)
    {

        $return = parent::getLabel($conversation);

        $count = $conversation->countUnreadMessages();

        if ($count > 0 && !$conversation->getOrder() && $conversation->getAuthor()) {
            $return = $count > 1
                ? static::t('SkinActOrderMessaging X new direct message from the customer', ['count' => $count])
                : static::t('SkinActOrderMessaging New direct message from the customer');
        }

        return $return;
    }
}