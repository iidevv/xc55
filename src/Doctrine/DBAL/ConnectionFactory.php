<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Doctrine\DBAL;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\Bundle\DoctrineBundle\ConnectionFactory as BaseConnectionFactory;

class ConnectionFactory extends BaseConnectionFactory
{
    public function createConnection(
        array $params,
        ?Configuration $config = null,
        ?EventManager $eventManager = null,
        array $mappingTypes = []
    ) {
        if (isset($params['url']) && parse_url($params['url']) === false) {
            preg_match('/:([^\/\/](.+))@/', $params['url'], $matches);

            if (isset($matches[1])) {
                $params['url'] = str_replace($matches[1], rawurlencode($matches[1]), $params['url']);
                $params['needPasswordDecoded'] = true;
            }
        }

        return parent::createConnection($params, $config, $eventManager, $mappingTypes);
    }
}
