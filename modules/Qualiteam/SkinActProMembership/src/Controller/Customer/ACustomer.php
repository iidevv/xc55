<?php


namespace Qualiteam\SkinActProMembership\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;
use XLite\Core\Database;

/**
 * @Extender\Mixin
 */
class ACustomer extends \XLite\Controller\Customer\ACustomer
{
    public function isProMembershipIconVisible()
    {
        static $result = null;

        if ($result === null) {

            $profile = Auth::getInstance()->getProfile();

            if (!$profile) {
                $result = true;
                return $result;
            }

            if ($profile->getAnonymous()) {
                $result = true;
                return $result;
            }

            $profileMembership = $profile->getMembership();

            if (!$profileMembership) {
                $result = true;
                return $result;
            }

            $qb = Database::getRepo('XLite\Model\Product')->createPureQueryBuilder('p');

            $productsCount =
                $qb->where('p.enabled = :enabledPaidMembershipProduct')
                ->andWhere('p.appointmentMembership = :currentProfileMembership')
                ->setParameter('enabledPaidMembershipProduct', true)
                ->setParameter('currentProfileMembership', $profileMembership)
                ->count();

            if ($productsCount > 0) {
                // pro membership exists
                $result = false;
                return $result;
            }

            $result = true;

            return $result;
        }

        return $result;
    }
}