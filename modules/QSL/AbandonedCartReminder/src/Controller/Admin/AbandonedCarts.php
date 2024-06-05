<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Controller\Admin;

use XCart\Domain\ModuleManagerDomain;
use XLite\Core\Auth;
use XLite\Core\Request;
use XLite\Core\Database;
use XLite\Core\TopMessage;
use XLite\Model\Cart;
use QSL\AbandonedCartReminder\Model\Reminder;
use QSL\AbandonedCartReminder\Core\CartReminder;

class AbandonedCarts extends \XLite\Controller\Admin\AAdmin
{
    private ModuleManagerDomain $moduleManagerDomain;

    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->moduleManagerDomain = \XCart\Container::getContainer()->get(ModuleManagerDomain::class);
    }

    /**
     * @return bool
     */
    public function checkACL()
    {
        return parent::checkACL()
            || Auth::getInstance()->isPermissionAllowed('manage orders');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return static::t('Abandoned carts');
    }

    /**
     * @param string $paramName Parameter name
     *
     * @return mixed
     */
    public function getCondition($paramName)
    {
        $searchParams = $this->getConditions();

        return $searchParams[$paramName] ?? null;
    }

    /**
     * @return array
     */
    protected function getSelectedIds()
    {
        $selected = Request::getInstance()->select;

        return ($selected && is_array($selected))
            ? array_keys($selected)
            : [];
    }

    /**
     * Returns selected cart models.
     *
     * @return array
     */
    protected function getSelectedEntities()
    {
        $selectedIds = $this->getSelectedIds();
        $repo        = Database::getRepo('XLite\Model\Cart');

        return !empty($selectedIds)
            ? $repo->findByIds($selectedIds)
            : [];
    }

    protected function doActionRemind()
    {
        $id       = Request::getInstance()->reminder;
        $reminder = Database::getRepo('QSL\AbandonedCartReminder\Model\Reminder')
            ->find($id);

        $carts = [];

        // Get IDs of profiles associated with selected carts
        if ($reminder) {
            foreach ($this->getSelectedEntities() as $cart) {
                $profile = $cart->getProfile();
                if ($profile) {
                    // Notify each customer only once, even if he has multiple carts (due to a bug)
                    if (!isset($carts[$profile->profile_id])) {
                        $carts[$profile->profile_id] = $cart;
                    }
                }
            }
        }

        $count  = 0;
        $failed = 0;

        // Send reminder to each selected customer
        foreach ($carts as $cart) {
            $sent   = $this->sendReminder($cart, $reminder);
            $count  += ($sent ? 1 : 0);
            $failed += ($sent ? 0 : 1);
        }

        Database::getEM()->flush();

        if ($count) {
            TopMessage::addInfo(
                static::t('Abandonment e-mails have been sent to X customer(s).', ['count' => $count])
            );
        }

        if ($failed) {
            TopMessage::addWarning(
                static::t('X customer(s) having abandoned cart were not notified.', ['count' => $failed])
            );
        }

        if (!$count && !$failed) {
            TopMessage::addError(
                static::t('No abandoned e-mails were sent.')
            );
        }

        $this->setReturnURL(\XLite\Core\Request::getInstance()->returnURL ?: $this->buildURL('abandoned_carts'));
    }

    protected function doActionClear()
    {
        $count = 0;
        foreach ($this->getSelectedEntities() as $cart) {
            if ($cart instanceof Cart) {
                $this->clearCart($cart);
                Database::getEM()->remove($cart);
                $count++;
            }
        }

        if ($count) {
            TopMessage::addInfo(
                static::t('Selected carts (X) have been emptied and removed from the list.', ['count' => $count])
            );

            $this->setReturnURL(\XLite\Core\Request::getInstance()->returnURL ?: $this->buildURL('abandoned_carts'));
        } else {
            TopMessage::addError(
                static::t('No abandoned carts were selected.')
            );
        }
    }

    /**
     * Clear the cart.
     *
     * This method unlinks all coupons from the cart, zeroes the number of
     * sent reminders and the last reminder date, then clears the cart.
     *
     * @param \XLite\Model\Cart $cart Cart model
     */
    protected function clearCart(Cart $cart)
    {
        if ($this->moduleManagerDomain->isEnabled('CDev-Coupons')) {
            Database::getRepo('CDev\Coupons\Model\Coupon')->unlinkAllFromCart($cart);
        }

        $cart->setCartRemindersSent(0);
        $cart->setCartReminderDate(0);

        $cart->clear();

        Database::getEM()->flush();
    }

    /**
     * Send the reminder to a customer.
     *
     * @param \XLite\Model\Cart                                      $cart     Cart object
     * @param \QSL\AbandonedCartReminder\Model\Reminder $reminder Reminder
     *
     * @return int
     */
    protected function sendReminder(Cart $cart, Reminder $reminder)
    {
        $tool = new CartReminder($cart, $reminder);

        return $tool->send();
    }

    /**
     * Save search conditions
     */
    protected function fillSearchValuesStorage()
    {
        // TODO: switch to the new search case processor and use the default fillSearchValuesStorage() method.

        $storage = $this->getSearchValuesStorage();

        // Fill search conditions from requst
        $className                    = $this->getItemsListClass();
        $searchConditionsRequestNames = $className::getSearchParams();

        foreach ($searchConditionsRequestNames as $name => $condition) {
            $requestName = is_string($condition)
                ? $condition
                : $name;
            // the only change is that we set the $requestName instead of the $name variable
            $storage->setValue($requestName, Request::getInstance()->$requestName);
        }

        $storage->update();
    }
}
