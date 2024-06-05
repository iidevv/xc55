<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Concierge\Core;

use XLite\Core\Cache\ExecuteCachedTrait;
use XC\Concierge\Core\Message\Identify;

/**
 * Event mediator
 */
class Mediator extends \XLite\Base\Singleton
{
    use ExecuteCachedTrait;

    protected function __construct()
    {
        parent::__construct();

        if (PHP_SAPI !== 'cli' && !defined('LC_CACHE_BUILDING')) {
            $this->includeLibrary();
            if ($this->isConfigured()) {
                $this->initOptions();
            }
        }
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return \XLite\Core\Config::getInstance()->XC->Concierge->user_id ?? '';
    }

    /**
     * @return string
     */
    public function getWriteKey()
    {
        return \XLite\Core\Config::getInstance()->XC->Concierge->write_key ?? '';
    }

    /**
     * @return boolean
     */
    public function isConfigured()
    {
        return $this->executeCachedRuntime(function () {
            return (bool) $this->getWriteKey();
        });
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->executeCachedRuntime(function () {
            return $this->defineOptions();
        }, [__CLASS__, __METHOD__]);
    }

    public function initOptions()
    {
        $this->setRuntimeCache('getOptions', null);
        $this->getOptions();
    }

    /**
     * @return boolean
     */
    public function isIntercomViaSegmentEnabled()
    {
        return true;
    }

    /**
     * @return array
     */
    protected function defineOptions()
    {
        $languageCode = \XLite\Core\Session::getInstance()->getLanguage()->getCode();

        $result = [
            'anonymousId' => \XLite\Core\Session::getInstance()->getID(),
            'context'     => [
                'plugin' => [
                    'name'    => 'X-Cart',
                    'version' => \XC\Concierge\Main::getVersion(),
                ],
                // @todo: [ISO 639-1]_[ISO 3166-1 alpha-2]
                'locale' => $languageCode . '_' . strtoupper($languageCode),
            ],
            'userId' => \XLite\Core\Config::getInstance()->XC->Concierge->user_id,
        ];

        //if (\XLite\Core\Auth::getInstance()->isLogged()) {
        //    $result['userId'] = \XLite\Core\Auth::getInstance()->getProfile()->getProfileId();
        //}

        //if (!\XLite\Core\Auth::getInstance()->isLogged()) {
        //    $result['Intercom']['hideDefaultLauncher'] = true;
        //}

        return $result;
    }

    // {{{ Message

    /**
     * Add message to session
     *
     * @param AMessage $message
     *
     * @return boolean
     */
    public function addMessage(AMessage $message)
    {
        $result = false;

        if ($this->isConfigured()) {
            $messages = \XLite\Core\Session::getInstance()->concierge_messages;
            if (!is_array($messages)) {
                $messages = [];
            }

            if (in_array($message->getType(), [AMessage::TYPE_PAGE, AMessage::TYPE_TRACK], true)) {
                $message->setIntegrations(['All' => true, 'Intercom' => false]);
                $messages[] = $message->toArray();

                $message->setIntegrations(['All' => false, 'Intercom' => $this->isIntercomViaSegmentEnabled()]);
                $messages[] = $message->toArray('intercom');
            } else {
                $messages[] = $message->toArray();
            }

            \XLite\Core\Session::getInstance()->concierge_messages = $messages;

            $result = true;
        }

        return $result;
    }

    /**
     * Get stored messages
     *
     * @return array
     */
    public function getMessages()
    {
        $messages = \XLite\Core\Session::getInstance()->concierge_messages;
        unset(\XLite\Core\Session::getInstance()->concierge_messages);

        return is_array($messages) ? $messages : [];
    }

    /**
     * @return array|Identify
     */
    public function getIdentifyMessage()
    {
        $result = [];

        $messages = \XLite\Core\Session::getInstance()->concierge_messages;
        if ($messages) {
            foreach ($messages as $i => $message) {
                if ($message['type'] === AMessage::TYPE_IDENTIFY) {
                    $result = $message;
                    unset($messages[$i]);
                }
            }
            \XLite\Core\Session::getInstance()->concierge_messages = $messages;
        }

        if (!$result) {
            $auth = \XLite\Core\Auth::getInstance();
            $profile = $auth->getProfile();
            $config = \XLite\Core\Config::getInstance();
            $result = new Identify($config->XC->Concierge->user_id, $profile, $config);
        }
        unset(\XLite\Core\Session::getInstance()->sessionImmediateCreated);

        return $result ? [$result] : [];
    }

    /**
     * Throw track request
     *
     * @param string $event      Event name
     * @param array  $properties Event properties
     */
    public function throwTrack($event, array $properties)
    {
        $this->throwMessage(AMessage::TYPE_TRACK, [
            'event'      => $event,
            'properties' => $properties,
        ]);
    }

    /**
     * Throw track request
     *
     * @param string $type
     * @param array  $arguments
     */
    protected function throwMessage($type, $arguments)
    {
        if ($this->isConfigured() && method_exists('\Analytics', $type)) {
            call_user_func(['\Analytics', $type], array_merge($arguments, $this->getOptions()));
            \Analytics::flush();
        }
    }

    // }}}

    // {{{ Assemblers

    /**
     * Assemble message for 'identify' request
     * @todo: remove
     *
     * @param \XLite\Model\Profile $profile Profile
     * @param \XLite\Model\Address $address Address OPTIONAL
     *
     * @return array
     */
    protected function assembleIdentifyMessage(\XLite\Model\Profile $profile, \XLite\Model\Address $address = null)
    {
        $message = [
            $profile->getProfileId(),
            [
                'createdAt' => date('c', $profile->getAdded()),
                'email'     => $profile->getLogin(),
                'id'        => $profile->getProfileId(),
                'username'  => $profile->getLogin(),
            ],
        ];

        if (!$address) {
            $address = $profile->getBillingAddress() ?: $profile->getShippingAddress();
        }
        if ($address) {
            $message[1]['address'] = [
                'city'       => $address->getCity(),
                'country'    => $address->getCountry()->getCode(),
                'postalCode' => $address->getZipcode(),
                'state'      => $address->getState()->getState(),
                'street'     => $address->getStreet(),
            ];
            $message[1]['firstName'] = $address->getFirstname();
            $message[1]['lastName'] = $address->getLastname();
            $message[1]['name'] = $address->getName();
            $message[1]['phone'] = $address->getPhone();
            $message[1]['title'] = $address->getTitle();

            // Mixpanel
            $message[1]['$country_code'] = $address->getCountry()->getCode();
            $message[1]['$city'] = $address->getCity();
            $message[1]['$region'] = $address->getState()->getState();
        }

        return $message;
    }

    // }}}

    // {{{ Service

    /**
     * Include PHP SDK
     */
    protected function includeLibrary()
    {
        if ($this->isConfigured() && !class_exists('Analytics')) {
            class_alias('Segment', 'Analytics');
            \Analytics::init($this->getWriteKey());
        }
    }

    // }}}
}
