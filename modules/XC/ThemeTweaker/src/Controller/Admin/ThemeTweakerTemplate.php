<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Controller\Admin;

use Twig\Environment;
use Twig\Source;
use XC\ThemeTweaker\Core\TemplateObjectProvider;
use XCart\Container;
use XCart\Operation\Service\ViewListRefresh;
use XLite\Core\Database;
use XLite\Core\Event;
use XLite\Core\Request;
use XLite\Core\Translation;

/**
 * Theme tweaker template controller
 */
class ThemeTweakerTemplate extends \XLite\Controller\Admin\AAdmin
{
    public const MAX_FILENAME_LENGTH = 255;

    public function __construct(array $params)
    {
        parent::__construct($params);

        if (Request::getInstance()->fromCustomer) {
            $this->hostRedirect = false;
        }

        $this->params = array_merge($this->params, ['id', 'template']);
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getTemplateLocalPath();
    }

    /**
     * Is create request
     *
     * @return boolean
     */
    public function isCreate()
    {
        return (bool) Request::getInstance()->template;
    }

    /**
     * Update model
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        if ($this->getModelForm()->performAction('modify')) {
            if (Request::getInstance()->isCreate) {
                echo <<<HTML
<script>window.opener.dispatchEvent(new Event('reload'));window.opener.location.reload();window.close()</script>
HTML;
                exit;
            }

            $viewListRefresh = \XCart\Container::getContainer()->get(ViewListRefresh::class);
            ($viewListRefresh)();

            $cacheDriver = \XLite\Core\Cache::getInstance()->getDriver();
            $cacheDriver->deleteAll();

            $this->reloadTwigCache($this->getTemplateLocalPath());
        }
    }

    /**
     * Update model
     *
     * @return void
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function doActionApplyChanges()
    {
        $cacheDriver = \XLite\Core\Cache::getInstance()->getDriver();
        $provider    = $this->getTemplateObjectProvider();
        $rawData     = Request::getInstance()->getPostData(false);
        $content     = $rawData['content'] ?? null;
        $weight      = \XLite\Core\Request::getInstance()->weight;
        $list        = \XLite\Core\Request::getInstance()->list;
        $pendingId   = \XLite\Core\Request::getInstance()->pendingId;
        $interface   = Request::getInstance()->interface;
        $zone        = Request::getInstance()->zone;

        if ($provider->getTemplatePath()) {
            $result = $this->updateEditedTemplate(
                $provider,
                $content,
                $interface,
                $zone
            );

            if ($result) {
                if ($weight && $list) {
                    if ($pendingId) {
                        $this->updateListChild($provider->getTemplatePath(), $list, $weight);
                    } else {
                        $this->addListChild($provider->getTemplatePath(), $list, $weight);
                    }
                }

                $cacheDriver->delete(\XC\ThemeTweaker\Core\Layout::THEME_TWEAKER_TEMPLATES_CACHE_KEY);
            }
        }

        $reverted = Request::getInstance()->reverted;

        if ($reverted) {
            $templates = [];
            foreach ($reverted as $template) {
                $layout      = \XLite\Core\Layout::getInstance();
                $templates[] = $layout->getTweakerPathByLocalPath($template, $interface, $zone);
            }

            Database::getRepo('XC\ThemeTweaker\Model\Template')->disableTemplates($templates);
            $viewListRefresh = \XCart\Container::getContainer()->get(ViewListRefresh::class);
            ($viewListRefresh)();

            $cacheDriver = \XLite\Core\Cache::getInstance()->getDriver();
            $cacheDriver->deleteAll();
        }

        Database::getEM()->flush();

        $this->reloadTwigCache($provider->getTemplatePath());

        $this->translateTopMessagesToHTTPHeaders();
        Event::getInstance()->display();
        Event::getInstance()->clear();
        $this->set('silent', true);
    }

    /**
     * @param $templatePath
     *
     * @return bool
     *
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected function reloadTwigCache($templatePath)
    {
        $twig = Container::getServiceLocator()->getTwig();

        // Required to overwrite the modified template cache
        $twig->enableAutoReload();

        [, , $name] = explode('/', $templatePath, 3);
        try {
            \XLite\Core\Layout::getInstance()
                ->callInInterfaceZone(static fn () => $twig->load($name));
        } catch (\Throwable $error) {
            return false;
        } finally {
            // After overwriting, return to the original state
            $twig->disableAutoReload();
        }

        return true;
    }

    /**
     * @return TemplateObjectProvider
     */
    protected function getTemplateObjectProvider()
    {
        return TemplateObjectProvider::getInstance();
    }

    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * Tries to persist content changes into template entity
     *
     * @param TemplateObjectProvider $provider
     * @param string                 $content
     * @param string                 $interface
     * @param string                 $zone
     *
     * @return bool
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function updateEditedTemplate($provider, $content, $interface = \XLite::INTERFACE_WEB, $zone = \XLite::ZONE_CUSTOMER)
    {
        $templatePath = $provider->getTemplatePath();
        $entity       = $provider->getTemplateObject();

        if ($error = $this->validateTemplate($content, $templatePath)) {
            $this->throwError(
                $error['message'],
                400,
                $error['lineno']
            );

            return false;
        }

        /** @var \XC\ThemeTweaker\Core\Layout $layout */
        $layout = \XLite\Core\Layout::getInstance();

        if ($interface === \XLite::INTERFACE_MAIL) {
            $layout->setMailSkin($zone);
        }

        if (strlen($templatePath) > static::MAX_FILENAME_LENGTH) {
            $this->throwError(
                Translation::lbl('File name is too long, it should be less than 255 characters'),
                400
            );

            return false;
        }

        $templatePath = $layout->getTweakerPathByLocalPath($templatePath, $interface, $zone);

        if (!$entity->isPersistent()) {
            $this->removePossibleDuplicates($templatePath);
        }

        $entity->setDate(LC_START_TIME);
        $entity->setTemplate($templatePath);
        $entity->setBody($content);
        $entity->setEnabled(true);

        Database::getEM()->persist($entity);
        Database::getEM()->flush();

        return true;
    }

