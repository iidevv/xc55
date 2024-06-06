<?php

namespace Iidev\StripeSubscriptions\Controller\Admin;

use XLite\Core\Database;

class MigrationStats extends \XLite\Controller\Admin\AAdmin
{
    public function getActiveSubscribersCount()
    {
        $subscriptions = Database::getRepo('Iidev\StripeSubscriptions\Model\MembershipMigrate')->findBy([
            'membershipid' => 9
        ]);
        return count($subscriptions);
    }
    public function getMigratedSubscribersCount()
    {
        $queryBuilder = Database::getRepo('Iidev\StripeSubscriptions\Model\MembershipMigrate')->createQueryBuilder('m');
        $queryBuilder->where('m.membershipid = :membershipid')
            ->andWhere('m.status = :status')
            ->setParameter('membershipid', 9)
            ->setParameter('status', 'MIGRATION_COMPLETE');
            
        $subscriptions = $queryBuilder->getQuery()->getResult();
        return count($subscriptions);
    }
}

