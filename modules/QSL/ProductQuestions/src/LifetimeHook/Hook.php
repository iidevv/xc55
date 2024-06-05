<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace QSL\ProductQuestions\LifetimeHook;

use XCart\Doctrine\FixtureLoader;
use XLite\Model\Config;

final class Hook
{
    private FixtureLoader $fixtureLoader;

    public function __construct(FixtureLoader $fixtureLoader)
    {
        $this->fixtureLoader = $fixtureLoader;
    }

    public function onDisable(): void
    {
        /** @var \XLite\Model\Product\GlobalTab $tab */
        $tab = \XLite\Core\Database::getRepo(\XLite\Model\Product\GlobalTab::class)
            ->findOneBy(['service_name' => 'Questions']);

        // Remove "Questions" tab when the module is disabled
        if ($tab) {
            \XLite\Core\Database::getEM()->remove($tab);
            \XLite\Core\Database::getEM()->flush();
        }
    }

    public function onRebuild(): void
    {
        /** @var \XLite\Model\Product\GlobalTab $tab */
        $tab = \XLite\Core\Database::getRepo(\XLite\Model\Product\GlobalTab::class)
            ->findOneBy(['service_name' => 'Questions']);

        // Make sure "Questions" tab is available and create new one if it is not
        if (!$tab) {
            $questionsTab = new \XLite\Model\Product\GlobalTab();
            \XLite\Core\Database::getEM()->persist($questionsTab);

            $questionsTab->setServiceName('Questions');
            $questionsTab->setName('Questions');
            $questionsTab->setPosition(50);

            $providerCode = 'QSL\\ProductQuestions';
            /** @var \XLite\Model\Product\GlobalTab $entity */
            if (!$questionsTab->getProviderByCode($providerCode)) {
                /** @var \XLite\Model\Product\GlobalTabProvider $provider */
                $provider = new \XLite\Model\Product\GlobalTabProvider();
                \XLite\Core\Database::getEM()->persist($provider);

                $provider->setTab($questionsTab);
                $provider->setCode($providerCode);
                $questionsTab->addProvider($provider);
            }

            \XLite\Core\Database::getEM()->flush();

            $repo = \XLite\Core\Database::getRepo('XLite\Model\Product\GlobalTab');
            if ($repo && method_exists($repo, 'createGlobalTabAliases')) {
                // refresh the model
                \XLite\Core\Database::getRepo('XLite\Model\Product\GlobalTab')
                    ->createGlobalTabAliases($questionsTab);

                \XLite\Core\Database::getEM()->flush();
            }
        }
    }

    public function onUpgradeTo5502(): void
    {
        $this->fixtureLoader->loadYaml(LC_DIR_MODULES . 'QSL/ProductQuestions/resources/hooks/upgrade/5.5/0.2/upgrade.yaml');
    }

    public function onUpgradeTo5503(): void
    {
        /** @var \Xlite\Model\Repo\Config $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Config');

        /** @var Config|null $config */
        $config = $repo->findOneBy(
            [
                'name' => 'product_questions_admin_email',
                'category' => 'Company'
            ]
        );

        if ($config) {
            $config->setOrderby(49005);
            $config->setOptionName('Product questions');
            $repo->update($config);
        }
    }
}
