<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\Core\Mail;

use XLite\Core\Mailer;
use XLite\Core\Translation;

class CustomerSatisfactionFeedbackMessage extends \XLite\Core\Mail\AMail
{
    public static function getZone()
    {
        return \XLite::ZONE_ADMIN;
    }

    public static function getDir()
    {
        return 'modules/QSL/CustomerSatisfaction/feedback';
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
     * @param \CDev\ContactUs\Model\Contact $contact
     * @param array|string                               $mails
     */
    public function __construct(array $data)
    {
        parent::__construct();

        $customerEmail = $data['data']['order']->getProfile()->getLogin();

        $this->setTo(Mailer::getSupportDepartmentMail());
        $this->setFrom($customerEmail);
        $this->appendData(['data' => $data['data']]);
        $this->appendData(['orderNumber' => $data['orderNumber']]);
        $this->appendData(['customerComment' => $data['customerComment']]);
        $this->appendData(['answers' => $data['answers']]);
        $this->populateVariables([
            'order_number'  => htmlspecialchars($data['data']['order']->getOrderNumber()),
        ]);
    }
}
