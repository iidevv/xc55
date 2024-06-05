<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MembershipProducts\Core\Mail;

use XLite\Core\Mailer;
use XLite\Model\OrderItem;

/**
 * NotificationAssigned
 */
class NotificationAssigned extends AMail
{
    public const MESSAGE_DIR = 'modules/QSL/MembershipProducts/notification_assigned';

    /**
     * Constructor
     *
     * @param OrderItem $item
     */
    public function __construct(OrderItem $item = null)
    {
        parent::__construct();

        $this->setFrom(Mailer::getSiteAdministratorMail());

        if ($item) {
            $profile = $item->getOrder()->getProfile();

            if ($profile) {
                $this->setTo([
                    'name' => $profile->getName(),
                    'email' => $profile->getEmail()
                ]);

                $this->setReplyTo([
                    'address' => Mailer::getSiteAdministratorMails() ?? '',
                ]);
            }

            $this->populateVariables([
                'membership_name' => $item->getProduct()->getAppointmentMembership()->getName() ?? '',
            ]);
        }

        $this->appendData([
            'item' => $item,
        ]);
    }
}
