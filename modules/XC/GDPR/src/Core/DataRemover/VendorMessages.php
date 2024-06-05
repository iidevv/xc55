<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Core\DataRemover;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Profile;

/**
 * VendorMessages
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\VendorMessages")
 */
class VendorMessages extends \XC\GDPR\Core\DataRemover
{
    public function removeByProfile(Profile $profile)
    {
        parent::removeByProfile($profile);

        $this->removeConversations($profile);
    }

    protected function removeConversations(Profile $profile)
    {
        foreach ($profile->getConversations() as $conversation) {
            /* @var Conversation $conversation */
            if ($order = $conversation->getOrder()) {
                $order->setConversation(null);
            }

            foreach ($conversation->getMembers() as $member) {
                $member->getConversations()->removeElement($conversation);
            }

            \XLite\Core\Database::getEM()->remove($conversation);
        }
    }
}
