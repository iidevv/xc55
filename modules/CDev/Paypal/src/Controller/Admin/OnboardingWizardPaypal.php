<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\InjectLoggerTrait;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\Onboarding")
 */
class OnboardingWizardPaypal extends \XC\Onboarding\Controller\Admin\OnboardingWizard
{
    use InjectLoggerTrait;

    public function doActionUpdateLocation()
    {
        parent::doActionUpdateLocation();

        $countryCode = \Xlite\Core\Request::getInstance()->country;

        if ($countryCode) {
            \XLite\Core\Event::updatePaypalCard();
        }
    }

    /**
     * Return URL for Paypal Signup
     *
     * @return void
     */
    protected function doActionUpdateCredentials()
    {
        $request = \XLite\Core\Request::getInstance();
        $data = [];

        if ($request->merchantIdInPayPal) {
            $apiClient = new \CDev\Paypal\Core\RESTAPI();

            $data = $apiClient->getMerchantCredentials(
                \CDev\Paypal\Core\RESTAPI::PARTNER_ID,
                $request->merchantIdInPayPal
            );
        }

        $method = \CDev\Paypal\Main::getPaymentMethod(\CDev\Paypal\Main::PP_METHOD_EC);

        if ($data && isset($data['api_credentials']) && isset($data['api_credentials']['signature'])) {
            $credentials = $data['api_credentials']['signature'];

            $method->setSetting('api_type', 'api');
            $method->setSetting('api_solution', 'paypal');
            $method->setSetting('api_username', $credentials['api_user_name']);
            $method->setSetting('api_password', $credentials['api_password']);
            $method->setSetting('auth_method', 'signature');
            $method->setSetting('signature', $credentials['signature']);
            $method->setSetting('mode', 'live');
            $method->setSetting('merchantId', $data['merchant_id']);

            $method->update();

            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                [
                    'category' => 'CDev\Paypal',
                    'name'     => 'show_admin_welcome',
                    'value'    => 'N',
                ]
            );

            \XLite\Core\TopMessage::getInstance()->addInfo(
                'Your API credentials have been successfully obtained from your PayPal account'
                . ' and saved for use by your X-Cart store.'
            );
        } else {
            $this->getLogger('CDev-Paypal')->error('API credentials could not be obtained from your PayPal', $data);
            \XLite\Core\TopMessage::getInstance()->addError(
                'Unfortunately, your API credentials could not be obtained from your PayPal account automatically.'
            );
            if ($request->returnMessage) {
                \XLite\Core\TopMessage::getInstance()->addInfo($request->returnMessage);
            }
        }

        $this->setHardRedirect(true);
        $this->setReturnURL($this->buildURL('onboarding_wizard'));
    }
}
