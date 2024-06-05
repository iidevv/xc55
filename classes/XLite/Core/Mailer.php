<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use Symfony\Component\Messenger\MessageBusInterface;
use XCart\Container;
use XCart\Messenger\Message\SendMail;
use XLite\Core\Mail\Common\AccessLinkCustomer;
use XLite\Core\Mail\Common\DeletedAdmin;
use XLite\Core\Mail\Common\FailedLoginAdmin;
use XLite\Core\Mail\Common\FailedTransactionAdmin;
use XLite\Core\Mail\Common\LowLimitAdmin;
use XLite\Core\Mail\Common\SafeMode;
use XLite\Core\Mail\Common\TestEmail;
use XLite\Core\Mail\Common\UpgradeSafeMode;
use XLite\Core\Mail\Order\CanceledAdmin;
use XLite\Core\Mail\Order\CanceledCustomer;
use XLite\Core\Mail\Order\ChangedAdmin;
use XLite\Core\Mail\Order\ChangedCustomer;
use XLite\Core\Mail\Order\CreatedAdmin as OrderCreatedAdmin;
use XLite\Core\Mail\Order\CreatedCustomer as OrderCreatedCustomer;
use XLite\Core\Mail\Order\FailedAdmin;
use XLite\Core\Mail\Order\FailedCustomer;
use XLite\Core\Mail\Order\ProcessedAdmin;
use XLite\Core\Mail\Order\ProcessedCustomer;
use XLite\Core\Mail\Order\ShippedCustomer;
use XLite\Core\Mail\Order\TrackingCustomer;
use XLite\Core\Mail\Order\WfaCustomer;
use XLite\Core\Mail\Order\BackorderCreatedAdmin;
use XLite\Core\Mail\Profile\CreatedAdmin;
use XLite\Core\Mail\Profile\CreatedCustomer;
use XLite\Core\Mail\Profile\RecoverPasswordAdmin;
use XLite\Core\Mail\Profile\RecoverPasswordCustomer;
use XLite\Core\Mail\Profile\RegisterAnonymous;
use XLite\Model\Order;
use XLite\Model\Payment\Transaction;
use XLite\Model\Profile;

/**
 * Mailer core class
 */
class Mailer extends \XLite\Base\Singleton
{
    /**
     * @return MessageBusInterface
     */
    protected static function getBus(): MessageBusInterface
    {
        return Container::getContainer()->get('messenger.default_bus');
    }

    /**
     * @param Profile $profile    Profile object
     * @param string  $password   Profile password OPTIONAL
     * @param boolean $byCheckout By checkout flag OPTIONAL
     */
    public static function sendProfileCreated(Profile $profile, $password = null, $byCheckout = false)
    {
        static::sendProfileCreatedAdmin($profile);

        static::sendProfileCreatedCustomer($profile, $password, $byCheckout);
    }

    /**
     * @param Profile $profile Profile object
     */
    public static function sendProfileCreatedAdmin(Profile $profile)
    {
        static::getBus()->dispatch(new SendMail(CreatedAdmin::class, [$profile]));
    }

    /**
     * @param Profile $profile    Profile object
     * @param string  $password   Profile password OPTIONAL
     * @param boolean $byCheckout By checkout flag OPTIONAL
     */
    public static function sendProfileCreatedCustomer(
        Profile $profile,
        $password = null,
        $byCheckout = false
    ) {
        static::getBus()->dispatch(new SendMail(CreatedCustomer::class, [$profile, $password, $byCheckout]));
    }

    /**
     * @param Profile $profile  Profile object
     * @param string  $password Profile password
     */
    public static function sendRegisterAnonymousCustomer(Profile $profile, $password)
    {
        static::getBus()->dispatch(new SendMail(RegisterAnonymous::class, [$profile, $password]));
    }

    /**
     * @param string $deletedLogin Login of deleted profile
     */
    public static function sendProfileDeleted(string $deletedLogin)
    {
        static::sendProfileDeletedAdmin($deletedLogin);
    }

    /**
     * @param string $deletedLogin Login of deleted profile
     */
    public static function sendProfileDeletedAdmin(string $deletedLogin)
    {
        static::getBus()->dispatch(new SendMail(DeletedAdmin::class, [$deletedLogin]));
    }

    /**
     * @param string $postedLogin Login that was used in failed login attempt
     */
    public static function sendFailedAdminLoginAdmin(string $postedLogin)
    {
        $args = [$postedLogin, Request::getInstance()->getClientIp()];
        static::getBus()->dispatch(new SendMail(FailedLoginAdmin::class, $args));
    }

    /**
     * @param Profile $profile              Profile
     * @param string  $userPasswordResetKey User password
     */
    public static function sendRecoverPasswordRequest($profile, $userPasswordResetKey)
    {
        $mail = $profile->isAdmin()
            ? (new RecoverPasswordAdmin($profile, $userPasswordResetKey))
            : (new RecoverPasswordCustomer($profile, $userPasswordResetKey));

        if ($mail::isEnabled()) {
            $mail->send();
        }
    }

