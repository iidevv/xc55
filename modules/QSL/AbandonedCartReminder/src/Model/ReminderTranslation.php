<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reminder multilingual data.
 *
 * @ORM\Entity
 * @ORM\Table  (name="cart_reminder_translations",
 *      indexes={
 *          @ORM\Index (name="ci", columns={"code","id"}),
 *          @ORM\Index (name="id", columns={"id"})
 *      }
 * )
 */
class ReminderTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Reminder subject.
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $subject;

    /**
     * Reminder text.
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $body = '';

    /**
     * Reminder subject (with coupon).
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $couponSubject;

    /**
     * Reminder text (with coupon).
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $couponBody = '';

    /**
     * @var \QSL\AbandonedCartReminder\Model\Reminder
     *
     * @ORM\ManyToOne (targetEntity="QSL\AbandonedCartReminder\Model\Reminder", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="reminder_id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Updates the reminder subject (no coupon).
     *
     * @param string $subject New subject
     *
     * @return ReminderTranslation
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Returns the reminder subject (no coupon).
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Updates the reminder message (no coupon).
     *
     * @param string $body New message
     *
     * @return ReminderTranslation
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Returns the reminder message (no coupon).
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Updates the reminder subject (with coupon).
     *
     * @param string $couponSubject New subject
     *
     * @return ReminderTranslation
     */
    public function setCouponSubject($couponSubject)
    {
        $this->couponSubject = $couponSubject;

        return $this;
    }

    /**
     * Returns the reminder subject (with coupon).
     *
     * @return string
     */
    public function getCouponSubject()
    {
        return $this->couponSubject;
    }

    /**
     * Updates the reminder message  (with coupon).
     *
     * @param string $couponBody New message
     *
     * @return ReminderTranslation
     */
    public function setCouponBody($couponBody)
    {
        $this->couponBody = $couponBody;

        return $this;
    }

    /**
     * Returns the reminder message (with coupon).
     *
     * @return string
     */
    public function getCouponBody()
    {
        return $this->couponBody;
    }

    /**
     * Returns the translation label identifier.
     *
     * @return integer
     */
    public function getLabelId()
    {
        return $this->label_id;
    }

    /**
     * Updates the language code of the translation label.
     *
     * @param string $code New code
     *
     * @return ReminderTranslation
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Returns the language code of the translation label.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
