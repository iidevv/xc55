<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\Core;

use XCart\Extender\Mapping\Extender;
use XCart\Messenger\Message\SendMail;
use QSL\CustomerSatisfaction\Core\Mail\CustomerSatisfactionFeedbackMessage;
use QSL\CustomerSatisfaction\Core\Mail\CustomerSatisfactionNotification;

/**
 * Mailer
 * @Extender\Mixin
 */
abstract class Mailer extends \XLite\Core\Mailer
{
    public const TYPE_SURVEY = 'siteAdmin';

    /**
     * Send order survey to customer
     *
     * @param array $data Data
     */
    public static function sendCustomerSatisfactionNotification(array $data)
    {
        $toSend = static::getCustomerSatisfactionNotificationData($data);

        static::getBus()->dispatch(new SendMail(CustomerSatisfactionNotification::class, [$toSend]));
    }

    public static function getCustomerSatisfactionNotificationData($data)
    {
        $ratingUrls = [];
        $loyaltyProgrammeNotice = ['enabled' => false, 'pointsToEarn' => 0, 'products' => []];
        for ($i = 5; 0 < $i; $i--) {
            if ($i < \XLite\Core\Config::getInstance()->QSL->CustomerSatisfaction->cs_raiting_for_share) {
                $target = 'customer_survey';
            } else {
                $target = 'share_with_friends';
            }

            $ratingUrls[] = [
                'rating' => $i,
                'url'    => \XLite::getInstance()->getShopURL(
                    \XLite\Core\Converter::buildURL(
                        $target,
                        '',
                        [
                            'id'     => $data['surveyId'],
                            'rating' => $i,
                            'key'    => $data['surveyKey'],
                        ],
                        \XLite::CART_SELF
                    )
                ),
                'label'  => \QSL\CustomerSatisfaction\View\Survey::getRatingLabel($i),
            ];
        }

        $loyaltyProgrammeModule = \Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('QSL', 'LoyaltyProgram');
        $productReviewsModule = \Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('XC', 'Reviews');

        if ($loyaltyProgrammeModule && $productReviewsModule) {
            $loyaltyProgrammeNotice['pointsToEarn'] = max(
                [
                    \XLite\Core\Config::getInstance()->QSL->LoyaltyProgram->reward_points_reviews_review,
                    \XLite\Core\Config::getInstance()->QSL->LoyaltyProgram->reward_points_reviews_rate,
                ]
            );

            if ($loyaltyProgrammeNotice['pointsToEarn'] > 0) {
                $loyaltyProgrammeNotice['enabled'] = true;

                foreach ($data['order']->getItems() as $orderItem) {
                    $loyaltyProgrammeNotice['products'][] = [
                        'name' => $orderItem->getProduct()->getName(),
                        'URL' => self::buildProductReviewUrl($orderItem->getProduct()->getId()),
                    ];
                }
            }
        }

        return [
            'loyaltyProgrammeModule' => $loyaltyProgrammeNotice,
            'ratingURLs'             => $ratingUrls,
            'data'                   => $data,
            'recipientName'          => $data['order']->getProfile()->getName(),
        ];
    }

    /**
     * Send feedback notification
     *
     * @param array $data Data
     */
    public static function sendCSFeedbackMessage(array $data)
    {
        $answers = \XLite\Core\Database::getRepo('QSL\CustomerSatisfaction\Model\Answer')->findBy(
            ['survey' => $data['survey']]
        );

        $data['answers'] = [];
        foreach ($answers as $answer) {
            $data['answers'][] = [
                'originQuestion' => $answer->getOriginQuestion(),
                'value'          => $answer->getValue(),
            ];
        }
        $data['orderId'] = $data['order']->getOrderId();

        $toSend = [
            'orderNumber'     => $data['order']->getOrderNumber(),
            'customerComment' => $data['survey']->getCustomerMessage(),
            'data'            => $data,
            'answers'         => $data['answers'],
        ];

        static::getBus()->dispatch(new SendMail(CustomerSatisfactionFeedbackMessage::class, [$toSend]));
    }


    /**
     * Generates url for detail product page with opened review tav
     *
     * @param $productId
     * @return string
     */
    protected static function buildProductReviewUrl($productId)
    {
        $shopUrl = \XLite\Core\URLManager::getShopURL(\XLite\Core\Converter::buildURL('', '', [], ''));
        $productRewiewTab = '#product-details-tab-reviews';
        $url = \XLite\Core\Converter::buildURL(
            'product',
            '',
            ['product_id' => $productId],
            \XLite::getCustomerScript()
        );

        return $shopUrl . $url . $productRewiewTab;
    }
}
