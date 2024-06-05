<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * E-mail notification for an abandoned cart.
 *
 * @ORM\Entity (repositoryClass="\QSL\AbandonedCartReminder\Model\Repo\Email")
 * @ORM\Table  (name="cart_reminder_emails",
 *      indexes={
 *          @ORM\Index (name="dateSent", columns={"dateSent"}),
 *          @ORM\Index (name="dateClicked", columns={"dateClicked"}),
 *          @ORM\Index (name="datePlaced", columns={"datePlaced"}),
 *          @ORM\Index (name="datePaid", columns={"datePaid"}),
 *          @ORM\Index (name="orderHash", columns={"orderHash"}),
 *          @ORM\Index (name="profileHash", columns={"profileHash"})
 *      }
 * )
 */
class Email extends \XLite\Model\AEntity
{
    /**
     * Primary key
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $email_id;

    /**
     * Abandoned cart / recovered order
     *
     * Due to the default "cart ttl" set to 7 days, we have to make the relation
     * optional and provide a function to clear past statistics (otherwise XC5
     * will auto-delete e-mails sent more than 7 days ago).
     *
     * @var \XLite\Model\Order
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Order", inversedBy="cartReminderEmails")
     * @ORM\JoinColumn (name="order_id", referencedColumnName="order_id", nullable=true, onDelete="SET NULL")
     */
    protected $order;

    /**
     * Unique identifier of the associated order.
     *
     * Orders can be deleted (and will be deleted during the garbage clean-up
     * routine!). So, in order to keep the statistics consistent, we have to
     * store some kind of a cached identifier that doesn't depend on
     * the associated order model.
     *
     * @var string
     * @ORM\Column (type="string", length=255, nullable=false, options={ "default": "" })
     */
    protected $orderHash = '';

    /**
     * Unique identifier of the associated profile.
     *
     * Profiles can be deleted. So, in order to keep the statistics consistent,
     * we have to store some kind of a cached identifier that doesn't depend on
     * the associated profile model.
     *
     * @var string
     * @ORM\Column (type="string", length=255, nullable=false, options={ "default": "" })
     */
    protected $profileHash = '';

    /**
     * Date when the e-mail was sent (timestamp).
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=false, options={ "default": 0 })
     */
    protected $dateSent = 0;

    /**
     * Date when the e-mail was clicked by the customer the last time (timestamp).
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=false, options={ "default": 0 })
     */
    protected $dateClicked = 0;

    /**
     * Date when the customer placed the order (timestamp).
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=false, options={ "default": 0 })
     */
    protected $datePlaced = 0;

    /**
     * Date when the customer paid the order (timestamp).
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=false, options={ "default": 0 })
     */
    protected $datePaid = 0;

    /**
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=false)
     */
    protected $reminderId;

    /**
     * Returns a unique string that identifies an order.
     *
     * @param \XLite\Model\Order $order Order or cart model
     *
     * @return string
     */
    public static function getHashForOrder(\XLite\Model\Order $order)
    {
        $email = $order->getLastCartReminderEmail();

        return $email ? $email->getOrderHash() : ($order->getDate() . '-' . $order->getOrderId());
    }

    /**
     * Returns a unique string that identifies a user.
     *
     * @param \XLite\Model\Profile $profile User profile
     *
     * @return string
     */
    public static function getHashForProfile(\XLite\Model\Profile $profile)
    {
        return $profile->getAdded() . '-' . $profile->getProfileId();
    }

    /**
     * Updates the date when the e-mail was sent to the user.
     *
     * @param int $timestamp Date (UNIX timestamp)
     *
     * @return void
     */
    public function setDateSent($timestamp)
    {
        $this->dateSent = $timestamp;
    }

    /**
     * Returns the date when the e-mail was sent to the user.
     *
     * @return int
     */
    public function getDateSent()
    {
        return $this->dateSent;
    }

    /**
     * Updates the date when user followed the cart recovery link in the e-mail
     * the last time.
     *
     * @param int $timestamp Date (UNIX timestamp)
     *
     * @return void
     */
    public function setDateClicked($timestamp)
    {
        $this->dateClicked = $timestamp;
    }

    /**
     * Returns the date when user followed the cart recovery link in the e-mail
     * the last time.
     *
     * @return int
     */
    public function getDateClicked()
    {
        return $this->dateClicked;
    }

    /**
     * Updates the date when user placed an order after following the cart
     * recovery link from the e-mail.
     *
     * @param int $timestamp Date (UNIX timestamp)
     *
     * @return void
     */
    public function setDatePlaced($timestamp)
    {
        $this->datePlaced = $timestamp;
    }

    /**
     * Returns the date when user placed an order after following the cart
     * recovery link from the e-mail.
     *
     * @return int
     */
    public function getDatePlaced()
    {
        return $this->datePlaced;
    }

    /**
     * Updates the date when user paid an an order, placed after following
     * the cart recovery link from the e-mail.
     *
     * @param int $timestamp Date (UNIX timestamp)
     *
     * @return void
     */
    public function setDatePaid($timestamp)
    {
        $this->datePaid = $timestamp;
    }

    /**
     * Returns the date when user paid an an order, placed after following
     * the cart recovery link from the e-mail.
     *
     * @return int
     */
    public function getDatePaid()
    {
        return $this->datePaid;
    }

    /**
     * Associated the e-mail model with the abandoned cart that the e-mail was
     * sent for.
     *
     * @param \XLite\Model\Order $order
     */
    public function setOrder(\XLite\Model\Order $order)
    {
        $this->order = $order;

        $this->setOrderHash(self::getHashForOrder($order));

        $this->setProfileHash(self::getHashForProfile(
            $order->getOrigProfile() ?: $order->getProfile()
        ));
    }

    /**
     * Returns the abandoned cart / order that the e-mail was sent for.
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Updates the unique string identifying the order that the e-mail was sent
     * for.
     *
     * @param string $hash Unique identifier
     *
     * @return void
     */
    public function setOrderHash($hash)
    {
        $this->orderHash = $hash;
    }

    /**
     * Returns the unique string identifying the order that the e-mail was sent
     * for.
     *
     * @return string
     */
    public function getOrderHash()
    {
        return $this->orderHash;
    }

    /**
     * Updates the unique string identifying the user that the e-mail was sent
     * for.
     *
     * @param string $hash Unique identifier
     *
     * @return void
     */
    public function setProfileHash($hash)
    {
        $this->profileHash = $hash;
    }

    /**
     * Returns the unique string identifying the user that the e-mail was sent
     * for.
     *
     * @return string
     */
    public function getProfileHash()
    {
        return $this->profileHash;
    }

    /**
     * @param integer $id
     *
     * @return $this
     */
    public function setReminderId($id)
    {
        $this->reminderId = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getReminderId()
    {
        return $this->reminderId;
    }
}
