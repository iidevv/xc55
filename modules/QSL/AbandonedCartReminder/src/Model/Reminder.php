<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Model;

use Doctrine\ORM\Mapping as ORM;
use XLite\View\FormField\Input\PriceOrPercent;

/**
 * Class represents Cart Reminder model.
 *
 * @ORM\Entity (repositoryClass="\QSL\AbandonedCartReminder\Model\Repo\Reminder")
 * @ORM\Table  (name="cart_reminders",
 *      indexes={
 *          @ORM\Index (name="cronDelay", columns={"cronDelay"}),
 *          @ORM\Index (name="enabled", columns={"enabled"})
 *      }
 * )
 */
class Reminder extends \XLite\Model\Base\I18n
{
    /**
     * Coupon types
     */
    public const COUPON_TYPE_ABSOLUTE = 'a';
    public const COUPON_TYPE_PERCENT  = 'p';

    /**
     * Reminder unique ID.
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $reminder_id;

    /**
     * Reminder name.
     *
     * @var string
     * @ORM\Column (type="string", length=255)
     */
    protected $name = '';

    /**
     * Delay between the cart becoming abandoned and sending the reminder automatically (in hours).
     *
     * @var integer
     * @ORM\Column (type="integer")
     */
    protected $cronDelay = 0;

    /**
     * Whether reminder is enabled.
     *
     * @var boolean
     * @ORM\Column (type="boolean")
     */
    protected $enabled = false;

    /**
     * In how many days the coupon will expire.
     *
     * @var integer
     * @ORM\Column (type="integer")
     */
    protected $couponExpire = 5;

    /**
     * New coupon's "Coupon cannot be combined with other coupons" flag
     *
     * @var boolean
     * @ORM\Column (type="boolean")
     */
    protected $couponSingleUse = false;

    /**
     * Reminder position
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * Coupon type
     *
     * @var string
     *
     * @ORM\Column (type="string", length=1)
     */
    protected $couponType = self::COUPON_TYPE_ABSOLUTE;

    /**
     * Coupon amount
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $couponAmount = 0.0000;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\AbandonedCartReminder\Model\ReminderTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Get object unique id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->getReminderId();
    }

    /**
     * Return subject for the reminder e-mail.
     *
     * @param boolean $hasCoupon Whether there is a coupon generated for the cart OPTIONAL
     *
     * @return string
     */
    public function getReminderSubject($hasCoupon = false)
    {
        $subject = $hasCoupon ? $this->getCouponSubject() : $this->getSubject();

        if ($hasCoupon && !$subject) {
            $subject = $this->getSubject();
        }

        return $subject;
    }

    /**
     * Return body for the reminder e-mail.
     *
     * @param boolean $hasCoupon Whether there is a coupon generated for the cart OPTIONAL
     *
     * @return string
     */
    public function getReminderBody($hasCoupon = false)
    {
        $body = $hasCoupon ? $this->getCouponBody() : $this->getBody();

        if ($hasCoupon && !$body) {
            $body = $this->getBody();
        }

        return $body;
    }

    /**
     * Return the amount of the coupon that should be created.
     *
     * @return float
     */
    public function getNewCouponAmount()
    {
        return $this->getCouponAmount();
    }

    /**
     * Return the type of the coupon that should be created.
     *
     * @return integer
     */
    public function getNewCouponType()
    {
        return $this->getCouponType();
    }

    /**
     * Return in what number of days the new coupon should expire.
     *
     * @return integer
     */
    public function getNewCouponPeriod()
    {
        return $this->getCouponExpire();
    }

    /**
     * Retun "Coupon cannot be combined with other coupons" flag
     * for generated coupon
     *
     * @return bool
     */
    public function getNewCouponSingleUse()
    {
        return $this->getCouponSingleUse();
    }

    /**
     * Check whether the reminder requires a new coupon to be created.
     *
     * @return boolean
     */
    public function requiresNewCoupon()
    {
        $amount = $this->getNewCouponAmount();

        return is_numeric($amount) && (0 < $amount);
    }

    /**
     * Returns the reminder identifier.
     *
     * @return integer
     */
    public function getReminderId()
    {
        return $this->reminder_id;
    }

    /**
     * Updates the administrative name for the reminder.
     *
     * @param string $name New administrative name
     *
     * @return Reminder
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the administrative name for the reminder.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the number of hours that should pass after a cart becomes abandoned to trigger the reminder.
     *
     * @param integer $cronDelay Delay (in hours)
     *
     * @return Reminder
     */
    public function setCronDelay($cronDelay)
    {
        $this->cronDelay = $cronDelay;

        return $this;
    }

    /**
     * Returns the number of hours that should pass after a cart becomes abandoned to trigger the reminder.
     *
     * @return integer
     */
    public function getCronDelay()
    {
        return $this->cronDelay;
    }

    /**
     * Configures whether the reminder is enabled, or not.
     *
     * @param boolean $enabled New status
     *
     * @return Reminder
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Checks if the reminder is enabled.
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setCoupon($value)
    {
        $this->setCouponType($value[PriceOrPercent::TYPE_VALUE] ?: static::COUPON_TYPE_ABSOLUTE);
        $this->setCouponAmount($value[PriceOrPercent::PRICE_VALUE] ?: 0);

        return $this;
    }

    /**
     * Checks if a coupon should be created when the reminder is sent.
     *
     * @return array
     */
    public function getCoupon()
    {
        return [
            PriceOrPercent::TYPE_VALUE  => $this->getCouponType(),
            PriceOrPercent::PRICE_VALUE => $this->getCouponAmount()
        ];
    }

    /**
     * @param $couponType
     *
     * @return $this
     */
    public function setCouponType($couponType)
    {
        $this->couponType = $couponType;

        return $this;
    }

    /**
     * @return string
     */
    public function getCouponType()
    {
        return $this->couponType;
    }

    /**
     * @param $couponAmount
     *
     * @return $this
     */
    public function setCouponAmount($couponAmount)
    {
        $this->couponAmount = $couponAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getCouponAmount()
    {
        return $this->couponAmount;
    }

    /**
     * Sets the number of days that coupons created for the reminder will be valid.
     *
     * @param integer $couponExpire Number of days
     *
     * @return Reminder
     */
    public function setCouponExpire($couponExpire)
    {
        $this->couponExpire = $couponExpire;

        return $this;
    }

    /**
     * Returns the number of days that coupons created for the reminder will be valid.
     *
     * @return integer
     */
    public function getCouponExpire()
    {
        return $this->couponExpire;
    }

    /**
     * @param bool $couponSingleUse
     *
     * @return Reminder
     */
    public function setCouponSingleUse($couponSingleUse)
    {
        $this->couponSingleUse = $couponSingleUse;

        return $this;
    }

    /**
     * @return bool
     */
    public function getCouponSingleUse()
    {
        return $this->couponSingleUse;
    }

    /**
     * @param integer $position Position
     *
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    // {{{ Translation Getters / setters

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $subject
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setSubject($subject)
    {
        return $this->setTranslationField(__FUNCTION__, $subject);
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $body
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setBody($body)
    {
        return $this->setTranslationField(__FUNCTION__, $body);
    }

    /**
     * @return string
     */
    public function getCouponSubject()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $couponSubject
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setCouponSubject($couponSubject)
    {
        return $this->setTranslationField(__FUNCTION__, $couponSubject);
    }

    /**
     * @return string
     */
    public function getCouponBody()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $couponBody
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setCouponBody($couponBody)
    {
        return $this->setTranslationField(__FUNCTION__, $couponBody);
    }

    // }}}
}