    /**
     * @param string $message
     * @param int    $code
     * @param int    $lineNo
     */
    protected function throwError($message, $code = 400, $lineNo = null)
    {
        $params = [
            'message' => $message,
        ];

        if ($lineNo) {
            $params['line'] = $lineNo;
        }

        $this->headerStatus($code);
        Event::getInstance()->trigger('themetweaker.error', $params);
    }

    /**
     * Add list child record when new template is added via editor
     *
     * @param $templatePath
     * @param $list
     * @param $weight
     */
    protected function addListChild($templatePath, $list, $weight)
    {
        $relativePath = str_replace(
            \XLite::INTERFACE_WEB . LC_DS . \XLite::ZONE_CUSTOMER . LC_DS,
            '',
            $templatePath
        );

        \XLite\Core\Database::getRepo('XLite\Model\ViewList')->insert(new \XLite\Model\ViewList([
            'tpl'             => $relativePath,
            'list'            => $list,
            'interface'       => \XLite\Model\ViewList::INTERFACE_WEB,
            'zone'            => \XLite\Model\ViewList::ZONE_CUSTOMER,
            'weight'          => $weight,
            'weight_override' => $weight,
        ]));

        $this->removeListCache($list);
    }

    /**
     * Add list child record when new template is added via editor
     *
     * @param $list
     */
    protected function removeListCache($list)
    {
        \XLite\Core\Database::getRepo('\XLite\Model\ViewList')->deleteCacheByNameAndParams(
            'class_list',
            [
                'list'      => $list,
                'interface' => \XLite\Model\ViewList::INTERFACE_WEB,
                'zone'      => \XLite\Model\ViewList::ZONE_CUSTOMER,
            ]
        );
    }

    /**
     * Add list child record when new template is added via editor
     *
     * @param $templatePath
     * @param $list
     * @param $weight
     */
    protected function updateListChild($templatePath, $list, $weight)
    {
        $relativePath = str_replace(
            \XLite::INTERFACE_WEB . LC_DS . \XLite::ZONE_CUSTOMER . LC_DS,
            '',
            $templatePath
        );

        $entity = Database::getRepo('XLite\Model\ViewList')->findEqualByData([
            'tpl'  => $relativePath,
            'list' => $list,
        ]);

        if ($entity) {
            $entity->setWeight($weight);
        }
    }

    /**
     * @param $fullPath
     */
    protected function removePossibleDuplicates($fullPath)
    {
        Database::getRepo('XC\ThemeTweaker\Model\Template')->deleteByPath($fullPath);
    }

    /**
     * Validates the template syntax and returns the array of errors or null value if the syntax is fine.
     *
     * @param $content
     * @param $identifier
     *
     * @return array|null
     */
    public function validateTemplate($content, $identifier)
    {
        /** @var Environment $twig */
        $twig = \XCart\Container::getContainer()->get('twig');

        try {
            $twig->parse($twig->tokenize(new Source($content, $identifier)));
        } catch (\Exception $e) {
            return [
                'message' => $e->getMessage(),
                'lineno'  => $e->getTemplateLine(),
            ];
        }

        return null;
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XC\ThemeTweaker\View\Model\Template';
    }

    /**
     * Returns current template short path
     *
     * @return string
     */
    public function getTemplateLocalPath()
    {
        $localPath = '';

        if ($this->isCreate()) {
            $localPath = Request::getInstance()->template;
        } elseif (Request::getInstance()->id) {
            $template = Database::getRepo('XC\ThemeTweaker\Model\Template')
                ->find(Request::getInstance()->id);

            $localPath = $template ? $template->getTemplate() : '';
        }

        return $localPath;
    }
}
