<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail;

use XLite\Core\Mail\Common\AccessLinkCustomer;
use XLite\Core\Mail\Common\DeletedAdmin;
use XLite\Core\Mail\Common\FailedLoginAdmin;
use XLite\Core\Mail\Common\FailedTransactionAdmin;
use XLite\Core\Mail\Common\LowLimitAdmin;
use XLite\Core\Mail\Common\SafeMode;
use XLite\Core\Mail\Common\TestEmail;
use XLite\Core\Mail\Common\UpgradeSafeMode;
use XLite\Core\Mail\Order\BackorderCreatedAdmin;
use XLite\Core\Mail\Order\CanceledAdmin;
use XLite\Core\Mail\Order\CanceledCustomer;
use XLite\Core\Mail\Order\ChangedAdmin;
use XLite\Core\Mail\Order\ChangedCustomer;
use XLite\Core\Mail\Order\CreatedAdmin;
use XLite\Core\Mail\Order\CreatedCustomer as OrderCreatedCustomer;
use XLite\Core\Mail\Order\FailedAdmin;
use XLite\Core\Mail\Order\FailedCustomer;
use XLite\Core\Mail\Order\ProcessedAdmin;
use XLite\Core\Mail\Order\ProcessedCustomer;
use XLite\Core\Mail\Order\ShippedCustomer;
use XLite\Core\Mail\Order\TrackingCustomer;
use XLite\Core\Mail\Order\WfaCustomer;
use XLite\Core\Mail\Profile\CreatedAdmin as ProfileCreatedAdmin;
use XLite\Core\Mail\Profile\CreatedCustomer;
use XLite\Core\Mail\Profile\RecoverPasswordAdmin;
use XLite\Core\Mail\Profile\RecoverPasswordCustomer;
use XLite\Core\Mail\Profile\RegisterAnonymous;

abstract class Registry
{
    protected static $sent = [];

    /**
     * @param $dir
     * @param $zone
     *
     * @return array
     */
    public static function getNotificationVariables($dir, $zone)
    {
        if (isset(static::getNotificationsList()[$zone][$dir])) {
            $variables = call_user_func(static::getNotificationsList()[$zone][$dir] . '::getVariables');

            return array_combine(
                array_map(static function ($name) {
                    return "%{$name}%";
                }, array_keys($variables)),
                array_values($variables)
            );
        }

        return [];
    }

    /**
     * @param string $zone
     * @param string $path
     * @param array $data
     *
     * @return AMail|null
     * @throws \ReflectionException
     */
    public static function createNotification($zone, $path, $data)
    {
        if (
            ($class = static::getNotificationClass($zone, $path))
            && class_exists($class)
        ) {
            $reflection = new \ReflectionClass($class);

            return $reflection->newInstanceArgs($data);
        }

        return null;
    }

    /**
     * @param string $zone
     * @param string $path
     *
     * @return string|null
     */
    protected static function getNotificationClass($zone, $path)
    {
        return static::getNotificationsList()[$zone][$path] ?? null;
    }

    /**
     * @return array
     */
    protected static function getNotificationsList()
    {
        return [
            \XLite::ZONE_CUSTOMER =>
                [
                    'access_link'                => AccessLinkCustomer::class,
                    'order_canceled'             => CanceledCustomer::class,
                    'order_changed'              => ChangedCustomer::class,
                    'order_created'              => OrderCreatedCustomer::class,
                    'order_failed'               => FailedCustomer::class,
                    'order_processed'            => ProcessedCustomer::class,
                    'order_shipped'              => ShippedCustomer::class,
                    'order_tracking_information' => TrackingCustomer::class,
                    'order_waiting_for_approve'  => WfaCustomer::class,
                    'profile_created'            => CreatedCustomer::class,
                    'recover_password_request'   => RecoverPasswordCustomer::class,
                    'register_anonymous'         => RegisterAnonymous::class,
                ],
            \XLite::ZONE_ADMIN    =>
                [
                    'profile_deleted'          => DeletedAdmin::class,
                    'failed_admin_login'       => FailedLoginAdmin::class,
                    'failed_transaction'       => FailedTransactionAdmin::class,
                    'low_limit_warning'        => LowLimitAdmin::class,
                    'safe_mode_key_generated'  => SafeMode::class,
                    'test_email'               => TestEmail::class,
                    'upgrade_access_keys'      => UpgradeSafeMode::class,
                    'order_canceled'           => CanceledAdmin::class,
                    'order_changed'            => ChangedAdmin::class,
                    'order_created'            => CreatedAdmin::class,
                    'order_failed'             => FailedAdmin::class,
                    'order_processed'          => ProcessedAdmin::class,
                    'profile_created'          => ProfileCreatedAdmin::class,
                    'recover_password_request' => RecoverPasswordAdmin::class,
                    'backorder_created'        => BackorderCreatedAdmin::class,
                ],
        ];
    }

    /**
     * Return Sent
     *
     * @return array
     */
    public static function getSent()
    {
        return static::$sent;
    }

    /**
     * Set Sent
     *
     * @param array $sent
     */
    public static function setSent($sent)
    {
        static::$sent = $sent;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public static function isSent($key)
    {
        return in_array($key, static::getSent());
    }

    /**
     * @param string $key
     */
    public static function addToSent($key)
    {
        static::$sent[] = $key;
    }

    /**
     * @param string $key
     */
    public static function removeFromSent($key)
    {
        static::setSent(array_diff(static::getSent(), [$key]));
    }
}
