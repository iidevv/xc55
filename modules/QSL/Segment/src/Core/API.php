<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Segment\Core;

/**
 * Segment API wrapper
 */
class API extends \XLite\Base\Singleton
{
    /**
     * Valid flag
     *
     * @var boolean
     */
    protected $valid = false;

    /**
     * Check - valid API or not
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->valid;
    }

    // {{{ Wrappers

    /**
     * Call method wrapper
     *
     * @param string $type    Message type
     * @param array  $message Message
     *
     * @return mixed
     */
    public function call($type, array $message)
    {
        try {
            $result = $this->isValid()
                ? \Analytics::$type($this->preprocessTrackMessage($message))
                : null;

            if ($result) {
                $this->getMediator()->log(
                    'Send Server API request' . PHP_EOL
                    . 'Type: ' . $type . PHP_EOL
                    . 'Message: ' . var_export($message, true)
                );
            }
        } catch (\Exception $e) {
            \XLite\Logger::getInstance()->registerException($e);
            $result = null;
        }

        return $result;
    }

    /**
     * Call method wrapper
     *
     * @param array $message Message
     *
     * @return mixed
     */
    public function identify(array $message)
    {
        return $this->call('identify', $message);
    }

    /**
     * Call method wrapper
     *
     * @param array $message Message
     *
     * @return mixed
     */
    public function track(array $message)
    {
        return $this->call('identify', $this->preprocessTrackMessage($message));
    }

    /**
     * Preprocess 'track' message
     *
     * @param array $message Message
     *
     * @return array
     */
    protected function preprocessTrackMessage(array $message)
    {
        $message['anonymousId'] = \QSL\Segment\Core\Mediator::getInstance()->getAnonymousId();

        if (\XLite\Core\Auth::getInstance()->isLogged()) {
            $message['userId'] = \XLite\Core\Auth::getInstance()->getProfile()->getProfileId();
        }

        return $message;
    }

    // }}}

    // {{{ Service

    /**
     * Constructor
     */
    protected function __construct()
    {
        parent::__construct();

        try {
            if (\XLite\Core\Config::getInstance()->QSL->Segment->write_key) {
                class_alias('Segment', 'Analytics');

                \Analytics::init(\XLite\Core\Config::getInstance()->QSL->Segment->write_key);
                $this->valid = true;
            }
        } catch (\Exception $e) {
            \XLite\Logger::getInstance()->registerException($e);
        }
    }

    // }}}
}
