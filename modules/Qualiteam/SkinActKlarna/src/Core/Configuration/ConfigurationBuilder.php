<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Configuration;

use Includes\Utils\Converter;
use Qualiteam\SkinActKlarna\Traits\KlarnaTrait;
use XCart\Container;
use XLite\Model\Payment\Method;

class ConfigurationBuilder
{
    use KlarnaTrait;

    /**
     * @var ?Method
     */
    protected ?Method $rawConfiguration;

    /**
     * Constructor
     *
     * @param Method|null $rawConfiguration
     *
     * @return void
     */
    public function __construct(?Method $rawConfiguration)
    {
        $this->rawConfiguration = $rawConfiguration;
    }

    /**
     * Build
     *
     * @return Configuration
     */
    public function build(): Configuration
    {
        return $this->rawConfiguration ? new Configuration(
            (string) $this->rawConfiguration->getSetting('username'),
            (string) $this->rawConfiguration->getSetting('password'),
            (bool) $this->rawConfiguration->isEnabled(),
            (string) $this->getMode(),
            (string) $this->getUrl(),
            (string) $this->rawConfiguration->getSetting('currency'),
            (int) $this->rawConfiguration->getMethodId(),
        ) : new Configuration(
            '',
            '',
            false,
            '',
            '',
            '',
            0
        );
    }

    public function getMode(): string
    {
        $method = 'get' . Converter::convertToUpperCamelCase($this->rawConfiguration->getSetting('mode')) . 'ModeName';
        return $this->{$method}();
    }

    public function getUrl(): string
    {
        $container = 'klarna.api.' . $this->rawConfiguration->getSetting('mode');
        return Container::getContainer()->getParameter($container);
    }
}