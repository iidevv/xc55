<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XC\ThemeTweaker\LifetimeHook;

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
        $this->fixtureLoader->loadYaml(LC_DIR_MODULES . 'XC/ThemeTweaker/resources/hooks/upgrade/5.5/0.0/upgrade.yaml');
        $this->removeFlexyLabels();
        $this->fillTemplatesRemoveFiles();
    }

    protected function removeFlexyLabels()
    {
        $deleteLabels = [
            'Flexy to twig converter',
            'Flexy-template',
            'Search flexy-templates',
            'Remove flexy-templates',
            'No flexy-templates found.',
            'Flexy templates have been removed',
            'Some flexy-templates cannot be removed. Please correct file permissions or remove them manually',
            'This action will remove all flexy-templates. Are you sure to proceed?',
            'Cannot get flexy content',
            'Failure to convert flexy-template. Check for syntax errors',
            'Flexy-to-twig converter warning'
        ];

        $labels = \XLite\Core\Database::getRepo('XLite\Model\LanguageLabel')
            ->findBy(['name' => $deleteLabels]);

        $em = \XLite\Core\Database::getEM();
        foreach ($labels as $entity) {
            $em->remove($entity);
        }

        $em->flush();
        \XLite\Core\Translation::getInstance()->reset();
    }

    protected function fillTemplatesRemoveFiles()
    {
        $migrated = [];
        $em = \XLite\Core\Database::getEM();
        $list = \XLite\Core\Database::getRepo('\XC\ThemeTweaker\Model\Template')->findAll();

        foreach ($list as $template) {
            $oldName = $path = $template->getTemplate();
            if (!$template->getEnabled()) {
                $path .= '.tmp';
            }

            $value = \Includes\Utils\FileManager::read(\LC_DIR_SKINS . $path);
            if ($value && strpos($oldName, 'theme_tweaker/') !== false) {
                $templateName = str_replace('theme_tweaker/', 'web/', $oldName);
                $template->setBody($value);
                $template->setTemplate($templateName);
                $template->setEnabled(false);

                $em->flush();

                $migrated[$templateName] = $path;
            }
        }

        $migratedList = \XLite\Core\Database::getRepo('\XC\ThemeTweaker\Model\Template')->findAll();
        foreach ($migratedList as $template) {
            $newName = $template->getTemplate();
            if (array_key_exists($newName, $migrated)) {
                $path = \LC_DIR_SKINS . $migrated[$newName];
                if (\Includes\Utils\FileManager::isExists($path)) {
                    \Includes\Utils\FileManager::deleteFile($path);
                }
            }
        }

        $em->flush();
    }

    public function onUpgradeTo5505(): void
    {
        $yamlFile = LC_DIR_MODULES . 'XC/ThemeTweaker/resources/hooks/upgrade/5.5/0.5/upgrade.yaml';

        if (\Includes\Utils\FileManager::isFileReadable($yamlFile)) {
            \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
            \XLite\Core\Database::getEM()->flush();
        }
    }
}
