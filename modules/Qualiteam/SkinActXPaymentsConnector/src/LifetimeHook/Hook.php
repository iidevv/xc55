<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActXPaymentsConnector\LifetimeHook;

use Includes\Utils\FileManager;
use Symfony\Component\Yaml\Yaml;
use XLite\Core\Database;

final class Hook
{
    public function onEnableOrInstall()
    {
        $data = \XLite\Core\Marketplace::getInstance()->getPaymentMethods();

        if (!empty($data) && is_array($data)) {

            foreach ($data as $key => $item) {

                if (
                    'XPay_XPaymentsCloud' == $item['moduleName']
                    && 'XPaymentsCloud' != $item['service_name']
                ) {

                    $data[$key]['moduleName'] = 'Qualiteam_SkinActXPaymentsConnector';

                    if ('SavedCard' == $item['service_name']) {
                        $data[$key]['class'] = 'Qualiteam\\SkinActXPaymentsConnector\\Model\\Payment\\Processor\\SavedCard';
                        $data[$key]['added'] = true;
                        $data[$key]['enabled'] = true;
                    } else {
                        $data[$key]['class'] = 'Qualiteam\\SkinActXPaymentsConnector\\Model\\Payment\\Processor\\XPayments';
                    }
                } else {
                    unset($data[$key]);
                }
            }

            $data = array_values($data);

            $yaml = Yaml::dump(['XLite\\Model\\Payment\\Method' => $data]);

            $yamlFile = LC_DIR_TMP . 'pm_xpc.yaml';

            FileManager::write(LC_DIR_TMP . 'pm_xpc.yaml', $yaml);

            Database::getInstance()->loadFixturesFromYaml($yamlFile);
        }
    }
}
