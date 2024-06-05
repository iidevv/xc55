<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Core\Task;

use XLite\Model\Cart;
use XLite\Core\Database;
use XLite\Core\CommonCell;
use XLite\Model\Repo\Cart as CartRepo;
use XLite\Model\Repo\Order as OrderRepo;
use QSL\AbandonedCartReminder\Core\CartReminder;
use QSL\AbandonedCartReminder\Model\Repo\Reminder;

/**
 * Scheduled task that sends automatic cart reminders.
 */
class CartRecovery extends \XLite\Core\Task\Base\Periodic
{
    /**
     * The maximum number of reminders that can be sent in a single Cron-job
     * iteration.
     */
    public const MAX_REMINDERS_PER_STEP = 100;

    /**
     * The minimum delay between Cron-job iterations (in seconds).
     *
     * If cron is configured to trigger tasks every minute, but this number is set
     * to 180 (3 minutes), the module will skip two cron iterations and will execute
     * the reminder-sending script every 3 minutes, not every minute.
     */
    public const MIN_DELAY_BETWEEN_STEPS = 60;

    /**
     * Reminders allowed for automatic sending.
     *
     * @var array
     */
    protected $cronReminders;

    /**
     * Return title for the task.
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Cart reminder';
    }

    /**
     * Return the delay (in seconds) between performing task steps.
     *
     * @return integer
     */
    protected function getPeriod()
    {
        return self::MIN_DELAY_BETWEEN_STEPS;
    }

    /**
     * Run a task step.
     *
     * @return void
     */
    protected function runStep()
    {
        $queue = $this->getAbandonedCarts();

        foreach ($queue as $cart) {
            $this->processCart($cart);
        }
    }

    /**
     * Process an abandoned cart and perform necessary actions (if any).
     *
     * @param \XLite\Model\Cart $cart The cart for which a reminder should be sent
     *
     * @return void
     */
    protected function processCart(Cart $cart)
    {
        if (!$this->isCartLost($cart)) {
            $this->sendReminders($cart);
        } else {
            $cart->markAsLost();
        }
    }

    /**
     * Send the reminder for a cart.
     *
     * @param \XLite\Model\Cart $cart The cart for which a reminder should be sent
     *
     * @return void
     */
    protected function sendReminders(Cart $cart)
    {
        $reminders = $this->findApplicableReminders($cart);

        foreach ($reminders as $reminder) {
            $tool = new CartReminder($cart, $reminder);
            $tool->send();
        }
    }

    /**
     * Find reminders which are to be sent for the cart.
     *
     * @param \XLite\Model\Cart $cart Cart
     *
     * @return array
     */
    protected function findApplicableReminders(Cart $cart)
    {
        // Look through all reminder groups (sorted by sending delay in descending order)
        // to find the one that is ready to be sent and has the greatest sending delay.
        $reminders = $this->getCronReminders();
        if (!$reminders) {
            return [];
        }
        do {
            $applicableReminders = current($reminders);
            next($reminders);
        } while ($this->isSent($cart, $applicableReminders));

        return $applicableReminders ?: [];
    }

    /**
     * @param Cart  $cart
     * @param array $reminders
     *
     * @return bool
     */
    protected function isSent(Cart $cart, $reminders = [])
    {
        $reminder = is_array($reminders) ? array_shift($reminders) : false;
        $sentReminderIds = $this->getSentReminderIds($cart);

        return $reminder ? in_array($reminder->getId(), $sentReminderIds) : false;
    }

    /**
     * @param Cart $cart
     *
     * @return array
     */
    protected function getSentReminderIds(Cart $cart)
    {
        $repo = Database::getEM()->getRepository('QSL\AbandonedCartReminder\Model\Email');

        return $repo->findSentReminderIdsByOrder($cart);
    }

    /**
     * Return reminders allowed for automatic sending.
     *
     * @return array
     */
    protected function getCronReminders()
    {
        if (!isset($this->cronReminders)) {
            $this->cronReminders = [];

            $reminders = Database::getRepo('QSL\AbandonedCartReminder\Model\Reminder')
                ->search($this->getReminderSearchConditions());

            foreach ($reminders as $reminder) {
                $this->cronReminders[$reminder->getCronDelay()][] = $reminder;
            }

            krsort($this->cronReminders);
        }

        return $this->cronReminders;
    }

