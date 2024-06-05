<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Command\Helpers;

trait ModuleTrait
{
    private $moduleHelper;

    /**
     * @param $name
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getModuleStateByName($name)
    {
        if (!$this->moduleHelper) {
            $this->moduleHelper = new Module();
        }

        return $this->moduleHelper->getModuleStateByName($name);
    }

    public function getMessageByState($state, $name)
    {
        switch ($state) {
            case Module::NOT_INSTALLED:
                $result = "Module $name is not installed";
                break;
            case Module::INSTALLED:
                $result = "Module $name is not enabled";
                break;
            case Module::ENABLED:
                $result = "Module $name. Everything is ok";
                break;
            default:
                $result = "Module $name does not exists. Please check the spelling";
                break;
        }

        return $result;
    }

    public function getModulesList()
    {
        chdir(\LC_DIR_MODULES);

        return glob('*/*');
    }

    public function getModuleData($author, $name)
    {
        $data = [];
        $data['version'] = \XLite::getInstance()->getMajorVersion() . '.0.0';
        $data['type'] = 'common';

        $data['author']      = $author;
        $data['name']        = $name;
        $data['authorName']  = $author;
        $data['moduleName']  = $name;
        $data['description'] = '';

        $data['minorRequiredCoreVersion'] = 0;

        $data['dependsOn']        = [];
        $data['incompatibleWith'] = [];
        $data['skins']            = [];
        $data['showSettingsForm'] = false;
        $data['canDisable']       = true;

        return $data;
    }

    public function saveModuleData($author, $name, $data)
    {
        $file = \LC_DIR_MODULES . "$author/$name/config/main.yaml";

        $dumper = new \Symfony\Component\Yaml\Dumper(2);

        unset($data['author'], $data['name']);
        $dump = preg_replace('#\-(\n)+[\s]*+#', '- ', $dumper->dump($data, 5));

        $dirPath = dirname($file);
        if (!\Includes\Utils\FileManager::isDirWriteable($dirPath)) {
            \Includes\Utils\FileManager::mkdirRecursive($dirPath);
        }

        return file_put_contents($file, $dump) !== false;
    }
}