    /**
     * @param Order $order Order object
     */
    public static function sendOrderTrackingInformationCustomer(Order $order)
    {
        static::getBus()->dispatch(new SendMail(TrackingCustomer::class, [$order]));
    }

    /**
     * @param Order $order Order model
     */
    public static function sendOrderCreated(Order $order)
    {
        static::sendOrderCreatedAdmin($order);

        static::sendOrderCreatedCustomer($order);
    }

    /**
     * @param Order $order Order model
     */
    public static function sendOrderCreatedAdmin(Order $order)
    {
        static::getBus()->dispatch(new SendMail(OrderCreatedAdmin::class, [$order]));
    }

    /**
     * @param Order $order Order model
     */
    public static function sendOrderCreatedCustomer(Order $order)
    {
        static::getBus()->dispatch(new SendMail(OrderCreatedCustomer::class, [$order]));
    }

    /**
     * @param Order   $order                      Order model
     * @param boolean $ignoreCustomerNotification Flag: do not send notification to customer
     *                                            OPTIONAL
     */
    public static function sendOrderProcessed(Order $order, $ignoreCustomerNotification = false)
    {
        static::sendOrderProcessedAdmin($order);

        if (!$ignoreCustomerNotification) {
            static::sendOrderProcessedCustomer($order);
        }
    }

    /**
     * @param Order $order Order model
     */
    public static function sendOrderProcessedAdmin(Order $order)
    {
        $mail = new ProcessedAdmin($order);

        if ($mail::isEnabled()) {
            static::getBus()->dispatch(new SendMail(ProcessedAdmin::class, [$order]));
        } elseif ($order->isJustClosed()) {
            // OrderProcessed notification is disabled - send OrderCreated if order just created by customer
            static::sendOrderCreatedAdmin($order);
        }
    }

    /**
     * @param Order $order Order model
     */
    public static function sendOrderProcessedCustomer(Order $order)
    {
        $mail = new ProcessedCustomer($order);

        if ($mail::isEnabled()) {
            static::getBus()->dispatch(new SendMail(ProcessedCustomer::class, [$order]));
        } elseif ($order->isJustClosed()) {
            // OrderProcessed notification is disabled - send OrderCreated if order just created by customer
            static::sendOrderCreatedCustomer($order);
        }
    }

    /**
     * @param Order   $order                      Order model
     * @param boolean $ignoreCustomerNotification Flag: do not send notification to customer
     *                                            OPTIONAL
     */
    public static function sendOrderChanged(Order $order, $ignoreCustomerNotification = false)
    {
        static::sendOrderChangedAdmin($order);

        if (!$ignoreCustomerNotification) {
            static::sendOrderChangedCustomer($order);
        }
    }

    /**
     * @param Order $order Order model
     */
    public static function sendOrderChangedAdmin(Order $order)
    {
        if (ChangedAdmin::isEnabled()) {
            static::getBus()->dispatch(new SendMail(ChangedAdmin::class, [$order]));
        }
    }

    /**
     * @param Order $order Order model
     */
    public static function sendOrderChangedCustomer(Order $order)
    {
        if (ChangedCustomer::isEnabled()) {
            static::getBus()->dispatch(new SendMail(ChangedCustomer::class, [$order]));
        }
    }

    /**
     * @param Order $order Order object
     */
    public static function sendOrderShipped(Order $order)
    {
        static::sendOrderShippedCustomer($order);
    }

    /**
     * @param Order $order Order object
     */
    public static function sendOrderShippedCustomer(Order $order)
    {
        static::getBus()->dispatch(new SendMail(ShippedCustomer::class, [$order]));
    }

    /**
     * @param Order $order Order object
     */
    public static function sendOrderWaitingForApprove(Order $order)
    {
        static::sendOrderWaitingForApproveCustomer($order);
    }

    /**
     * @param Order $order Order object
     */
    public static function sendOrderWaitingForApproveCustomer(Order $order)
    {
        static::getBus()->dispatch(new SendMail(WfaCustomer::class, [$order]));
    }

    /**
     * @param Order $order                      Order model
     * @param bool  $ignoreCustomerNotification Flag: do not send notification to customer
     *                                          OPTIONAL
     */
    public static function sendOrderFailed(Order $order, bool $ignoreCustomerNotification = false)
    {
        static::sendOrderFailedAdmin($order);

        if (!$ignoreCustomerNotification) {
            static::sendOrderFailedCustomer($order);
        }
    }

    /**
     * @param Order $order Order model
     */
    public static function sendOrderFailedAdmin(Order $order)
    {
        static::getBus()->dispatch(new SendMail(FailedAdmin::class, [$order]));
    }

    /**
     * @param Order $order Order model
     */
    public static function sendOrderFailedCustomer(Order $order)
    {
        static::getBus()->dispatch(new SendMail(FailedCustomer::class, [$order]));
    }

