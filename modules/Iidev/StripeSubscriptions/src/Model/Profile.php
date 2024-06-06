<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Iidev\StripeSubscriptions\Model;

use XLite\Core\Database;
use XCart\Extender\Mapping\Extender;

/**
 * XPayments payment processor
 *
 * @Extender\Mixin
 */
class Profile extends \XLite\Model\Profile
{
    public function isMembershipMigrationProfile()
    {

        $isMembershipMigrationProfile = false;

        $login = $this->getLogin();

        /** @var \Iidev\StripeSubscriptions\Model\MembershipMigrate $preProfile */
        $preProfile = Database::getRepo('Iidev\StripeSubscriptions\Model\MembershipMigrate')->findOneBy([
            'login' => $login
        ]);

        if ($preProfile && $preProfile->getMembershipId() === 9 && $preProfile->getStatus() === '') {
            $isMembershipMigrationProfile = true;
        }

        return $isMembershipMigrationProfile;
    }
    public function setMembershipMigrationProfileComplete()
    {
        if (!$this->isMembershipMigrationProfile())
            return null;

        $login = $this->getLogin();

        /** @var \Iidev\StripeSubscriptions\Model\MembershipMigrate $preProfile */
        $preProfile = Database::getRepo('Iidev\StripeSubscriptions\Model\MembershipMigrate')->findOneBy([
            'login' => $login
        ]);

        $preProfile->setStatus("MIGRATION_COMPLETE");

        return true;
    }

    public function getMembershipMigrationProfileExpirationDate()
    {
        if (!$this->isMembershipMigrationProfile())
            return null;

        $login = $this->getLogin();

        /** @var \Iidev\StripeSubscriptions\Model\MembershipMigrate $preProfile */
        $preProfile = Database::getRepo('Iidev\StripeSubscriptions\Model\MembershipMigrate')->findOneBy([
            'login' => $login
        ]);

        return $preProfile->getPaidMembershipExpire();
    }
}
