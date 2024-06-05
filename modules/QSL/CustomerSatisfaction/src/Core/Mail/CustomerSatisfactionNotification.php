<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\Core\Mail;

use XLite\Core\Mailer;
use XLite\Core\Translation;

class CustomerSatisfactionNotification extends \XLite\Core\Mail\AMail
{
    public static function getZone()
    {
        return \XLite::ZONE_CUSTOMER;
    }

    public static function getDir()
    {
        return 'modules/QSL/CustomerSatisfaction/customer_notification';
    }

    /**
     * @return array
     */
    protected static function defineVariables()
    {
        return [
                'order_number'  => Translation::lbl('Order Number'),
            ] + parent::defineVariables();
    }

    /**
     * ContactUsMessage constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct();

        $this->setTo($data['data']['order']->getProfile()->getLogin());
        $this->setFrom(Mailer::getSupportDepartmentMail());
        $this->tryToSetLanguageCode($data['data']['order']->getProfile()->getLanguage());
        $this->appendData([
            'data'                      => $data['data'],
            'loyaltyProgrammeModule'    => $data['loyaltyProgrammeModule'],
            'ratingURLs'                => $data['ratingURLs'],
        ]);
        $this->populateVariables([
            'order_number'              => htmlspecialchars($data['data']['order']->getOrderNumber()),
            'recipient_name'            => $data['recipientName'],
        ]);
    }

    /**
     * @return array
     */
    protected function getHashData()
    {
        return array_merge(parent::getHashData(), [$this->getSurveyHash()]);
    }

    /**
     * @return string
     */
    protected function getSurveyHash()
    {
        return $this->getData()['data']['surveyKey'] ?? '';
    }
}
