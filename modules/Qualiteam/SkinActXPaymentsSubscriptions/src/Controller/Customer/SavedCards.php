<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace Qualiteam\SkinActXPaymentsSubscriptions\Controller\Customer;

use Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * Saved credit cards
 *
 * @Extender\Mixin
 */
class SavedCards extends \Qualiteam\SkinActXPaymentsConnector\Controller\Customer\SavedCards
{
    /**
     * Template for the Remove button (or something instead of it)
     *
     * @param int $cardId Card ID
     *
     * @return string
     */
    public function getRemoveTemplate($cardId)
    {
        $template = parent::getRemoveTemplate($cardId);

        $subscription = Database::getRepo(Subscription::class)
            ->findOneActiveByCardId($cardId, true);
        if (
            $subscription
        ) {
            $template = 'modules/Qualiteam/SkinActXPaymentsSubscriptions/account/saved_cards.table.remove.twig';
        }

        return $template;
    }
}
