<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Concierge\Core;

abstract class AMessage
{
    public const TYPE_IDENTIFY = 'identify';
    public const TYPE_TRACK    = 'track';
    public const TYPE_PAGE     = 'page';
    public const TYPE_SCREEN   = 'screen';
    public const TYPE_GROUP    = 'group';
    public const TYPE_ALIAS    = 'alias';
    public const TYPE_RESET    = 'reset';

    protected $integrations;

    /**
     * @return string
     */
    abstract public function getType();

    /**
     * @return array
     */
    abstract public function getArguments();

    /**
     * @param string $integration
     *
     * @return array
     */
    public function toArray($integration = '')
    {
        return [
            'type'      => $this->getType(),
            'arguments' => $this->getArguments(),
        ];
    }

    /**
     * @return mixed
     */
    public function getIntegrations()
    {
        return $this->integrations;
    }

    /**
     * @param mixed $integrations
     */
    public function setIntegrations($integrations)
    {
        $this->integrations = $integrations;
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        $result = Mediator::getInstance()->getOptions();

        $integrations = $this->getIntegrations();
        if ($integrations) {
            $result['integrations'] = $integrations;
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getSubscriptionType()
    {
        return 'none';
    }
}
