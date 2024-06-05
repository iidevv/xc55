<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace QSL\OAuth2Client\LifetimeHook;

final class Hook
{
    public function onUpgradeTo5500(): void
    {
        $this->updateProvider();
        \XLite\Core\Database::getEM()->flush();
    }

    private function updateProvider(): void
    {
        $repo = \XLite\Core\Database::getRepo(\QSL\OAuth2Client\Model\Provider::class);

        if ($repo) {
            $qb = $repo->createPureQueryBuilder('p');

            $qb
                ->update(\QSL\OAuth2Client\Model\Provider::class, 'p')
                ->set('p.class_name', "REPLACE(p.class_name, 'XLite\\Module\\', '')")
                ->where($qb->expr()->like('p.class_name', "'XLite%'"))
                ->execute();
        }
    }
}
