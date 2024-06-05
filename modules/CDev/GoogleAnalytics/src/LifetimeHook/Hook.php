<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\LifetimeHook;

use CDev\GoogleAnalytics\Core\Base;
use CDev\GoogleAnalytics\Core\GA;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Contracts\EventDispatcher\Event;
use XLite\Core\Database;
use XLite\Model\Config;
use XCart\Doctrine\FixtureLoader;

final class Hook
{
    private FixtureLoader $fixtureLoader;

    public function __construct(FixtureLoader $fixtureLoader)
    {
        $this->fixtureLoader = $fixtureLoader;
    }

    public function onInit(Event $event): void
    {
        if ($event instanceof ConsoleEvent) {
            return;
        }

        Base::addCDevGASingleton();
    }

    public function onUpgradeTo5510(): void
    {
        $needResetCodeVersionToUniversal = $this->needResetCodeVersionToUniversal();
        $needValues = !$this->hasMeasurementId();

        $this->loadYaml('upgrade', '5.5', '1.0');

        if ($needValues) {
            $this->loadYaml('values', '5.5', '1.0');
        }

        if ($needResetCodeVersionToUniversal) {
            $this->setCodeVersion(GA::CODE_VERSION_U);
        }
    }

    protected function needResetCodeVersionToUniversal(): bool
    {
        $codeVersionOption = $this->getConfig()->findOneBy([
            'category' => 'CDev\GoogleAnalytics',
            'name'     => 'ga_code_version',
        ]);

        return $codeVersionOption && $codeVersionOption->getValue() !== GA::CODE_VERSION_4;
    }

    protected function hasMeasurementId(): bool
    {
        $codeVersionOption = $this->getConfig()->findOneBy([
            'category' => 'CDev\GoogleAnalytics',
            'name'     => 'ga_measurement_id',
        ]);

        return (bool)$codeVersionOption;
    }

    protected function setCodeVersion($value): void
    {
        $this->getConfig()->createOption(
            [
                'category' => 'CDev\GoogleAnalytics',
                'name'     => 'ga_code_version',
                'value'    => $value,
            ]
        );
    }

    protected function getConfig(): \XLite\Model\Repo\Config
    {
        return Database::getRepo(Config::class);
    }

    protected function loadYaml(string $name, string $major, string $minor): void
    {
        $this->fixtureLoader->loadYaml(LC_DIR_MODULES . "CDev/GoogleAnalytics/resources/hooks/upgrade/$major/$minor/$name.yaml");
    }
}
