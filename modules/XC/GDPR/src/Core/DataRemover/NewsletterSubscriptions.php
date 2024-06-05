<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Core\DataRemover;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Profile;

/**
 * NewsletterSubscriptions
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\NewsletterSubscriptions")
 */
class NewsletterSubscriptions extends \XC\GDPR\Core\DataRemover
{
    public function removeByProfile(Profile $profile)
    {
        parent::removeByProfile($profile);

        $this->removeSubscriptions($profile);
    }

    protected function removeSubscriptions(Profile $profile)
    {
        $qb = \XLite\Core\Database::getRepo('XC\NewsletterSubscriptions\Model\Subscriber')
            ->createPureQueryBuilder();
        $alias = $qb->getMainAlias();

        $qb->delete()
            ->where($qb->expr()->orX(
                $qb->expr()->eq("{$alias}.email", ':email'),
                $qb->expr()->eq("{$alias}.profile", ':profile')
            ))
            ->setParameter('email', $profile->getLogin())
            ->setParameter('profile', $profile)
            ->getQuery()
            ->execute();
    }
}
