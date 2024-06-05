<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Date range
 */
class DateRange extends \XLite\View\FormField\Input\Text
{
    /**
     * Labels displayed
     *
     * @var   boolean
     */
    protected static $labelsDisplayed = false;

    /**
     * Date format for hidden field (use on server)
     *
     * @var string
     */
    protected static $altFormat = '%Y-%m-%d';

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'input/text/date_range.twig';
    }

    /**
     * Parse range as string
     *
     * @param string $string String
     * @param string $format Format
     *
     * @return array
     */
    public static function convertToArray($string, $format = null)
    {
        $result = [0,0];

        if (static::isValueMatchFormat($string, $format)) {
            $result = \XLite\Core\Converter::convertRangeStringToArray($string, $format ?: static::getDateFormat(), static::getDatesSeparator());
        }

        return $result;
    }

    public static function convertToStringStatic(array $value, $format = null): string
    {
        return (new static())->convertToString($value, $format);
    }

    /**
     * Get used  date format
     *
     * @param boolean $forJS Flag: return format for JS DateRangePicker script (true) or for php's date() function (false)
     *
     * @return string
     */
    protected static function getUserDateFormat($forJS = false)
    {
        $formats = \XLite\Core\Converter::getDateFormatsByStrftimeFormat();
        return $forJS ? $formats['jsFormat'] : $formats['phpFormat'];
    }

    /**
     * Get used  date format
     *
     * @param boolean $forJS Flag: return format for JS DateRangePicker script (true) or for php's date() function (false)
     *
     * @return string
     */
    protected static function getDateFormat($forJS = false)
    {
        $formats = \XLite\Core\Converter::getAvailableDateFormats();
        return $forJS ? $formats[static::$altFormat]['jsFormat'] : $formats[static::$altFormat]['phpFormat'];
    }

    /**
     * Get separate string between start date and end date
     *
     * @return string
     */
    protected static function getDatesSeparator()
    {
        return ' ~ ';
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_JS][] = 'js/jquery-ui-i18n.min.js';
        $list[static::RESOURCE_JS][] = 'js/moment.min.js';
        $list[static::RESOURCE_JS][] = 'js/jquery.comiseo.daterangepicker.js';
        $list[static::RESOURCE_CSS][] = 'css/jquery.comiseo.daterangepicker.css';

        return $list;
    }

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'form_field/input/text/date.less';
        $list[] = 'form_field/input/text/date_range.less';

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . '/js/date_range.js';

        return $list;
    }

    /**
     * Set value
     *
     * @param mixed $value Value to set
     *
     * @return void
     */
    public function setValue($value)
    {
        if (!static::isValueMatchFormat($value)) {
            $value = '';
        }

        if (is_array($value)) {
            $value = $this->convertToString($value);
        }

        parent::setValue($value);
    }

    /**
     * @return void
     */
    public function getUserValue()
    {
        $value = parent::getValue();
        if ($value) {
            $range = static::convertToArray($value);
            $value = $this->convertToString($range, $this->getUserDateFormat());
        }

        return $value;
    }

    /**
     * Return true if dates match current date format
     *
     * @param string $value  Date range string
     * @param string $format Date format
     *
     * @return boolean
     */
    protected static function isValueMatchFormat($value, $format = null)
    {
        $range = \XLite\Core\Converter::convertRangeStringToArray($value, $format ?: static::getDateFormat(), static::getDatesSeparator());

        return ($range[0] > 0
                && $range[1] > 0
                && $value == static::convertToStringStatic($range)
            );
    }

    /**
     * Get formatted range
     *
     * @param array  $value  Date range
     * @param stirng $format Date format
     *
     * @return string
     */
    protected function convertToString(array $value, $format = null)
    {
        return \XLite\Core\Converter::convertArrayToRangeString(
            $value,
            $format ?: static::getDateFormat(),
            static::getDatesSeparator()
        );
    }

    /**
     * Add attribute 'data-end-date' to input field
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $result = parent::getCommonAttributes();

        $result['name'] = $this->getName() . '-visible';
        $result['value'] = $this->getUserValue();
        $result['data-end-date'] = date(static::getDateFormat(), \XLite\Core\Converter::convertTimeToUser());
        $result['data-datarangeconfig'] = $this->getDateRangeConfig();

        return $result;
    }

    /**
     * @return int
     */
    protected function getStartDay()
    {
        $start = \XLite\Core\Config::getInstance()->Units->week_start;

        $starts = [
            'sun' => 0,
            'mon' => 1,
            'tue' => 2,
            'wed' => 3,
            'thu' => 4,
            'fri' => 5,
            'sat' => 6,
        ];

        return $starts[$start] ?? 0;
    }

    /**
     * Get config settings for DateRangePicker
     *
     * @return string
     */
    protected function getDateRangeConfig()
    {
        $lng = \XLite\Core\Session::getInstance()->getLanguage()
            ? \XLite\Core\Session::getInstance()->getLanguage()->getCode()
            : 'en';

        $config = [
            'separator' => static::getDatesSeparator(),
            'language'  => $lng,
            'startDay'  => $this->getStartDay(),
            'altFormat' => static::getDateFormat(true),
            'format'    => static::getUserDateFormat(true),
            'labels'    => [
                'today'           => static::t('Today'),
                'thisWeek'       => static::t('This week'),
                'thisMonth'      => static::t('This month'),
                'thisQuarter'    => static::t('This quarter'),
                'thisYear'       => static::t('This year'),
                'allTime'        => static::t('All time'),
            ]
        ];

        return json_encode($config);
    }

    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $list = parent::assembleClasses($classes);

        $list[] = 'date-range';

        return $list;
    }

    /**
     * Get date format string available for user
     *
     * @return string
     */
    protected function getUserDateFormatString()
    {
        return \XLite\Core\Converter::getDateFormatsByStrftimeFormat()['userFormat'];
    }

    /**
     * Get default placeholder
     *
     * @return string
     */
    protected function getDefaultPlaceholder()
    {
        $dateFormat = $this->getUserDateFormatString();

        return $dateFormat . ' ' . static::getDatesSeparator() . ' ' . $dateFormat;
    }
}