    /**
     * Prepares the search condition for retrieving reminder templates.
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getReminderSearchConditions()
    {
        return new CommonCell([Reminder::SEARCH_ENABLED => 1]);
    }

    /**
     * Retrieve abandoned carts which may require an reminder to be sent.
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    protected function getAbandonedCarts()
    {
        return Database::getRepo('XLite\Model\Cart')
            ->search($this->getCartSearchConditions());
    }

    /**
     * Return conditions to query abandoned carts.
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getCartSearchConditions()
    {
        $cnd = Database::getRepo('XLite\Model\Cart')
            ->addConditionSearchAbandoned(new CommonCell());

        // Skip carts with reminders sent after the last reminder date
        $cnd->{CartRepo::SEARCH_SKIP_REMINDED} = [
            null,
            $this->getLastReminderDelay() * 3600, // Convert delay to seconds
        ];

        // Move recently reminded carts to the bottom of the queue
        $cnd->{CartRepo::P_ORDER_BY} = [CartRepo::SORT_BY_REMINDER_DATE, 'ASC'];

        // Limit the number of carts to the maximum number allowed per step
        $cnd->{CartRepo::P_LIMIT} = [0, $this->getMaxRemindersPerStep()];

        return $cnd;
    }

    /**
     * The max number of reminders to send in one step.
     *
     * @return integer
     */
    protected function getMaxRemindersPerStep()
    {
        return self::MAX_REMINDERS_PER_STEP;
    }

    /**
     * Return the date the last reminder
     *
     * @return integer
     */
    protected function getLastReminderDelay()
    {
        $delays = array_keys($this->getCronReminders());
        $maxDelay = !empty($delays) ? $delays[0] : 0;

        return $maxDelay;
    }

    /**
     * Count number of order placed after the cart was visited the last time.
     *
     * @param \XLite\Model\Cart $cart Cart
     *
     * @return integer
     */
    protected function countNewerOrders(Cart $cart)
    {
        $profile = $cart->getProfile();

        return !$profile
            ? 0
            : Database::getRepo('XLite\Model\Order')->search(
                $this->getNewerOrdersCondition($profile, $cart->getDate()),
                true
            );
    }

    /**
     * Returns the condition used to search newer orders the customer has placed.
     *
     * @param \XLite\Model\Profile $profile Customer's profile.
     * @param integer              $date    Timestamp of the order's date
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getNewerOrdersCondition($profile, $date)
    {
        $cnd = new CommonCell();
        $cnd->{OrderRepo::P_DATE} = [$date + 1, null];

        if ($this->isSearchNewerOrdersByEmail()) {
            $cnd->{OrderRepo::P_EMAIL} = $profile->getLogin();
        } else {
            $cnd->{OrderRepo::P_PROFILE} = $profile;
        }

        return $cnd;
    }

    /**
     * Check if newer orders should be searched by the customer's email instead of the profile id.
     *
     * @return boolean
     */
    protected function isSearchNewerOrdersByEmail()
    {
        return true;
    }

    /**
     * Count number of other carts which were viewed after the cart was visited the last time.
     *
     * @param \XLite\Model\Cart $cart Cart
     *
     * @return integer
     */
    protected function countNewerCarts(Cart $cart)
    {
        $count = 0;

        $profile = $cart->getProfile();
        if ($profile) {
            $cnd = new CommonCell();
            $cnd->{CartRepo::SEARCH_PROFILE} = $profile;
            $cnd->{CartRepo::SEARCH_LAST_VISIT_DATE} = [$cart->getLastVisitDate() + 1, null];
            $count = Database::getRepo('XLite\Model\Cart')->search($cnd, true);
        }

        return $count;
    }

    /**
     * Check whether it is a 'lost' cart, or not.
     *
     * @param \XLite\Model\Cart $cart Cart
     *
     * @return boolean
     */
    protected function isCartLost(Cart $cart)
    {
        return $cart->getProfile() && ($this->countNewerCarts($cart)
            || $this->countNewerOrders($cart));
    }
}
