<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\SimpleCMS")
 */
class BannerWithSimpleCMS extends \QSL\Banner\View\Model\Banner
{
    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionDefault()
    {
        $pages = [
            'pages' => [
                self::SCHEMA_CLASS    => 'QSL\Banner\View\FormField\Select\Pages',
                self::SCHEMA_LABEL    => 'Static pages',
                self::SCHEMA_REQUIRED => false,
            ]
        ];

        //insert after 5th key for like a "group" with category/product pages
        $this->schemaDefault = array_merge(
            array_slice($this->schemaDefault, 0, 5),
            $pages,
            array_slice($this->schemaDefault, 5)
        );

        return $this->getFieldsBySchema($this->schemaDefault);
    }

    protected function setModelProperties(array $data)
    {
        parent::setModelProperties($data);

        /** @var \QSL\Banner\Model\Banner $entity */
        $model = $this->getModelObject();
        $pages = $data['pages'] ?? null;

        foreach ($model->getPages() as $p) {
            $p->getBanners()->removeElement($model);
        }
        //$model->getPages()->clear();
        $model->clearPages();

        if (is_array($pages)) {
            foreach ($pages as $id) {
                $p = \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Page')->find($id);
                if ($p) {
                    $model->addPages($p);
                    $p->addBanners($model);
                }
            }
        }
    }
}
