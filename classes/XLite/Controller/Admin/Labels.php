<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Core\Database;
use XLite\Core\Translation;
use XLite\Model\LanguageLabelTranslation;

/**
 * Language labels controller
 */
class Labels extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     * FIXME: to remove
     *
     * @var string
     */
    protected $params = ['target', 'code', 'section'];

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), ['searchItemsList']);
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return \XLite\Core\Request::getInstance()->section === 'store'
            ? static::t('Localization')
            : static::t('Customization');
    }

    /**
     * Get return URL
     *
     * @return string
     */
    public function getReturnURL()
    {
        $request = \XLite\Core\Request::getInstance();
        if ($request->action && $request->code && $request->section) {
            $url = $this->buildURL('labels', '', [
                'code'    => $request->code,
                'section' => $request->section
            ]);
        } else {
            $url = parent::getReturnURL();
        }

        return $url;
    }

    /**
     * Get all active languages
     *
     * @return array
     */
    public function getLanguages()
    {
        return Database::getRepo('\XLite\Model\Language')->findAddedLanguages();
    }

    /**
     * Get current language code
     *
     * @return string
     */
    public function getCode()
    {
        return \XLite\Core\Request::getInstance()->code ?: parent::getDefaultLanguage();
    }

    /**
     * Get current section
     *
     * @return string
     */
    public function getSection()
    {
        return \XLite\Core\Request::getInstance()->section ?: '';
    }

    /**
     * Update labels
     *
     * @return void
     */
    protected function doActionUpdateItemsList()
    {
        // Update 'enabled' and 'added' properties editable in the item list
        parent::doActionUpdateItemsList();

        $requestData = \XLite\Core\Request::getInstance()->getPostData(false);

        $current = !empty($requestData['current']) ? $requestData['current'] : null;

        // Edit labels for current language
        if ($current && is_array($current)) {
            $this->saveLabels(
                $current,
                static::getDefaultLanguage()
            );
        }
        unset($current);

        $translated    = !empty($requestData['translated']) ? $requestData['translated'] : null;
        $translateFail = false;
        if ($translated && is_array($translated)) {
            $language = \XLite\Core\Request::getInstance()->code;

            if (!$language) {
                \XLite\Core\TopMessage::addWarning(
                    'Text labels have not been updated successfully: the translation language has not been specified'
                );
                $translateFail = true;
            } elseif (!Database::getRepo('\XLite\Model\Language')->findOneByCode($language)) {
                \XLite\Core\TopMessage::addWarning(
                    'Text labels have not been updated successfully: the translation language has not been found'
                );
                $translateFail = true;
            } else {
                $this->saveLabels(
                    $translated,
                    $language
                );
            }
        }
        unset($translated);

        if (!$translateFail) {
            \XLite\Core\TopMessage::addInfo('Text labels have been updated successfully');
        }
    }

    /**
     * Add label
     *
     * @return void
     */
    protected function doActionAdd()
    {
        $requestData = \XLite\Core\Request::getInstance()->getPostData(false);
        $entityManager = Database::getEM();

        $name  = substr($requestData['name'], 0, 255);
        $label = $requestData['label'];

        if (!$name) {
            $this->valid = false;
            \XLite\Core\TopMessage::addError(
                'The text label has not been added, because its name has not been specified'
            );
        } elseif (Database::getRepo('\XLite\Model\LanguageLabel')->findOneByName($name)) {
            $this->valid = false;
            \XLite\Core\TopMessage::addError(
                'The text label has not been added, because such a text label already exists'
            );
        } else {
            $lbl = new \XLite\Model\LanguageLabel();
            $lbl->setName($name);
            $entityManager->persist($lbl);

            foreach ($label as $code => $text) {
                if (!empty($text)) {
                    $translation = new LanguageLabelTranslation();
                    $translation->setCode($code);
                    $translation->setOwner($lbl);
                    $translation->setLabel($text);
                    $entityManager->persist($translation);
                }
            }

            $entityManager->flush();

            if ($lbl && $lbl->getLabelId()) {
                // Save added label ID in session
                $addedLabels = \XLite\Core\Session::getInstance()->added_labels;

                if (is_array($addedLabels)) {
                    array_push($addedLabels, $lbl->getLabelId());
                } else {
                    $addedLabels = [$lbl->getLabelId()];
                }

                \XLite\Core\Session::getInstance()->added_labels = $addedLabels;
                $this->setHardRedirect();
            }

            Translation::getInstance()->reset();

            \XLite\Core\TopMessage::addInfo('The text label has been added successfully');
        }
    }

    /**
     * Edit label
     *
     * @return void
     */
    protected function doActionEdit()
    {
        $requestData = \XLite\Core\Request::getInstance()->getPostData(false);

        $label   = $requestData['label'];
        $labelId = intval(\XLite\Core\Request::getInstance()->label_id);
        $labelName = $requestData['label_name'] ?? null;

        $repo = Database::getRepo('XLite\Model\LanguageLabel');
        /** @var \XLite\Model\LanguageLabel $lbl */
        $lbl = $repo->find($labelId)
            ?: $repo->findOneBy(['name' => $labelName]);

        if (!$lbl) {
            \XLite\Core\TopMessage::addError('The edited language has not been found');
        } else {
            $list = [];

            foreach ($label as $code => $text) {
                if (!empty($text)) {
                    $translation = $lbl->getTranslation($code);
                    $translation->setLabel($text);

                    $list['update'][] = $translation;
                } elseif ($lbl->hasTranslation($code)) {
                    $list['delete'][] = $lbl->getTranslation($code);
                }
            }

            $repo->insertInBatch($list['update'] ?? []);
            $repo->deleteInBatch($list['delete'] ?? []);
            Translation::getInstance()->reset();

            $this->onEditSuccess($lbl);
        }
    }

    /**
     * Save labels from array
     *
     * @param array  $values Array
     * @param string $code   Language code
     *
     * @return void
     */
    protected function saveLabels(array $values, $code)
    {
        $repo = Database::getRepo('\XLite\Model\LanguageLabel');
        $entityManager = Database::getEM();
        $labels = $repo->findByIds(
            array_keys($values)
        );

        foreach ($labels as $label) {
            $newValue = $values[$label->getLabelId()];
            $translation = $label->getTranslation($code);
            if ($newValue === '') {
                if ($translation) {
                    $entityManager->remove($translation);
                }
            } else {
                if (!$translation) {
                    $translation = new LanguageLabelTranslation();
                    $translation->setCode($code);
                    $label->addTranslations($translation);
                }
                $translation->setLabel($newValue);
                $entityManager->persist($translation);
            }
        }
        $entityManager->flush();

        \XLite\Core\Translation::getInstance()->reset();
    }

    protected function doNoAction()
    {
        $sessionCellName = \XLite\View\ItemsList\Model\Translation\Labels::getSearchSessionCellName();
        $sessionCell     = \XLite\Core\Session::getInstance()->{$sessionCellName};

        if ($sessionCell && \XLite\Core\Request::getInstance()->substring) {
            $sessionCell['substring'] = \XLite\Core\Request::getInstance()->substring;

            \XLite\Core\Session::getInstance()->{$sessionCellName} = $sessionCell;
        }

        parent::doNoAction();
    }

    /**
     * Is called when doActionEdit() has been performed successfully; sends the appropriate message to the user
     *
     * @param \XLite\Model\LanguageLabel $lbl Edited label entity
     */
    protected function onEditSuccess($lbl)
    {
        \XLite\Core\TopMessage::addInfo('The text label has been modified successfully');

        $this->setSilenceClose();
    }
}
