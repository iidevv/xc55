<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Core;

use QSL\AbandonedCartReminder\Model\Reminder;
use QSL\AbandonedCartReminder\Model\Email;
use QSL\AbandonedCartReminder\Core\TokenReplacer\AbandonmentEmail;
use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Tool that does all the job of sending a reminder for a cart.
 */
class CartReminder extends \XLite\Base
{
    use ExecuteCachedTrait;

    /**
     * The cart for which the reminder should be sent.
     *
     * @var \XLite\Model\Cart
     */
    protected $cart;

    /**
     * The number of reminder to send.
     *
     * @var integer
     */
    protected $reminder;

    /**
     * Constructor
     *
     * @param \XLite\Model\Cart                                      $cart     The cart for which the reminder should be sent.
     * @param \QSL\AbandonedCartReminder\Model\Reminder $reminder What reminder to send
     *
     * @return \QSL\AbandonedCartReminder\Core\CartReminder
     */
    public function __construct(\XLite\Model\Cart $cart, Reminder $reminder)
    {
        $this->cart = $cart;
        $this->reminder = $reminder;
    }

    /**
     * Send the reminder.
     *
     * @return int
     */
    public function send()
    {
        $sent = $this->sendMessages();
        if ($sent) {
            $this->updateCart();
        }

        return $sent;
    }

    /**
     * Return the cart for which the reminder should be sent.
     *
     * @return \XLite\Model\Cart
     */
    protected function getCart()
    {
        return $this->cart;
    }

    /**
     * Return the reminder that should be sent.
     *
     * @return \QSL\AbandonedCartReminder\Model\Reminder
     */
    protected function getReminder()
    {
        return $this->reminder;
    }

    /**
     * Send reminder messages.
     *
     * @return int
     */
    protected function sendMessages()
    {
        return $this->sendEmail() ? 1 : 0;
    }

    /**
     * Send a reminder email.
     *
     * @return bool
     */
    protected function sendEmail()
    {
        $cart = $this->getCart();

        $result = false;

        if ($cart) {
            $reminder = new Email();
            $reminder->setOrder($cart);
            $reminder->setReminderId($this->getReminder()->getId());
            $reminder->getRepository()->insert($reminder);
            $cart->addCartReminderEmail($reminder);

            $tokenReplacer = new AbandonmentEmail(
                $this->processTokenReplacerParams([
                    'cart' => $this->getCart(),
                    'email' => $reminder,
                ])
            );

            $subject = $tokenReplacer->replaceTokens($this->getReminderSubject());
            $body = $tokenReplacer->replaceTokens(
                $this->preprocessReminderBody($this->getReminderBody())
            );

            $result = \XLite\Core\Mailer::sendAbandonmentEmail($cart->getProfile(), $subject, $body);
            if ($result) {
                $reminder->setDateSent(\XLite\Core\Converter::time());
            }
        }

        return $result;
    }

    /**
     * Returns the e-mail subject for the reminder.
     *
     * @return string
     */
    protected function getReminderSubject()
    {
        return $this->getReminder()->getReminderSubject();
    }

    /**
     * Returns tokens (placeholders) and the text that should replace the tokens.
     *
     * @return array
     */
    protected function processTokenReplacerParams($params)
    {
        return $params;
    }

    /**
     * Preprocesses the e-mail body for the reminder.
     *
     * @return string
     */
    protected function preprocessReminderBody($body)
    {
        // Don't let visual editors corrupt recovery links
        return str_replace('%5BRECOVERY_LINK%5D', '[RECOVERY_LINK]', $body);
    }

    /**
     * Returns the e-mail body for the reminder.
     *
     * @return string
     */
    protected function getReminderBody()
    {
        return $this->getReminder()->getReminderBody();
    }

    /**
     * Cache coupon generated for the reminder.
     *
     * @param \CDev\Coupons\Model\Coupon $coupon Coupon model
     *
     * @return void
     */
    protected function setCoupon(\CDev\Coupons\Model\Coupon $coupon)
    {
        $this->coupon = $coupon;
    }

    /**
     * Return coupon generated for the reminder.
     *
     * @return \CDev\Coupons\Model\Coupon
     */
    protected function getCoupon()
    {
        return $this->coupon;
    }

    /**
     * Update the cart and mark that a new reminder has been sent.
     *
     * @return void
     */
    protected function updateCart()
    {
        $cart = $this->getCart();

        $cart->setCartRemindersSent(1 + $cart->getCartRemindersSent());
        $cart->setCartReminderDate(\XLite\Core\Converter::time());
    }
}
