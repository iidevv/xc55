<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\LifetimeHook\Upgrade;

use XCart\Doctrine\FixtureLoader;
use XLite\Core\Config;

final class UpgradeTo55010
{
    private FixtureLoader $fixtureLoader;

    public function __construct(
        FixtureLoader $fixtureLoader
    ) {
        $this->fixtureLoader  = $fixtureLoader;
    }

    public function onUpgrade(): void
    {
        $this->fixtureLoader->loadYaml(LC_DIR_ROOT . 'upgrade/5.5/0.10/upgrade.yaml');
        $this->updateConfigSmtpAuth();
    }

    private function updateConfigSmtpAuth(): void
    {
        $configRepo = \XLite\Core\Database::getRepo(\XLite\Model\Config::class);

        if ($configRepo) {
            /** @var \XLite\Model\Config|null $useSmtpAuth */
            $useSmtpAuth = $configRepo->findOneBy([
                'category' => 'Email',
                'name' => 'use_smtp_auth'
            ]);

            /** @var \XLite\Model\Config|null $smtpAuthMode */
            $smtpAuthMode = $configRepo->findOneBy([
                'category' => 'Email',
                'name' => 'smtp_auth_mode'
            ]);

            if ($useSmtpAuth) {
                if ($useSmtpAuth->getValue() && $smtpAuthMode) {
                    $smtpAuthMode->setValue('custom');
                    $configRepo->update($smtpAuthMode);
                }

                $configRepo->delete($useSmtpAuth);
                Config::updateInstance();
            }
        }
    }
}
