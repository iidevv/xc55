<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Attribute controller
 */
class Attribute extends \XLite\Controller\Admin\ACL\Catalog
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = ['target', 'id', 'product_class_id'];

    /**
     * Product class
     *
     * @var \XLite\Model\ProductClass
     */
    protected $productClass;

    /**
     * Attribute
     *
     * @var \XLite\Model\Attribute
     */
    protected $attribute;


    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess()
            && $this->isAJAX()
            && (
                $this->getProductClass()
                || !\XLite\Core\Request::getInstance()->product_class_id
            );
    }

    /**
     * Get product class
     *
     * @return \XLite\Model\ProductClass
     */
    public function getProductClass()
    {
        if (
            $this->productClass === null
            && \XLite\Core\Request::getInstance()->product_class_id
        ) {
            $this->productClass = \XLite\Core\Database::getRepo('XLite\Model\ProductClass')
                ->find((int) \XLite\Core\Request::getInstance()->product_class_id);
        }

        return $this->productClass;
    }

    /**
     * Get attribute
     *
     * @return \XLite\Model\Attribute
     */
    public function getAttribute()
    {
        if (
            $this->attribute === null
            && \XLite\Core\Request::getInstance()->id
        ) {
            $this->attribute = \XLite\Core\Database::getRepo('XLite\Model\Attribute')
                ->find((int) \XLite\Core\Request::getInstance()->id);
        }

        return $this->attribute;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $id = (int) \XLite\Core\Request::getInstance()->id;
        $model = $id
            ? \XLite\Core\Database::getRepo('XLite\Model\Attribute')->find($id)
            : null;

        return ($model && $model->getId())
            ? static::t('Edit attribute values')
            : static::t('New attribute');
    }

    /**
     * Update model
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $this->setInternalRedirect();

        $list = new \XLite\View\ItemsList\Model\AttributeOption();

        if (
            $this->getAttribute()
            && $this->getAttribute()->getType() === \XLite\Model\Attribute::TYPE_HIDDEN
        ) {
            $requestData = \XLite\Core\Request::getInstance()->getData();
            $createDataPrefix = $list->getCreateDataPrefix();
            $updateDataPrefix = $list->getDataPrefix();

            $itemsData = [];
            if (isset($requestData[$createDataPrefix])) {
                $itemsData = array_merge($itemsData, $requestData[$createDataPrefix]);
            }
            if (isset($requestData[$updateDataPrefix])) {
                $itemsData = array_merge($itemsData, $requestData[$updateDataPrefix]);
            }

            foreach ($itemsData as $itemData) {
                if (!empty($itemData['addToNew'])) {
                    \XLite\Core\Database::getRepo('XLite\Model\AttributeOption')->resetAddToNew($this->getAttribute());
                }
            }
        }

        $list->processQuick();

        if ($this->getModelForm()->performAction('modify')) {
            $existingAttributeId = $this->getModelForm()->getModelObject()->getId();
            \XLite\Core\Event::updateAttribute(['id' => $existingAttributeId]);

            $this->setReturnUrl(
                \XLite\Core\Converter::buildURL(
                    'attribute',
                    '',
                    [
                        'id'               => $existingAttributeId,
                        'product_class_id' => \XLite\Core\Request::getInstance()->product_class_id,
                        'widget'           => 'XLite\View\Attribute',
                    ]
                )
            );
        } else {
            $this->setInternalRedirect();
        }
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\View\Model\Attribute';
    }
}
