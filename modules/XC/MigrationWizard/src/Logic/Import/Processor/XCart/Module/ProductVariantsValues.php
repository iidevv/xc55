<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

use XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration;
use XLite\Core\Database;

/**
 * Product Variants module
 */
class ProductVariantsValues extends \XLite\Logic\Import\Processor\Products
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = [
            'attributesData' => [
                static::COLUMN_IS_MULTIPLE  => true,
                static::COLUMN_IS_MULTIROW  => true,
                static::COLUMN_IS_EMULATION => true,
            ],

            'xc4EntityId'    => [
                static::COLUMN_IS_MULTIPLE  => true,
            ],
            'variantDefault' => [
                static::COLUMN_IS_MULTIROW => true,
            ],
        ];

        // Important: Define new columns before parent to be processed first
        $columns += parent::defineColumns();

        return $columns;
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        return "v.variantid AS `xc4EntityId`"
            . ", p.productcode AS `sku`"
            . ", CONCAT(p.productid, '&&', v.variantid) AS `attributesData`"
            . ", v.variantid AS `variantID`"
            . ", v.productcode AS `variantSKU`"
            . ", pr.price AS `variantPrice`"
            . ", v.avail AS `variantQuantity`"
            . ", v.def AS `variantDefault`"
            . ", v.weight AS `variantWeight`";
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $prefix = self::getTablePrefix();

        $id_generator_place_holder = self::GENERATOR_PLACEHOLDER;

        return "{$prefix}variants AS v"
            . " INNER JOIN {$prefix}products AS p"
            . ' ON p.`productid` = v.`productid`'
            . ' AND v.`variantid` <> 0'
            . $id_generator_place_holder
            . " INNER JOIN {$prefix}pricing AS pr"
            . ' ON pr.`quantity` = "1"'
            . ' AND pr.`variantid` = v.`variantid`'
            . ' AND pr.`membershipid` = "0"';
    }

    /**
     * Define filter SQL
     *
     * @return array
     */
    public static function defineDatasorter()
    {
        return ['v.productid', 'v.variantid'];
    }

    /**
     * Define ID generator data
     *
     * @return array
     */
    public static function defineIdGenerator()
    {
        $prefix = self::getTablePrefix();

        return [
            'table' => "{$prefix}variants",
            'alias' => 'v',
            'order' => ['v.productid', 'v.variantid'],
        ];
    }

    /**
     * Define filter SQL
     *
     * @return string
     */
    public static function defineDatafilter()
    {
        $result = '1';

        if (static::isDemoModeMigration()) {
            $productIds = static::getDemoProductIds();
            if (!empty($productIds)) {
                $productIds = implode(',', $productIds);
                $result = "p.productid IN ({$productIds})";
            }
        }

        return $result;
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * Get product language fields SQL
     *
     * @return string
     */
    public static function getProductVariantsAttributesSQL($productid, $variantid)
    {
        $tp = self::getTablePrefix();

        return "SELECT GROUP_CONCAT("
            . " CONCAT(cs.`class`, ' (field:product)', ' =>> ', co.`option_name` ) SEPARATOR '&&'"
            . " )"
            . " FROM {$tp}variant_items AS vi"
            . " INNER JOIN {$tp}class_options AS co"
            . " ON vi.`variantid` = {$variantid}"
            . " AND co.`optionid` = vi.`optionid`"
            . " INNER JOIN {$tp}classes AS cs"
            . " ON cs.`productid` = {$productid}"
            . " AND cs.`classid` = co.`classid`"
            . " AND cs.`is_modifier` = ''"
            . " ORDER BY co.`orderby` ASC, co.`optionid` ASC";
    }

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migrating product variants');
    }

    // }}} </editor-fold>

    // {{{ Import <editor-fold desc="Import" defaultstate="collapsed">

    protected $variantIdsList = [];

    /**
     * Import data
     *
     * @param array $data Row set Data
     *
     * @return boolean
     */
    protected function importData(array $data)
    {
        $key = static::VARIANT_PREFIX . 'ID';
        $this->variantIdsList = [];
        if (isset($data[$key])) {
            foreach ($data[$key] as $k => $v) {
                $data[$key][$k] = strlen($v) > 32 ? md5($v) : $v;
            }
            $this->variantIdsList = $data[$key];
        }

        return parent::importData($data);
    }

    /**
     * Import 'variantID' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importVariantIDColumn(\XLite\Model\Product $model, $value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value)) {
            foreach ($this->variants as $rowIndex => $variant) {
                if (!empty($value[$rowIndex])) {
                    $variant->setVariantId($this->normalizeValueAsString($value[$rowIndex]));
                }
            }
        }
    }

    /**
     * Import 'variantDefault' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importVariantDefaultColumn(\XLite\Model\Product $model, $value, array $column)
    {
        foreach ($this->variants as $rowIndex => $variant) {
            if (!empty($value[$rowIndex])) {
                $variant->setDefaultValue($value[$rowIndex] === 'Y');
            } else {
                $variant->setDefaultValue(false);
            }
        }
    }

    /**
     * Import 'attributes data' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importAttributesDataColumn(\XLite\Model\Product $model, $value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value)) {
            foreach ($value as $index => $attr) {
                if (
                    $this->getConnection()
                    && ($query = $this->getConnection()->query(
                        $this->getProductVariantsAttributesSQL($attr[0], $attr[1])
                    ))
                    && !empty($query)
                    && ($attributes = $query->fetchColumn())
                ) {
                    $value[$index] = explode('&&', trim($attributes));
                }
            }

            $values = [];

            foreach ($value as $index => $attr) {
                foreach ($attr as $field) {
                    if (preg_match('/(.+)[ ]+\((field:product)\)[ ]+=>>[ ]+(.+)/iSs', $field, $m)) {
                        $field_key   = "{$m[1]} ({$m[2]})";
                        $field_value = $m[3];
                        if (!isset($values[$field_key])) {
                            $values[$field_key] = [$index => [$field_value]];
                        } else {
                            array_push($values[$field_key], [$field_value]);
                        }
                    }
                }
            }




            $this->importAttributesColumn($model, $values, $column);
        }
    }

    // {{{ temporary fix

    protected function importAttributesColumn(\XLite\Model\Product $model, array $value, array $column)
    {
        foreach ($value as $k => $v) {
            if (!$this->isVariantValues($v)) {
                $value[$k] = array_splice($v, 0, 1);
            }
        }

        $this->defaultImportAttributesColumn($model, $value, $column);

        if ($this->multAttributes) {
            Database::getEM()->flush();

            $variantsAttributes = [];
            foreach ($this->multAttributes as $id => $values) {
                if ($this->isVariantValues($values)) {
                    foreach ($values as $k => $v) {
                        $variantsAttributes[$k][$id] = array_shift($v);
                    }
                } else {
                    unset($this->multAttributes[$id]);
                    continue;
                }
            }

            if ($variantsAttributes) {
                $variantsRepo = Database::getRepo('XC\ProductVariants\Model\ProductVariant');

                $tmp = [];
                foreach ($variantsAttributes as $k => $v) {
                    $tmp[$k] = implode('::', $v);
                }
                if (count($tmp) === count($variantsAttributes)) {
                    foreach ($variantsAttributes as $rowIndex => $values) {
                        foreach ($values as $id => $value) {
                            if (!isset($this->variantsAttributes[$id])) {
                                $this->variantsAttributes[$id] = Database::getRepo('XLite\Model\Attribute')
                                    ->find($id);
                            }
                            $attribute = $this->variantsAttributes[$id];

                            $repo = Database::getRepo($attribute->getAttributeValueClass($attribute->getType()));
                            if ($attribute::TYPE_CHECKBOX == $attribute->getType()) {
                                $values[$id] = $repo->findOneBy(
                                    [
                                        'attribute' => $attribute,
                                        'product'   => $model,
                                        'value'     => $this->normalizeValueAsBoolean($value),
                                    ]
                                );
                            } else {
                                $attributeOption = Database::getRepo('XLite\Model\AttributeOption')
                                    ->findOneByNameAndAttribute($value, $attribute);
                                $values[$id] = $repo->findOneBy(
                                    [
                                        'attribute'        => $attribute,
                                        'product'          => $model,
                                        'attribute_option' => $attributeOption,
                                    ]
                                );
                            }
                        }

                        if (isset($this->variants[$rowIndex])) {
                            $idVariant = $this->variants[$rowIndex];
                        } else {
                            $idVariant = null;
                        }

                        $variant = $model->getVariantByAttributeValues($values, true);
                        $oldVariantId = $variant
                            ? $variant->getVariantId()
                            : null;

                        if (!$variant || (isset($idVariant) && $idVariant->getId() !== $variant->getId())) {
                            if (isset($variant)) {
                                Database::getEM()->remove($variant);
                            }

                            if (isset($idVariant)) {
                                $variant = $idVariant;
                                $variant->getAttributeValueC()->clear();
                                $variant->getAttributeValueS()->clear();
                            } else {
                                $variant = $variantsRepo->insert(null, false);
                                $variant->setProduct($model);
                                $model->addVariants($variant);
                            }

                            foreach ($values as $attributeValue) {
                                $method = 'addAttributeValue' . $attributeValue->getAttribute()->getType();
                                $variant->$method($attributeValue);
                                $attributeValue->addVariants($variant);
                            }

                            if (!$oldVariantId) {
                                $variant->setVariantId($variantsRepo->assembleUniqueVariantId($variant));
                            }
                        }

                        $this->variants[$rowIndex] = $variant;
                    }
                }

                foreach ($model->getVariantsAttributes() as $va) {
                    $model->getVariantsAttributes()->removeElement($va);
                    $va->getVariantsProducts()->removeElement($model);
                }

                foreach ($this->variantsAttributes as $va) {
                    $model->addVariantsAttributes($va);
                    $va->addVariantsProducts($model);
                }
            }

            $model->assignDefaultVariant();
        }
    }

    protected function defaultImportAttributesColumn(\XLite\Model\Product $model, array $value, array $column)
    {
        $this->multAttributes = [];
        foreach ($value as $attr => $v) {
            if (preg_match('/(.+)\(field:(global|product|class)([ ]*>>>[ ]*(.+))?\)(_([a-z]{2}))?/iSs', $attr, $m)) {
                $type = $m[2];
                $name = trim($m[1]);
                $lngCode = $m[6] ?? null;
                $productClass = $type === 'class'
                    ? $model->getProductClass()
                    : null;
                $product = $type === 'product'
                    ? $model
                    : null;

                $values = [];
                foreach ($v as $value) {
                    $values = array_merge($values, $value);
                }
                $values = array_values(array_unique($values));
                $shouldClear = $this->verifyValueAsNull($values);
                $notEmptyValues = array_filter($values, static function ($element) {
                    return $element !== "";
                });

                if ((empty($notEmptyValues) && !$shouldClear) || ($type === 'class' && !$productClass)) {
                    continue;
                }

                $attributeGroup = isset($m[4]) && $type !== 'product'
                    ? $this->normalizeValueAsAttributeGroup($m[4], $productClass)
                    : null;

                $data = [
                    'value'    => [],
                    'default'  => [],
                    'price'    => [],
                    'weight'   => [],
                ];
                $hasOptions = false;
                foreach ($values as $k => $value) {
                    $data['value'][$k] = $value;
                }
                $data['multiple'] = 1 < count($data['value']);

                $cnd = new \XLite\Core\CommonCell();

                if ($product && $product->getId()) {
                    $cnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT} = $product;
                } else {
                    $cnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT} = null;
                }

                $cnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT_CLASS}   = $productClass;
                $cnd->{\XLite\Model\Repo\Attribute::SEARCH_ATTRIBUTE_GROUP} = $attributeGroup;
                $cnd->{\XLite\Model\Repo\Attribute::SEARCH_NAME}            = $name;

                $attribute = \XLite\Core\Database::getRepo('XLite\Model\Attribute')->search($cnd);

                if ($attribute) {
                    $attribute = $attribute[0];
                } else {
                    $type = !$data['multiple'] && !$hasOptions
                        ? \XLite\Model\Attribute::TYPE_TEXT
                        : \XLite\Model\Attribute::TYPE_SELECT;
                    if (count($data['value']) === 1 || count($data['value']) === 2) {
                        $isCheckbox = true;
                        foreach ($data['value'] as $val) {
                            $isCheckbox = $isCheckbox && $this->verifyValueAsBoolean($val);
                        }
                        if ($isCheckbox) {
                            $type = \XLite\Model\Attribute::TYPE_CHECKBOX;
                        }
                    }
                    $attribute = \XLite\Core\Database::getRepo('\XLite\Model\Attribute')->insert(
                        [
                            'name'           => $name,
                            'productClass'   => $productClass,
                            'attributeGroup' => $attributeGroup,
                            'product'        => $product,
                            'type'           => $type,
                        ]
                    );

                    if ($attributeGroup && $productClass) {
                        $attributeGroup->setProductClass($productClass);
                    }
                }

                if ($data['multiple']) {
                    $this->multAttributes[$attribute->getId()] = $v;
                }

                $data['ignoreIds'] = true;

                if ($lngCode) {
                    $oldCode = $this->importer->getLanguageCode();
                    $this->importer->setLanguageCode($lngCode);
                }

                if ($attribute->getType() === \XLite\Model\Attribute::TYPE_CHECKBOX) {
                    foreach ($data['value'] as $k => $val) {
                        $data['value'][$k] = $this->normalizeValueAsBoolean($val);
                    }
                }
                if ($shouldClear) {
                    $attribute->setAttributeValue($model, []);
                } else {
                    $attribute->setAttributeValue($model, $data);
                }

                if ($lngCode) {
                    static::databaseGetEmFlush();
                    $this->importer->setLanguageCode($oldCode);
                }
            }
        }
    }

    // }}}

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        $dataset = self::defineDataset();

        return Configuration::isModuleEnabled(Configuration::MODULE_PRODUCT_OPTIONS)
            && static::getCellData(
                'SELECT 1'
                . " FROM {$dataset} LIMIT 1"
            );
    }

    // }}} </editor-fold>
}
