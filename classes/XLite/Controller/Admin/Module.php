<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XCart\Domain\ModuleManagerDomain;

/**
 * Module settings
 */
class Module extends \XLite\Controller\Admin\AAdmin
{
    /**
     * @var mixed
     */
    protected $module;

    private ModuleManagerDomain $moduleManagerDomain;

    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->moduleManagerDomain = \XCart\Container::getContainer()->get(ModuleManagerDomain::class);
    }

    /**
     * @param array $classes Classes
     *
     * @return array
     */
    public function defineBodyClasses(array $classes)
    {
        $classes = parent::defineBodyClasses($classes);

        [$author, $name] = \Includes\Utils\Module\Module::explodeModuleId($this->getModule());

        if ($author && $name) {
            $classes[] = strtolower('module-' . $author . '-' . $name);
        }

        return $classes;
    }

    public function handleRequest()
    {
        $module = $this->getModule();

        if (!$module) {
            $this->setReturnURL($this->buildURL(\XLite::TARGET_404));
        } else {
            $showSettingsForm = $this->moduleManagerDomain->getModule($module)['showSettingsForm'];

            $settingsUrl = \Includes\Utils\Module\Module::callMainClassMethod($module, 'getSettingsForm');
            parse_str(parse_url($settingsUrl, PHP_URL_QUERY), $settingsUrlParams);

            if (
                $showSettingsForm
                && $settingsUrl
                && (
                    empty($settingsUrlParams['target'])
                    || $settingsUrlParams['target'] !== $this->getTarget()
                )
            ) {
                $this->setReturnURL($settingsUrl);
            }
        }

        parent::handleRequest();
    }

    /**
     * Return current module options
     *
     * @return array
     */
    public function getOptions()
    {
        [$author, $name] = \Includes\Utils\Module\Module::explodeModuleId($this->getModule());

        return \XLite\Core\Database::getRepo('XLite\Model\Config')
            ->findByCategoryAndVisible($author . '\\' . $name);
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     * @todo: use readable name
     */
    public function getTitle()
    {
        $module = $this->getModule();

        [$moduleAuthor, $moduleName] = \Includes\Utils\Module\Module::explodeModuleId($module);

        $customHeading = \XLite\Core\Database::getRepo('\XLite\Model\Config')
            ->findOneBy([
                'category' => $moduleAuthor . '\\' . $moduleName,
                'name'     => 'settings_page_heading',
            ]);

        if ($customHeading) {
            $customHeadingTranslation = $customHeading->getTranslation(\XLite\Core\Session::getInstance()->getLanguage()->getCode())->getOptionName();

            if ($customHeadingTranslation) {
                return $customHeadingTranslation;
            }
        }

        return static::t(
            'X module settings',
            [
                'name' => $this->moduleManagerDomain->getModule($module)['moduleName'],
            ]
        );
    }

    /**
     * Return current module object
     *
     * @return string
     */
    public function getModule()
    {
        if ($this->module === null) {
            $this->module = $this->moduleManagerDomain->isEnabled($this->getModuleId())
                ? $this->getModuleId()
                : '';
        }

        return $this->module;
    }

    /**
     * Get current module ID
     *
     * @return integer
     */
    protected function getModuleId()
    {
        return \XLite\Core\Request::getInstance()->moduleId;
    }

    /**
     * Update module settings
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $this->getModelForm()->performAction('update');
    }

    /**
     * getModelFormClass
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\View\Model\ModuleSettings';
    }
}
