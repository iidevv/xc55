<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Configuration;

use Qualiteam\SkinActKlarna\Traits\KlarnaTrait;

class Configuration
{
    use KlarnaTrait;

    /**
     * @var string
     */
    protected string $username;

    /**
     * @var string
     */
    protected string $password;

    /**
     * @var bool
     */
    protected bool   $isEnabled;

    /**
     * @var string
     */
    protected string $mode;

    /**
     * @var string
     */
    protected string $url;

    /**
     * @var string
     */
    protected string $currency;

    /**
     * @var int
     */
    protected int    $methodId;

    /**
     * @param string $username
     * @param string $password
     * @param bool   $isEnabled
     * @param string $mode
     * @param string $url
     * @param string $currency
     * @param int    $methodId
     */
    public function __construct(string $username, string $password, bool $isEnabled, string $mode, string $url, string $currency, int $methodId)
    {
        $this->username = $username;
        $this->password = $password;
        $this->isEnabled = $isEnabled;
        $this->mode = $mode;
        $this->url = $url;
        $this->currency = $currency;
        $this->methodId = $methodId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getMethodId(): int
    {
        return $this->methodId;
    }
}