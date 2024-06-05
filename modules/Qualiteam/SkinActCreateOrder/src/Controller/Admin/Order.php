<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Core\OrderHistory;

/**
 * @Extender\Mixin
 */
class Order extends \XLite\Controller\Admin\Order
{
    use \Qualiteam\SkinActCreateOrder\Controller\Admin\Features\OrderCustomer;

    protected $removeTmpOrder = true;

    protected function removeTemporaryOrder(\XLite\Model\Order $order)
    {
        if ($this->removeTmpOrder) {
            parent::removeTemporaryOrder($order);
        }
    }

    protected function doActionRecalculate()
    {
        $order = $this->getOrder();

        if ($order
            && $order->getManuallyCreated()
            && !$order->getOrigProfile()
        ) {

            $this->removeTmpOrder = false;

            $tmpOrder = static::getTemporaryOrder($order->getOrderId(), true);

            parent::doActionRecalculate();

            $surchargeTotals = $tmpOrder->getSurchargeTotals();

            $html = [];

            foreach ($surchargeTotals as $type => $modifier) {

                if (in_array($type, ['DCOUPON', 'SHIPPING'], true)) {
                    continue;
                }

                $modifier['formField'] = new \XLite\View\FormField\Inline\Input\Text\Price\OrderModifierTotal([
                    'entity' => $modifier['object'],

                ]);

                $html[] = '<li class="order-modifier ctrl-manual order-modifier-manually-added">' . (new $modifier['widget']([
                        'order' => $tmpOrder,
                        'surcharge' => $modifier,
                        'sType' => $type
                    ]))->getContent() . '</li>';
            }

            //\XLite\Core\Event::newTaxModifiers(['html' => $html]);
            echo \json_encode(['html' => $html]);

            $this->removeTmpOrder = true;

            $this->removeTemporaryOrder($tmpOrder);


        } else {
            parent::doActionRecalculate();
        }

    }

    protected function sendOrderChangeNotification()
    {
        $order = $this->getOrder();

        if ($order
            && $order->getManuallyCreated()
            && !$order->getOrderCreatedNotificationSent()
            && $order->getOrigProfile()
        ) {
            $order->setOrderCreatedNotificationSent(true);
            Database::getEM()->flush();

            if (!$this->getIgnoreCustomerNotificationFlag()) {
                return $order->sendOrderCreatedIfNeeded();
            }

            return \XLite\Core\Mailer::sendOrderCreatedAdmin($order);
        }

        if ($order->getManuallyCreated() && !$order->getOrigProfile()) {
            return;
        }

        return parent::sendOrderChangeNotification();
    }

    protected function editProfileRoutine()
    {
        $order = $this->getOrder();

        [$login, $isLoginValid,
            $isLoginExists,
            $isProfileUseProvidedLogin,
            $isOrigProfileUseProvidedLogin] = $this->defineLoginUsageState();

        if (!$isLoginValid) {

            if (empty($login)) {
                // \XLite\Core\TopMessage::addWarning('Empty email address');
            } else {
                \XLite\Core\TopMessage::addWarning('Invalid email address: ' . $login);
            }

            return;
        }

        if ($isLoginExists) {
            $origProfile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findByLogin($login);

            $order->setOrigProfile($origProfile);

            $profile = $order->getProfile();

            $ba = $profile->getBillingAddress();

            if ($ba) {
                $originalBa = $origProfile->getBillingAddress();

                if ($originalBa) {
                    $origProfile->getAddresses()->removeElement($originalBa);
                    Database::getEM()->remove($originalBa);
                }

                $baForOriginal = $ba->cloneEntity();
                $baForOriginal->setProfile($origProfile);
                $origProfile->setBillingAddress($baForOriginal);
                Database::getEM()->persist($baForOriginal);
            }


            $sa = $profile->getShippingAddress();

            if ($sa) {
                $originalSa = $origProfile->getShippingAddress();

                if ($originalSa) {
                    $origProfile->getAddresses()->removeElement($originalSa);
                    Database::getEM()->remove($originalSa);
                }

                $saForOriginal = $sa->cloneEntity();
                $saForOriginal->setProfile($origProfile);
                $origProfile->setShippingAddress($saForOriginal);
                Database::getEM()->persist($saForOriginal);
            }

            OrderHistory::getInstance()
                ->registerEvent($order->getOrderId(), 'ocm',
                    static::t('SkinActCreateOrder The customer for the order has been changed'));

            \XLite\Core\TopMessage::addInfo('SkinActCreateOrder The customer for the order has been changed');

            Database::getEM()->flush();

            return;
        }

        if (!$isLoginExists) {
            // create new orig profile from current
            $profile = $order->getProfile();
            $newOrigProfile = $profile->cloneEntity();
            $order->setOrigProfile($newOrigProfile);

            Database::getEM()->persist($newOrigProfile);

            Database::getEM()->flush();

            OrderHistory::getInstance()
                ->registerEvent($order->getOrderId(), 'ocm',
                    static::t('SkinActCreateOrder New customer created from the order'));

            \XLite\Core\TopMessage::addInfo('SkinActCreateOrder New customer created from the order');

            return;
        }
    }

    /**
     * doActionUpdate
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        parent::doActionUpdate();

        $order = $this->getOrder();

        if (!$order->getOrigProfile() && $order->getManuallyCreated()) {

            $this->editProfileRoutine();

            if ($this->isOrderChanged()) {
                $this->sendOrderChangeNotification();
            } else if ($order->countItems() > 0) {
                // item added before customer setup
                $this->sendOrderChangeNotification();
            }


        }
    }

    protected function defineLoginUsageState()
    {
        $login = \XLite\Core\Request::getInstance()->login;

        if (is_array($login)) {
            $login = reset($login);
        } else {
            $login = null;
        }

        $isLoginValid = (bool)filter_var($login, FILTER_VALIDATE_EMAIL);

        $isLoginExists = $isLoginValid && !empty(\XLite\Core\Database::getRepo('XLite\Model\Profile')->findByLogin($login));

        $order = $this->getOrder();

        $isProfileUseProvidedLogin = $isLoginValid && $order->getProfile() && $order->getProfile()->getLogin() === $login;
        $isOrigProfileUseProvidedLogin = $isLoginValid && $order->getOrigProfile() && $order->getOrigProfile()->getLogin() === $login;

        return [$login, $isLoginValid, $isLoginExists, $isProfileUseProvidedLogin, $isOrigProfileUseProvidedLogin];
    }

}
