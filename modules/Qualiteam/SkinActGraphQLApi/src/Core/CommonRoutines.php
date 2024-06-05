<?php


namespace Qualiteam\SkinActGraphQLApi\Core;


use XcartGraphqlApi\Types;
use XLite\Core\Database;

class CommonRoutines extends \XLite\Base\Singleton
{

    public function exception($message)
    {
        throw new \Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\CustomError($message);
    }

    public function preprocessArgs(&$args)
    {
        $countryCode = $args['location_country'] ?? ''; // US
        $stateName = $args['location_state'] ?? ''; // California or CA or 562

        if (!empty($countryCode) && !empty($stateName)) {

            /** @var \XLite\Model\Country $country */
            $country = Database::getRepo('XLite\Model\Country')->findOneBy(['code' => $countryCode]);

            if (!$country) {
                $this->exception('Invalid "location_country" param');
            }

            if ($country->hasStates()) {
                /** @var \XLite\Model\State[] $states */
                $states = $country->getStates();
                $isStateExists = false;
                foreach ($states as $state) {
                    if ($state->getState() === $stateName
                        || $state->getCode() === $stateName
                        || $state->getStateId() === (int)$stateName
                    ) {
                        $isStateExists = true;
                        $args['location_state'] = $state->getStateId();
                        break;
                    }
                }
                if (!$isStateExists) {
                    $this->exception("Provided country has no state \"$stateName\"");
                }

            } else {
                $this->exception('Provided country has no states');
            }

        } else {
            $this->exception('Invalid "location_state" or "location_country" param(s)');
        }
    }

    public function setupRequestFields($args)
    {
        $defaults = \json_decode('
            {
              "target": "register_vendor",
              "action": "register",
              "login": "",
              "password": "",
              "password_conf": "",
              "company_name": "",
              "description": "",
              "paypal_account": "",
              "firstname": "",
              "lastname": "",
              "location_address": "",
              "location_city": "",
              "location_country": "",
              "location_state": "",
              "location_zipcode": "",
              "company_phone": "",
              "company_fax": ""
            }', true);

        foreach ($args as $k => $v) {
            if (isset($defaults[$k])) {
                $defaults[$k] = $v;
            }
        }

        \XLite\Core\Request::getInstance()->mapRequest($defaults);

    }

    public function makeVendor($args, $context, $operationType = null)
    {
        \XLite\Core\TopMessage::getInstance()->unloadPreviousMessages();

        \Qualiteam\SkinActGraphQLApi\Core\ActionResult::getInstance()->setIsEnabled(true);

        $this->setupRequestFields($args);

        $registerVendor = \XC\MultiVendor\Controller\Customer\RegisterVendor::getInstance();

        \Closure::bind(function () {
            return $this->doActionRegister();
        }, $registerVendor, \XC\MultiVendor\Controller\Customer\RegisterVendor::class)();

        \Qualiteam\SkinActGraphQLApi\Core\ActionResult::getInstance()->setIsEnabled(false);

        $this->processTopMessages();

        if ($operationType !== 'convert') {
            $profile = Database::getRepo('XLite\Model\Profile')->findOneBy(['login' => $args['login']]);
        } else {
            $profile = $context->getLoggedProfile();
        }

        if (!$profile) {
            $this->exception('Unexpected error while vendor registration.');
        }

        return $profile;
    }

    public function validateVendorPlanId($args)
    {
        $id = (int)($args['consignItAwaySignupForVendorPlan'] ?? null);

        if ($id <= 0) {
            $this->exception('Invalid "consignItAwaySignupForVendorPlan" param');
        }

        $plan = Database::getRepo('\Qualiteam\ConsignItAwayVendorPlans\Model\VendorPlan')->findOneBy([
            'enabled' => true, 'vendor_plan_id' => $id
        ]);

        if (!$plan) {
            $this->exception("Specified vendor plan ($id) is no exists or not enabled");
        }

    }

    public function getTypeArgsConvert()
    {
        return [
            //'login' => Types::nonNull(Types::string()),
            //'password' => Types::nonNull(Types::string()),
            //'password_conf' => Types::nonNull(Types::string()),
            'company_name' => Types::nonNull(Types::string()),
            'description' => Types::string(),
           // 'paypal_account' => Types::nonNull(Types::string()),
            'firstname' => Types::nonNull(Types::string()),
            'lastname' => Types::nonNull(Types::string()),
            'location_address' => Types::nonNull(Types::string()),
            'location_city' => Types::nonNull(Types::string()),
            'location_country' => Types::nonNull(Types::string()),
            'location_state' => Types::nonNull(Types::string()),
            'location_zipcode' => Types::nonNull(Types::string()),
            'company_phone' => Types::string(),
            'company_fax' => Types::string(),
            'consignItAwaySignupForVendorPlan' => Types::nonNull(Types::int()),
        ];
    }

    public function getTypeArgsRegister()
    {
        return [
            'login' => Types::nonNull(Types::string()),
            'password' => Types::nonNull(Types::string()),
            'password_conf' => Types::nonNull(Types::string()),
            'company_name' => Types::nonNull(Types::string()),
            'description' => Types::string(),
            'paypal_account' => Types::string(),
            'firstname' => Types::nonNull(Types::string()),
            'lastname' => Types::nonNull(Types::string()),
            'location_address' => Types::nonNull(Types::string()),
            'location_city' => Types::nonNull(Types::string()),
            'location_country' => Types::nonNull(Types::string()),
            'location_state' => Types::nonNull(Types::string()),
            'location_zipcode' => Types::nonNull(Types::string()),
            'company_phone' => Types::string(),
            'company_fax' => Types::string(),
            'consignItAwaySignupForVendorPlan' => Types::nonNull(Types::int()),
        ];
    }

    protected function processTopMessages()
    {
        $messages = \XLite\Core\TopMessage::getInstance()->getMessages();

        $text = '';
        foreach ($messages as $k => $message) {
            if ($message['type'] === 'error') {
                $text .= $message['text'] . '; ';
            }
        }

        if (!empty($text)) {
            $this->exception(trim($text, '; '));
        }

        \XLite\Core\TopMessage::getInstance()->clear();
    }

}