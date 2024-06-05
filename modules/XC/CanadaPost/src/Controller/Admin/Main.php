<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Main page controller
 * @Extender\Mixin
 */
class Main extends \XLite\Controller\Admin\Main
{
    /**
     * ACTION: default action
     *
     * @return void
     */
    protected function doNoAction()
    {
        if (
            isset(\XLite\Core\Request::getInstance()->{'token-id'})
            && isset(\XLite\Core\Request::getInstance()->{'registration-status'})
        ) {
            $this->capostValidateMerchant();
        }

        parent::doNoAction();
    }

    /**
     * Validate return from Canada Post merchant registration process
     *
     * @return void
     */
    protected function capostValidateMerchant()
    {
        $token = \XLite\Core\Request::getInstance()->{'token-id'};
        $status = \XLite\Core\Request::getInstance()->{'registration-status'};

        if ($status === \XC\CanadaPost\Core\Service\Platforms::REG_STATUS_SUCCESS) {
            // Registration is complete

            // Send request to Canada Post server to retrieve merchant details
            $data = \XC\CanadaPost\Core\Service\Platforms::getInstance()
                ->callGetMerchantRegistrationInfoByToken($token);

            if (isset($data->merchantInfo)) {
                // Update Canada Post settings
                $this->updateCapostMerchantSettings($data->merchantInfo);

                // Disable wizard
                $this->disableCapostWizard();

                \XLite\Core\TopMessage::getInstance()->addInfo('Registration process has been completed successfully.');
            } else {
                foreach ($data->errors as $err) {
                    \XLite\Core\TopMessage::getInstance()->addError('ERROR: [' . $err->code . '] ' . $err->description);
                }
            }
        } else {
            // An error occurred

            if ($status === \XC\CanadaPost\Core\Service\Platforms::REG_STATUS_CANCELLED) {
                \XLite\Core\TopMessage::getInstance()->addError('Registration process has been canceled.');
            } else {
                \XLite\Core\TopMessage::getInstance()->addError('Failure to finish registration process.');
            }
        }

        // Remove token from the session
        \XLite\Core\Session::getInstance()->capost_token_id = null;
        \XLite\Core\Session::getInstance()->capost_token_ts = null;

        // Redirect back to the Canada Post settings page
        $this->setReturnURL($this->buildURL('capost'));
    }

    /**
     * Disable Canada Post merchant registration wizard
     *
     * @return void
     */
    protected function disableCapostWizard()
    {
        /** @var \XLite\Model\Repo\Config $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Config');
        $repo->createOption(
            [
                'category' => 'XC\CanadaPost',
                'name'     => 'wizard_enabled',
                'value'    => false,
            ]
        );
    }

    /**
     * Update Canada Post merchant settings
     *
     * @param \XLite\Core\CommonCell $data Merchant new data
     *
     * @return void
     */
    protected function updateCapostMerchantSettings($data)
    {
        $optionsMap = [
            'customer_number' => 'customerNumber',
            'contract_id'     => 'contractNumber',
            'user'            => 'merchantUsername',
            'password'        => 'merchantPassword',
            'quote_type'      => 'quoteType',
            'wizard_hash'     => 'wizardHash',
        ];

        $data->wizardHash = md5($data->merchantUsername . ':' . $data->merchantPassword);

        // Determine quote type
        $data->quoteType = (isset($data->contractNumber))
            ? \XC\CanadaPost\Core\API::QUOTE_TYPE_CONTRACTED
            : \XC\CanadaPost\Core\API::QUOTE_TYPE_NON_CONTRACTED;

        $options = \XLite\Core\Database::getRepo('\XLite\Model\Config')
            ->findBy(['category' => 'XC\CanadaPost', 'name' => array_keys($optionsMap)]);

        foreach ($options as $k => $o) {
            $field = $optionsMap[$o->getName()];

            $o->setValue((isset($data->{$field})) ? $data->{$field} : '');

            \XLite\Core\Database::getEM()->persist($o);
        }

        \XLite\Core\Database::getEM()->flush();
    }
}
