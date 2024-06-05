<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Dimensions
 */
class Dimensions extends \XLite\View\FormField\Input\AInput
{
    public const FIELD_TYPE_DIMENSIONS = 'dimensions';

    /**
     * Prepare request data (typecasting)
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    public function prepareRequestData($value)
    {
        $result = parent::prepareRequestData($value);

        return array_map('floatval', $result);
    }

    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return static::FIELD_TYPE_DIMENSIONS;
    }

    /**
     * Return field value
     *
     * @return array
     */
    public function getValue()
    {
        $result = parent::getValue();

        return (is_array($result) && count($result) == 3)
            ? array_values($result)
            : [0, 0, 0];
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/input/text/dimensions.less';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'input/text/dimensions.twig';
    }

    /**
     * Get dimensions by index
     *
     * @param integer $index Index
     *
     * @return float
     */
    protected function getDimension($index)
    {
        $value = $this->getValue();

        return $value[$index] ?? 0;
    }

    /**
     * Get length
     *
     * @return float
     */
    protected function getLength()
    {
        return $this->getDimension(0);
    }

    /**
     * Get width
     *
     * @return float
     */
    protected function getWidth()
    {
        return $this->getDimension(1);
    }

    /**
     * Get height
     *
     * @return float
     */
    protected function getHeight()
    {
        return $this->getDimension(2);
    }

    /**
     * Returns sub field name
     *
     * @param string $name Name
     *
     * @return string
     */
    protected function getSubFieldName($name)
    {
        $subName = '';

        switch ($name) {
            case 'length':
                $subName = '0';
                break;

            case 'width':
                $subName = '1';
                break;

            case 'height':
                $subName = '2';
                break;
        }

        return sprintf('%s[%s]', $this->getName(), $subName);
    }
}
