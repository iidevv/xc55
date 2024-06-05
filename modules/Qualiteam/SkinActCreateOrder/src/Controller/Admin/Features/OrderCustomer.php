<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\Controller\Admin\Features;


use XLite\Core\Database;

/**
 *
 */
trait OrderCustomer
{
    protected $selectedProfile;


    protected function getProfileClone($selectedProfile, $order)
    {
        $newProfile = $selectedProfile->cloneEntity();
        $newProfile->setOrder($order);
        $newProfile->setAnonymous(false);

        if (!$newProfile->getAddresses()->count()) {
            $address = new \XLite\Model\Address;
            $address->setIsBilling(true);
            $address->setIsShipping(true);
            $address->setProfile($newProfile);
            $newProfile->addAddresses($address);
        }

        return $newProfile;
    }

    /**
     *
     * @return boolean
     */
    protected function updateOrderCustomer()
    {
        $order = $this->getOrder();

        $selectedProfile = $this->getSelectedProfile();

        return $this->updateOrderProfile($order, $selectedProfile);
    }

    protected function updateOrderProfile($order, $selectedProfile, $updateOrigProfile = true)
    {
        $result = false;

        if ($order
            && $order->getManuallyCreated()
            && $selectedProfile instanceof \XLite\Model\Profile
            && !$order->getOrigProfile()
        ) {

            $profile = $order->getProfile();
            $profile->setOrder(null);
            Database::getEM()->flush();

            $newProfile = $this->getProfileClone($selectedProfile, $order);

            $order->setProfile($newProfile);

            if ($updateOrigProfile) {
                $order->setOrigProfile($selectedProfile);
            }

            Database::getEM()->flush();
            $profile->delete();

            $result = true;
        }

        return $result;
    }

    /**
     *
     * @return \XLite\Model\Profile
     */
    protected function getSelectedProfile()
    {
        if (isset($this->selectedProfile)) {
            return $this->selectedProfile;
        }

        $login = \XLite\Core\Request::getInstance()->login;
        if (is_array($login)) {
            $login = reset($login);
        } else {
            unset($login);
        }

        $selectedProfile = null;

        $selectedProfileId = (int)\XLite\Core\Request::getInstance()->select;

        if ($selectedProfileId) {
            $selectedProfile = Database::getRepo('XLite\Model\Profile')->find($selectedProfileId);
        } else {

            $order = $this->getOrder();
            if ($order && $order->getOrigProfile()) {
                $selectedProfile = $order->getOrigProfile();
            }
            if ($selectedProfile && $login != $selectedProfile->getLogin()) {
                unset($selectedProfile);
            }

        }

        if ($login && !$selectedProfile) {

            $selectedProfile = Database::getRepo('XLite\Model\Profile')
                ->findOneBy(['login' => $login, 'order' => null, 'anonymous' => false]);

            if (!$selectedProfile) {
                $cnd = new \XLite\Core\CommonCell;
                $cnd->{\XLite\Model\Repo\Profile::SEARCH_USER_TYPE} = ['C', 'N'];
                $cnd->{\XLite\Model\Repo\Profile::SEARCH_ORDER_ID} = false;
                $cnd->{\XLite\Model\Repo\Profile::SEARCH_PATTERN} = $login;
                $selectedProfile = Database::getRepo('XLite\Model\Profile')->search($cnd);
                if (count($selectedProfile) == 1) {
                    $selectedProfile = reset($selectedProfile);
                }
            }
        }

        $this->selectedProfile = $selectedProfile;

        return $selectedProfile;
    }

}
