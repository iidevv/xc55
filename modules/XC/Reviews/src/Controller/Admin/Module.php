<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;
use XLite\Core\Database;

/**
 * @Extender\Mixin
 */
class Module extends \XLite\Controller\Admin\Module
{
    protected function doActionUpdate()
    {
        parent::doActionUpdate();

        if ($this->getModuleId() === 'XC-Reviews') {
            $qb = Database::getRepo('XLite\Model\Notification')->createPureQueryBuilder('n');
            $qb->update()
                ->set(
                    'n.enabledForCustomer',
                    $qb->expr()->literal((bool)Config::getInstance()->XC->Reviews->enableCustomersFollowup)
                )
                ->where($qb->expr()->eq('n.templatesDirectory', ':templatesDirectory'))
                ->setParameter('templatesDirectory', 'modules/XC/Reviews/review_key')
                ->getQuery()
                ->execute();
        }
    }
}
