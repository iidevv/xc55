<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMagicImages\Controller\Admin;

use Doctrine\Common\Collections\Collection;
use Qualiteam\SkinActMagicImages\Model\MagicSwatchesSet as MagicSwatchesSetModel;
use XLite\Controller\Admin\AAdmin;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Core\TopMessage;
use XLite\Model\AEntity;
use XLite\Model\AttributeValue\AttributeValueSelect;
use XLite\Model\Product;

class MagicSwatchesSet extends AAdmin
{
    /**
     * Whether multi rows are used for this product
     *
     * @return bool|null
     */
    public function isMultiRowsEnabled(): ?bool
    {
        static $isMultiRowsEnabled = null;
        if ($isMultiRowsEnabled === null) {
            $isMultiRowsEnabled = false;
            if ($this->getSpinRows() > 1 || ($this->getSpinColumns() < $this->getImagesCount())) {
                $isMultiRowsEnabled = true;
            }
        }

        return $isMultiRowsEnabled;
    }

    /**
     * Get number of rows
     *
     * @return int|null
     */
    public function getSpinRows(): ?int
    {
        static $rows = null;
        if ($rows === null) {
            $rows    = 0;
            $columns = $this->getSpinColumns();
            if ($columns) {
                $rows  = 1;
                $count = $this->getImagesCount();
                if ($count != $columns) {
                    $rows = (int) floor($count / $columns);
                }
            }
        }

        return $rows;
    }

    /**
     * Get number of columns
     *
     * @return int|null
     */
    public function getSpinColumns(): ?int
    {
        static $columns = null;
        if ($columns === null) {
            $columns = $this->getSet() ? $this->getSet()->getSpinColumns() : null;
        }

        return $columns;
    }

    public function getSet(): ?AEntity
    {
        if ($this->isChangedSet()) {
            $setId = (int) Request::getInstance()->id;

            return Database::getRepo(MagicSwatchesSetModel::class)
                ->find($setId);
        }

        return null;
    }

    protected function isChangedSet(): ?int
    {
        return (int) Request::getInstance()->id;
    }

    /**
     * Get spin images count
     *
     * @return int|null
     */
    public function getImagesCount(): ?int
    {
        static $count = null;
        if ($count === null) {
            $count = $this->getSet() ? $this->getSet()->getImagesCount() : null;
        }

        return $count;
    }

    /**
     * Get spin images
     *
     * @return Collection|null
     */
    public function getImages(): ?Collection
    {
        static $images = null;
        if ($images === null) {
            $images = $this->getSet() ? $this->getSet()->getImages() : null;
        }

        return $images;
    }

    /**
     * Update Magic360 action handler
     *
     * @return void
     * @throws \Exception
     */
    public function doActionUpdateMagic360(): void
    {
        $request = Request::getInstance();

        /** @var Product $product */
        $product = $this->getProduct();

        /** @var MagicSwatchesSetModel $set */
        $set = $this->getSet() ?? new MagicSwatchesSetModel();

        $data = $request->magic360 ?? [];
        $attributeValueId = (int) $data['attribute_value'];

        if (isset($data['attribute_value'])
            && $attributeValueId > 0
        ) {
            $attribute = Database::getRepo(AttributeValueSelect::class)->find($attributeValueId);
            $set->setAttributeValue($attribute);
        }

        $isMultiRowsEnabled = isset($data['multi_rows_enabled']) && (bool) $data['multi_rows_enabled'];
        $columns            = isset($data['columns']) ? (int) $data['columns'] : 0;
        $images             = $data['images'] ?? [];

        $set->processFiles('images', $images);

        $imagesCount = $set->getImagesCount();

        if ($imagesCount > 0) {

            if (!$isMultiRowsEnabled || !$columns || ($columns > $imagesCount)) {
                $columns = $imagesCount;
            }

            $set->setSpinColumns($columns);
        }

        $set->setName($data['name']);

        if (!$this->isChangedSet()) {
            $set->setProduct($product);
        }

        Database::getEM()->persist($set);

        Database::getEM()->flush();

        TopMessage::addInfo('SkinActMagicImages magic360 data have been successfully updated');

        if (!$this->getSet()) {
            $this->setReturnURL(
                $this->buildURL('product', '', ['product_id' => $product->getProductId(), 'page' => 'magic360'])
            );
        }
    }

    public function getTitle()
    {
        return static::t('SkinActMagicImages images 360');
    }

    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(
            'Products',
            $this->buildURL('product_list')
        );

        $this->addLocationNode(
            $this->getProduct()->getName(),
            $this->buildURL('product', '', ['product_id' => $this->getProductId()])
        );
    }

    public function getProduct(): ?Product
    {
        return $this->getSet()
            ? $this->getSet()->getProduct()
            : Database::getRepo(Product::class)
                ->findOneBy([
                    'product_id' => Request::getInstance()->product_id,
                ]);
    }

    public function getSetName(): ?string
    {
        return $this->getSet() ? $this->getSet()->getName() : '';
    }

    public function getAttributeValue(): ?int
    {
        return $this->getSet() && $this->getSet()->getAttributeValue() ? (int) $this->getSet()->getAttributeValue()->getId() : null;
    }

    public function getProductId(): ?int
    {
        return $this->getProduct() ? (int) $this->getProduct()->getProductId() : null;
    }
}