    /**
     * @param Order $order                      Order model
     * @param bool  $ignoreCustomerNotification Flag: do not send notification to customer
     *                                          OPTIONAL
     */
    public static function sendOrderCanceled(Order $order, bool $ignoreCustomerNotification = false)
    {
        static::sendOrderCanceledAdmin($order);

        if (!$ignoreCustomerNotification) {
            static::sendOrderCanceledCustomer($order);
        }
    }

    /**
     * @param Order $order Order model
     */
    public static function sendOrderCanceledAdmin(Order $order)
    {
        static::getBus()->dispatch(new SendMail(CanceledAdmin::class, [$order]));
    }

    /**
     * @param Order $order Order model
     */
    public static function sendOrderCanceledCustomer(Order $order)
    {
        static::getBus()->dispatch(new SendMail(CanceledCustomer::class, [$order]));
    }

    /**
     * @param Order $order Order model
     */
    public static function sendBackorderCreatedAdmin(Order $order)
    {
        static::getBus()->dispatch(new SendMail(BackorderCreatedAdmin::class, [$order]));
    }

    /**
     * @param Profile                        $profile Order model
     * @param \XLite\Model\AccessControlCell $acc     Order model
     */
    public static function sendAccessLinkCustomer(Profile $profile, \XLite\Model\AccessControlCell $acc)
    {
        static::getBus()->dispatch(new SendMail(AccessLinkCustomer::class, [$profile, $acc]));
    }

    /**
     * @param string $key        Access key
     * @param bool   $keyChanged is key new
     */
    public static function sendSafeModeAccessKeyNotification(string $key, bool $keyChanged = false)
    {
        $mail = new SafeMode($key, $keyChanged);
        if ($mail::isEnabled()) {
            $mail->send();
        }
    }

    public static function sendUpgradeSafeModeAccessKeyNotification()
    {
        $mail = new UpgradeSafeMode();
        if ($mail::isEnabled()) {
            $mail->send();
        }
    }

    /**
     * @param string $from Email address to send test email from
     * @param string $to   Email address to send test email to
     * @param string $body Body test email text OPTIONAL
     *
     * @return string|null
     */
    public static function sendTestEmail(string $from, string $to, string $body = ''): ?string
    {
        $mail = new TestEmail($from, $to, $body);
        $mail->send();

        return $mail->getError();
    }

    /**
     * @param array $data Product data
     */
    public static function sendLowLimitWarningAdmin(array $data)
    {
        static::getBus()->dispatch(new SendMail(LowLimitAdmin::class, [$data]));
    }

    /**
     * @param Transaction $transaction
     */
    public static function sendFailedTransactionAdmin(Transaction $transaction)
    {
        static::getBus()->dispatch(new SendMail(FailedTransactionAdmin::class, [$transaction]));
    }

    /**
     * Sales department e-mail:
     *
     * @return string
     */
    public static function getOrdersDepartmentMail()
    {
        $emails = @unserialize(Config::getInstance()->Company->orders_department);

        return (is_array($emails) && !empty($emails))
            ? array_shift($emails)
            : static::getSiteAdministratorMail();
    }

    /**
     * Sales department e-mail:
     *
     * @return string[]
     */
    public static function getOrdersDepartmentMails()
    {
        $emails = @unserialize(Config::getInstance()->Company->orders_department);

        return (is_array($emails) && !empty($emails))
            ? $emails
            : static::getSiteAdministratorMails();
    }

    /**
     * Customer relations e-mail
     *
     * @return string
     */
    public static function getUsersDepartmentMail()
    {
        $emails = @unserialize(Config::getInstance()->Company->users_department);

        return (is_array($emails) && !empty($emails)) ? array_shift($emails) : '';
    }

    /**
     * Customer relations e-mail
     *
     * @return string[]
     */
    public static function getUsersDepartmentMails()
    {
        $emails = @unserialize(Config::getInstance()->Company->users_department);

        return (is_array($emails) && !empty($emails)) ? $emails : [];
    }

    /**
     * Customer relations e-mail
     *
     * @return string
     */
    public static function getSupportDepartmentMail()
    {
        $emails = @unserialize(Config::getInstance()->Company->support_department);

        return (is_array($emails) && !empty($emails)) ? array_shift($emails) : '';
    }

    /**
     * Support e-mails
     *
     * @return string[]
     */
    public static function getSupportDepartmentMails()
    {
        $emails = @unserialize(Config::getInstance()->Company->support_department);

        return (is_array($emails) && !empty($emails)) ? $emails : [];
    }

    /**
     * Site administrator e-mail
     *
     * @return string
     */
    public static function getSiteAdministratorMail()
    {
        $emails = @unserialize(Config::getInstance()->Company->site_administrator);

        return (is_array($emails) && !empty($emails)) ? array_shift($emails) : '';
    }

    /**
     * Site administrator e-mail
     *
     * @return string[]
     */
    public static function getSiteAdministratorMails()
    {
        $emails = @unserialize(Config::getInstance()->Company->site_administrator);

        return (is_array($emails) && !empty($emails)) ? $emails : [];
    }
}
