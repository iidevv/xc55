<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActShipStationAdvanced\LifetimeHook;

use App\Entity\Module;
use Symfony\Component\Yaml\Yaml;
use XCart\Command\Helpers\ModuleTrait;
use XCart\Doctrine\FixtureLoader;
use XLite\Core\Config;
use XLite\Core\Database;

final class Hook
{
    use ModuleTrait;

    private FixtureLoader $fixtureLoader;

    public function __construct(FixtureLoader $fixtureLoader)
    {
        $this->fixtureLoader = $fixtureLoader;
    }

    public function onDisable(): void
    {
        $this->fixShipStationYaml();
        $this->fixShipStationSQL();
    }

    public function onEnable(): void
    {
        $this->fixShipStationYaml(true);
        $this->fixShipStationSQL(true);
    }

    protected function fixShipStationYaml(bool $isEnable = false): void
    {
        $file = \LC_DIR_MODULES . "ShipStation/Api/config/main.yaml";
        $yaml = Yaml::parseFile($file);
        $yaml['showSettingsForm'] = $isEnable;

        $this->saveModuleData('ShipStation', 'Api', $yaml);
    }

    protected function fixShipStationSQL(bool $isEnabled = false): void
    {
        $conn = Database::getEM()->getConnection();
        $sql = "SELECT meta_data FROM service_module WHERE module_id = 'ShipStation-Api'";
        $result = $conn->fetchAssociative($sql);

        $meta = json_decode($result['meta_data'], true);
        $meta['showSettingsForm'] = $isEnabled;
        $meta = json_encode($meta);

        $sql = "UPDATE service_module SET meta_data = ? WHERE module_id = 'ShipStation-Api'";
        $conn->executeQuery($sql, [$meta]);
    }
}
