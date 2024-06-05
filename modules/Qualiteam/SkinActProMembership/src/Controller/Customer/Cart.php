<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;
use XLite\Core\Database;
use XLite\Core\Event;
use XLite\Core\TopMessage;

/**
 * @Extender\Mixin
 */
class Cart extends \XLite\Controller\Customer\Cart
{
    protected function doActionAddProMembershipProduct()
    {
        // ajax from checkout page

        $isAdd = (int)\XLite\Core\Request::getInstance()->addProduct;
        $pid = (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActProMembership->product_to_add;
        $product = Database::getRepo('XLite\Model\Product')->find($pid);

        if (!$product) {
            TopMessage::addError('SkinActProMembership paid membership product is not found');
            return;
        }

        $this->setSilenceClose(true);

        if ($isAdd > 0) {
            \XLite\Core\Request::getInstance()->product_id = $pid;

            foreach ($this->getCart()->getItems() as $item) {
                if ($item->getProduct() === $product) {
                    return;
                }
            }

            $this->addProProduct(true);

            $this->setReturnURL($this->buildURL('checkout'));
            $this->setHardRedirect();

        } else {
            // remove
            foreach ($this->getCart()->getItems() as $item) {
                if ($item->getProduct() === $product) {

                    $item->setAmount(1);

                    $this->getCart()->removeItem($item);

                    $this->processRemoveItemSuccess($item);

                    $this->updateCart(true);

                    $this->setReturnURL($this->buildURL('checkout'));
                    $this->setHardRedirect();

                    break;
                }

            }
        }

    }

    protected function addProProduct($silent = false)
    {
        $item = $this->getCurrentItem();

        if ($item && $this->addItem($item)) {
            $this->processAddItemSuccess($item);
        } else {
            $this->processAddItemError();
        }

        $this->updateCart($silent);
    }

    protected function doActionProMembershipProduct()
    {
        $this->addProProduct();

        $this->setReturnURL($this->buildURL('cart'));
        $this->setHardRedirect();
    }

    protected function addItem($item)
    {
        if (Auth::getInstance()->getMembershipId()
            && $item->getProduct()->getAppointmentMembership()
        ) {

            $qb = Database::getRepo('\XLite\Model\Membership')->createPureQueryBuilder();

            $hasPaidMembership =
                $qb->linkInner('\XLite\Model\Product', 'p',
                    'WITH', 'm.membership_id = p.appointmentMembership')
                    ->andWhere('m.membership_id = :membership_id')
                    ->setParameter('membership_id', Auth::getInstance()->getMembershipId())
                    ->count();

            if ($hasPaidMembership > 0) {

                $pid = (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActProMembership->product_to_add;
                $product = Database::getRepo('XLite\Model\Product')->find($pid);

                if ($product
                    && $product->getAppointmentMembership() !== Auth::getInstance()->getMembership()
                ) {

                    $message = static::t('SkinActProMembership membership will be changed', [
                        'currentName' => Auth::getInstance()->getMembership()->getName(),
                        'newName' => $product->getAppointmentMembership()->getName(),
                    ]);

                    TopMessage::addWarning($message);
                } else {
                    TopMessage::addWarning('SkinActProMembership customer already has paid membership');
                }

            }

        }

        return parent::addItem($item);
    }

}