<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Configuration;

/**
 * Configuration
 */
class Configuration
{
    /**
     * @var int
     */
    protected $consumeCommandsInterval;

    /**
     * @var string
     */
    protected $skuVaultUrl;

    /**
     * @var string
     */
    protected $basicAuthUser;

    /**
     * @var string
     */
    protected $basicAuthPassword;

    /**
     * Constructor
     *
     * @param int $consumeCommandsInterval
     * @param string $skuVaultUrl
     * @param string $basicAuthUser
     * @param string $basicAuthPassword
     *
     * @return void
     */
    public function __construct(int $consumeCommandsInterval, string $skuVaultUrl, string $basicAuthUser, string $basicAuthPassword)
    {
        $this->consumeCommandsInterval = $consumeCommandsInterval;
        $this->skuVaultUrl = $skuVaultUrl;
        $this->basicAuthUser = $basicAuthUser;
        $this->basicAuthPassword = $basicAuthPassword;
    }

    /**
     * Return 'consume commands interval' value
     *
     * @return int
     */
    public function getConsumeCommandsInterval(): int
    {
        return $this->consumeCommandsInterval;
    }

    /**
     * Return 'skuVaultUrl' value
     *
     * @return string
     */
    public function getSkuVaultUrl(): string
    {
        return $this->skuVaultUrl;
    }

    /**
     * Return 'basic auth user' value
     *
     * @return string
     */
    public function getBasicAuthUser(): string
    {
        return $this->basicAuthUser;
    }

    /**
     * Return 'basic auth password' value
     *
     * @return string
     */
    public function getBasicAuthPassword(): string
    {
        return $this->basicAuthPassword;
    }
}