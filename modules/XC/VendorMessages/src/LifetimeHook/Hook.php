<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XC\VendorMessages\LifetimeHook;

use XCart\Doctrine\FixtureLoader;

final class Hook
{
    private FixtureLoader $fixtureLoader;

    public function __construct(FixtureLoader $fixtureLoader)
    {
        $this->fixtureLoader = $fixtureLoader;
    }

    public function onUpgradeTo5500(): void
    {
        $this->fixtureLoader->loadYaml(LC_DIR_MODULES . 'XC/VendorMessages/resources/hooks/upgrade/5.5/0.0/upgrade.yaml');
    }

    public function onUpgradeTo5502(): void
    {
        $notificationsToChange = [
            'modules/XC/VendorMessages/notification' => [
                'oldSubject' => 'Order #%order_number%: new message from seller',
                'newSubject' => 'Order #%order_number%: new message',
                'descriptionText' => 'This message will be sent when a new message appears in the communication thread regarding an order'
            ],
        ];

        foreach ($notificationsToChange as $id => $data) {
            /** @var \XLite\Model\Notification $notification */
            $notification = \XLite\Core\Database::getRepo('XLite\Model\Notification')->find($id);

            if ($notification && ($translation = $notification->getTranslation('en'))) {
                if ($translation->getCustomerSubject() === $data['oldSubject']) {
                    $translation->setCustomerSubject($data['newSubject']);
                }

                $translation->setDescription($data['descriptionText']);
            }

            \XLite\Core\Database::getEM()->flush();
        }
    }
}
