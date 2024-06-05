<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;
use QSL\AbandonedCartReminder\Model\Email;

/**
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{
    /**
     * Whether the order was placed after following the link in an abandonment email.
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $recovered = 0;

    /**
     * Number of sent reminders
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $cart_reminders_sent = 0;

    /**
     * Date the last reminder of an abandoned cart was sent.
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $cart_reminder_date = 0;

    /**
     * Reminder e-mail notifications.
     *
     * @var \Doctrine\Common\Collections\Collection|\\QSL\AbandonedCartReminder\Model\Email[]
     *
     * @ORM\OneToMany (targetEntity="QSL\AbandonedCartReminder\Model\Email", mappedBy="order")
     */
    protected $cartReminderEmails;

    /**
     * The reminder e-mail notification that was used to recover the cart the last time
     *
     * @var \Doctrine\Common\Collections\Collection|\\QSL\AbandonedCartReminder\Model\Email[]
     *
     * @ORM\OneToOne (targetEntity="QSL\AbandonedCartReminder\Model\Email")
     * @ORM\JoinColumn (name="recovery_email_id", referencedColumnName="email_id", nullable=true, onDelete="SET NULL")
     */
    protected $cartRecoveryEmail;

    /**
     * Whether it is a "lost" cart (i.e. the customer has a newer one)
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $lost = 0;

    /**
     * Last order soft renew date
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $lastVisitDate = 0;

    /*
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->cartReminderEmails = new \Doctrine\Common\Collections\ArrayCollection();
        parent::__construct($data);
    }

    /**
     * Get the number of seconds it takes to get a cart listed as abandoned.
     *
     * @return integer
     */
    public static function getAbandonmentTime()
    {
        // convert minutes to seconds
        return 60 * \XLite\Core\Config::getInstance()->QSL->AbandonedCartReminder->abandonment_time;
    }

    /**
     * Check whether the cart is abandoned, or not.
     *
     * @return boolean
     */
    public function isAbandoned()
    {
        return ! $this->abcrIsPlacedOrder()
            && $this->getLastVisitDate() < (\XLite\Core\Converter::time() - static::getAbandonmentTime());
    }

    protected function abcrIsPlacedOrder()
    {
        return $this->getOrderNumber();
    }

    /**
     * Check whether it is a lost cart (i.e. the shopper has a newed cart already).
     *
     * @return boolean
     */
    public function isLost()
    {
        return $this->lost == 0;
    }

    /**
     * Mark the cart as lost.
     *
     * @return void
     */
    public function markAsLost()
    {
        $this->lost = 1;
    }

    /**
     * If a recovered cart became abandoned again, drop the "recovered" flag.
     *
     * @return void
     */
    public function renewCartRecoveredFlag()
    {
        if ($this->isRecovered() && $this->isAbandoned()) {
            $this->dropCartRecoveredFlag();
        }
    }

    /**
     * Updates the last visit date and sets it to the current time.
     *
     * The method prevents the field from being update too often and makes sure
     * that the date is update less than once a threshold period.
     *
     * @return boolean Whether the date was updated, or not.
     */
    public function renewLastVisitDate()
    {
        $time = \XLite\Core\Converter::time();

        $renewed = ($time - $this->getLastVisitDate()) > $this->getLastVisitDateThreshold();

        if ($renewed) {
            $this->setLastVisitDate($time);
        }

        return $renewed;
    }

    /**
     * Returns the number of seconds since the last renewal of the last visit
     * date field within which the field is not updated.
     *
     * @return int
     */
    protected function getLastVisitDateThreshold()
    {
        return 60; // Do not update the last visit date more than once every minute.
    }

    /**
     * Check whether the order is recovered with a link from an abandonment email.
     *
     * @return boolean
     */
    public function isRecovered()
    {
        return ($this->getRecovered() > 0);
    }

    /**
     * Mark the order as not recovered.
     *
     * @return void
     */
    public function dropCartRecoveredFlag()
    {
        $this->setRecovered(0);
    }

    /**
     * Mark the order as recovered.
     *
     * @return void
     */
    public function setCartRecoveredFlag()
    {
        $this->setRecovered(1);
    }


    /**
     * Clone order and all related data
     * TODO: Decompose this method into several methods
     *
     * @return \XLite\Model\Order
     */
    public function cloneEntity()
    {
        // Consider cloned carts to be newer than original ones (and mark the original carts as lost)
        $clone = parent::cloneEntity();
        $this->setLost(1);

        return $clone;
    }

    /**
     * Updates the "recovered" flag.
     *
     * @param integer $recovered New flag value
     *
     * @return Order
     */
    public function setRecovered($recovered)
    {
        $this->recovered = $recovered;

        return $this;
    }

    /**
     * Returns the "recovered" flag.
     *
     * @return integer
     */
    public function getRecovered()
    {
        return $this->recovered;
    }

    /**
     * Updates the number of reminders sent for the cart.
     *
     * @param integer $cartRemindersSent New number
     *
     * @return Order
     */
    public function setCartRemindersSent($cartRemindersSent)
    {
        $this->cart_reminders_sent = $cartRemindersSent;

        return $this;
    }

    /**
     * Returns the number of reminders sent for the cart.
     *
     * @return integer
     */
    public function getCartRemindersSent()
    {
        return $this->cart_reminders_sent;
    }

    /**
     * Updates the date when the reminder was sent the last time.
     *
     * @param integer $cartReminderDate Timestamp
     *
     * @return Order
     */
    public function setCartReminderDate($cartReminderDate)
    {
        $this->cart_reminder_date = $cartReminderDate;

        return $this;
    }

    /**
     * Returns the date when the reminder was sent the last time.
     *
     * @return integer
     */
    public function getCartReminderDate()
    {
        return $this->cart_reminder_date;
    }

    /**
     * Updates the "lost" flag for the cart.
     *
     * @param integer $lost New flag value
     *
     * @return Order
     */
    public function setLost($lost)
    {
        $this->lost = $lost;

        return $this;
    }

    /**
     * Returns the "lost" flag for the cart.
     *
     * @return integer
     */
    public function getLost()
    {
        return $this->lost;
    }

    /**
     * Updates the date when the cart was viewed by the customer the last time.
     *
     * @param integer $lastVisitDate Timestamp
     *
     * @return Order
     */
    public function setLastVisitDate($lastVisitDate)
    {
        $this->lastVisitDate = $lastVisitDate;

        return $this;
    }

    /**
     * Returns the date when the cart was viewed by the customer the last time.
     *
     * @return integer
     */
    public function getLastVisitDate()
    {
        return $this->lastVisitDate;
    }

    /**
     * Add sent cart reminder e-mail.
     *
     * @param \QSL\AbandonedCartReminder\Model\Email $item
     *
     * @return \XLite\Model\Order
     */
    public function addCartReminderEmail(Email $item)
    {
        $this->cartReminderEmails[] = $item;

        return $this;
    }

    /**
     * Get sent cart reminder e-mails.
     *
     * @return \QSL\AbandonedCartReminder\Model\Email[]
     */
    public function getCartReminderEmails()
    {
        return $this->cartReminderEmails;
    }

    /**
     * Sets the email notification that recovered the cart the last time.
     *
     * @param \QSL\AbandonedCartReminder\Model\Email $item
     *
     * @return \XLite\Model\Order
     */
    public function setCartRecoveryEmail(Email $item)
    {
        $this->cartRecoveryEmail = $item;

        return $this;
    }

    /**
     * Get the email notification that recovered the cart.
     *
     * @return \QSL\AbandonedCartReminder\Model\Email
     */
    public function getCartRecoveryEmail()
    {
        return $this->cartRecoveryEmail;
    }

    /**
     * Returns the last cart reminder e-mail sent for this cart/order.
     *
     * @return \QSL\AbandonedCartReminder\Model\Email|null
     */
    public function getLastCartReminderEmail()
    {
        $found = null;

        foreach ($this->getCartReminderEmails() as $email) {
            if (!$found || ($email->getDateSent() > $found->getDateSent())) {
                $found = $email;
            }
        }

        return $found;
    }

    /**
     * Set profile
     *
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     *
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     *
     * @return void
     */
    public function setProfile(\XLite\Model\Profile $profile = null)
    {
        $changed = $profile
            && $profile->getProfileId()
            && !$this->getOrigProfile()
            && ($this->getProfile() !== $profile);

        parent::setProfile($profile);

        if ($changed) {
            $this->abcrChangeRemindersProfile($profile);
        }
    }

    /**
     * Set original profile
     *
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     *
     * @return void
     */
    public function setOrigProfile(\XLite\Model\Profile $profile = null)
    {
        $changed = $profile
            && $profile->getProfileId()
            && ($this->getOrigProfile() !== $profile);

        parent::setOrigProfile($profile);

        if ($changed) {
            $this->abcrChangeRemindersProfile($profile);
        }
    }

    /**
     * Updates the user associated with abandoned cart emails sent for the order.
     *
     * @param \XLite\Model\Profile $profile User profile
     *
     * @return void
     */
    protected function abcrChangeRemindersProfile(\XLite\Model\Profile $profile)
    {
        foreach ($this->getCartReminderEmails() as $email) {
            $email->setProfileHash(\QSL\AbandonedCartReminder\Model\Email::getHashForProfile($profile));
        }
    }

    /**
     * Set payment status
     *
     * @param mixed $paymentStatus Payment status
     *
     * @return void
     */
    public function setPaymentStatus($paymentStatus = null)
    {
        parent::setPaymentStatus($paymentStatus);

        $order = method_exists($this, 'isChild') && $this->isChild() // Multi-Vendor add-on
            ? $this->getParent()
            : $this;

        if ($order->getRecovered()) {
            $email = $order->getCartRecoveryEmail();
            if ($email) {
                $this->abcrChangeReminderPaidDate($email);
            }
        }
    }

    /**
     * Updates an abandoned cart e-mail and changes the pay date depending on
     * whether the new order status is paid, or not.
     *
     * @param \QSL\AbandonedCartReminder\Model\Email $email Email model
     *
     * @return void
     */
    protected function abcrChangeReminderPaidDate(\QSL\AbandonedCartReminder\Model\Email $email)
    {
        $codes = \XLite\Model\Order\Status\Payment::getPaidStatuses();
        $old = in_array($this->getOldPaymentStatusCode(), $codes);
        $new = in_array($this->getPaymentStatusCode(), $codes);

        if ($old != $new) {
            $email->setDatePaid($new ? \XLite\Core\Converter::time() : 0);
        }
    }


    /**
     * Called when an order successfully placed by a client
     *
     * @return void
     */
    public function processSucceed()
    {
        parent::processSucceed();

        if ($this->isRecovered()) {
            $this->markCartReminderAsRecovered();
        }
    }

    /**
     * Mark the fact that user has followed a cart recovery link for the cart.
     *
     * @param \QSL\AbandonedCartReminder\Model\Email $email Email model
     *
     * @return void
     */
    public function markCartAsRecovered(Email $email)
    {
        $this->setCartRecoveredFlag();

        $email = $email ?: $this->getLastCartReminderEmail();
        if ($email) {
            $this->setCartRecoveryEmail($email);
            $email->setDateClicked(\XLite\Core\Converter::time());
        }
    }

    /**
     * Mark the fact that user has placed an order after following a cart
     * recovery link.
     *
     * @param \QSL\AbandonedCartReminder\Model\Email $email Email model
     *
     * @return void
     */
    protected function markCartReminderAsRecovered()
    {
        $reminder = $this->getCartRecoveryEmail() ?: $this->getLastCartReminderEmail();
        if ($reminder) {
            $reminder->setDatePlaced(\XLite\Core\Converter::time());
        }
    }
}
