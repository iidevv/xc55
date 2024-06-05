<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\Module\XC\ThemeTweaker\Core\Notifications;

use XCart\Extender\Mapping\Extender;

/**
 * DataPreProcessor
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ThemeTweaker")
 */
class DataPreProcessor extends \XC\ThemeTweaker\Core\Notifications\DataPreProcessor
{
    /**
     * Prepare data to pass to constructor XC\Reviews\Core\Mail\OrderReviewKey
     *
     * @param string $dir Notification template directory
     * @param array $data Data
     *
     * @return array
     */
    public static function prepareDataForNotification($dir, array $data)
    {
        $data = parent::prepareDataForNotification($dir, $data);

        if ($dir === 'modules/QSL/CustomerSatisfaction/customer_notification') {
            $data = [static::getDemoCustomerSatisfactionData($data)];
        }
        if ($dir === 'modules/QSL/CustomerSatisfaction/feedback') {
            $data = [static::getDemoCustomerSatisfactionFeedbackData($data)];
        }

        return $data;
    }

    protected static function getDemoCustomerSatisfactionData($data)
    {
        $data = [
            'surveyId'     => 123,
            'customerName' => $data['order']->getProfile()->getName(),
            'order'        => $data['order'],
            'surveyKey'    => md5(time()),
        ];

        return \XLite\Core\Mailer::getCustomerSatisfactionNotificationData($data);
    }

    protected static function getDemoCustomerSatisfactionFeedbackData($data)
    {
        $questions = \XLite\Core\Database::getRepo('QSL\CustomerSatisfaction\Model\Question')
                                         ->findByEnabled(true);

        $data['answers'] = [];
        foreach ($questions as $question) {
            $data['answers'][] = [
                'originQuestion' => $question->getQuestion(),
                'value' => 'Lorem ipsum dolor sit amet',
            ];
        }

        $data['orderId'] = $data['order']->getOrderId();

        $data = [
            'orderNumber' => $data['order']->getOrderNumber(),
            'customerComment' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. A, ab architecto aut commodi consequatur delectus distinctio earum excepturi iusto laboriosam quaerat recusandae, repellendus ut, veritatis vitae? Ipsum iste nostrum saepe!',
            'data' => $data,
            'answers' => $data['answers'],
        ];

        return $data;
    }
}
