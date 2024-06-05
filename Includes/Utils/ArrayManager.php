<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils;

/**
 * Array manager
 */
abstract class ArrayManager extends \Includes\Utils\AUtils
{
    /**
     * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
     * keys to arrays rather than overwriting the value in the first array with the duplicate
     * value in the second array, as array_merge does. I.e., with array_merge_recursive,
     * this happens (documented behavior):
     *
     * http://php.net/manual/en/function.array-merge-recursive.php#92195
     *
     * @param array          $array1  Data array #1
     * @param array          $array2  Data array #2
     *
     * @return array
     */
    public static function mergeRecursiveDistinct(array $array1, array $array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (
                is_array($value)
                && isset($merged[$key])
                && is_array($merged[$key])
            ) {
                $merged[$key] = static::mergeRecursiveDistinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * Method to safely get array element (or a whole array)
     *
     * @param array          $data  Data array
     * @param integer|string $index  Array index
     * @param boolean        $strict Flag; return value or null in any case
     *
     * @return array|mixed|null
     */
    public static function getIndex(array $data, $index = null, $strict = false)
    {
        return isset($index) ? ($data[$index] ?? null) : ($strict ? null : $data);
    }

    /**
     * Return array elements having the corresponded keys
     *
     * @param array   $data   Array to filter
     * @param array   $keys   Keys (filter rule)
     * @param boolean $invert Flag; determines which function to use: "diff" or "intersect" OPTIONAL
     *
     * @return array
     */
    public static function filterByKeys(array $data, array $keys, $invert = false)
    {
        $method = $invert ? 'array_diff_key' : 'array_intersect_key';

        return $method($data, array_fill_keys($keys, true));
    }

    /**
     * Wrapper to return property from object
     *
     * @param object  $object   Object to get property from
     * @param string  $field    Field to get
     * @param boolean $isGetter Determines if the second param is a property name or a method
     *
     * @return mixed
     */
    public static function getObjectField($object, $field, $isGetter = false)
    {
        return $isGetter ? $object->$field() : $object->$field;
    }

    /**
     * Search entities in array by a field value
     *
     * @param array  $array Array to search
     * @param string $field Field to search by
     * @param mixed  $value Value to use for comparison
     *
     * @return mixed
     */
    public static function searchAllInArraysArray(array $array, $field, $value)
    {
        $result = [];

        foreach ($array as $key => $element) {
            $element = (array) $element;
            if (static::getIndex($element, $field, true) == $value) {
                $result[$key] = $element;
            }
        }

        return $result;
    }

    /**
     * Search entities in array by a field value
     *
     * @param array  $array Array to search
     * @param string $field Field to search by
     * @param mixed  $value Value to use for comparison
     *
     * @return mixed
     */
    public static function searchInArraysArray(array $array, $field, $value)
    {
        $list = static::searchAllInArraysArray($array, $field, $value);

        return $list ? reset($list) : null;
    }

    /**
     * Return some object property values
     *
     * @param array   $array    Array to use
     * @param string  $field    Field to return
     * @param boolean $isGetter Determines if the second param is a property name or a method OPTIONAL
     *
     * @return array
     */
    public static function getObjectsArrayFieldValues(array $array, $field, $isGetter = true)
    {
        foreach ($array as &$element) {
            $element = static::getObjectField($element, $field, $isGetter);
        }

        return $array;
    }

    /**
     * Search entities in array by a field value
     *
     * @param array   $array    Array to search
     * @param string  $field    Field to search by
     * @param mixed   $value    Value to use for comparison
     * @param boolean $isGetter Determines if the second param is a property name or a method OPTIONAL
     *
     * @return mixed
     */
    public static function searchAllInObjectsArray(array $array, $field, $value, $isGetter = true)
    {
        $result = [];

        foreach ($array as $key => $element) {
            if (static::getObjectField($element, $field, $isGetter) == $value) {
                $result[$key] = $element;
            }
        }

        return $result;
    }

    /**
     * Search entity in array by a field value
     *
     * @param array   $array    Array to search
     * @param string  $field    Field to search by
     * @param mixed   $value    Value to use for comparison
     * @param boolean $isGetter Determines if the second param is a property name or a method OPTIONAL
     *
     * @return mixed
     */
    public static function searchInObjectsArray(array $array, $field, $value, $isGetter = true)
    {
        $list = static::searchAllInObjectsArray($array, $field, $value, $isGetter);

        return $list ? reset($list) : null;
    }

    /**
     * Sum some object property values
     *
     * @param array   $array    Array to use
     * @param string  $field    Field to sum by
     * @param boolean $isGetter Determines if the second param is a property name or a method OPTIONAL
     *
     * @return mixed
     */
    public static function sumObjectsArrayFieldValues(array $array, $field, $isGetter = true)
    {
        return array_sum(static::getObjectsArrayFieldValues($array, $field, $isGetter));
    }

    /**
     * Takes batches of items from $data split by size of $batchSize and applies $callback to each batch.
     *
     * @param array    $data        Array of items
     * @param integer  $batchSize   Size of batches
     * @param callable $callback    Callback which is applied to batches
     *
     * @return void
     */
    public static function eachCons($data, $batchSize, $callback)
    {
        $batches = static::partition($data, $batchSize);

        foreach ($batches as $batch) {
            call_user_func($callback, $batch);
        }
    }

    /**
     * Returns batches of items from $data with size of $batchSize.
     *
     * @param array    $data        Array of items
     * @param integer  $batchSize   Size of batches
     *
     * @return array
     */
    public static function partition($data, $batchSize)
    {
        $size = count($data);

        $result = [];

        for ($position = 0; $position < $size; $position += $batchSize) {
            $batch = array_slice($data, $position, $batchSize);

            $result[] = $batch;
        }

        return $result;
    }

    /**
     * Find item
     *
     * FIXME: parameters are passed incorrectly into "call_user_func"
     * FIXME: "userData" parameter is not used
     *
     * @param mixed    &$data    Data
     * @param callback $callback Callback
     * @param mixed    $userData Additional data OPTIONAL
     *
     * @return array|void
     */
    public static function findValue(&$data, $callback, $userData = null)
    {
        $found = null;

        foreach ($data as $key => $value) {
            // Input argument
            if (call_user_func($callback, $value, $userData)) {
                $found = $value;
                break;
            }
        }

        return $found;
    }

    /**
     * Filter array
     *
     * FIXME: must use the "array_filter" function
     * FIXME: parameters are passed incorrectly into "call_user_func"
     * FIXME: "userData" parameter is not used
     *
     * @param mixed    &$data    Data
     * @param callback $callback Callback
     * @param mixed    $userData Additional data OPTIONAL
     *
     * @return array
     */
    public static function filter(&$data, $callback, $userData = null)
    {
        $result = [];

        foreach ($data as $key => $value) {
            // Input argument
            if (call_user_func($callback, $value, $userData)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Get md5 hash from array
     *
     * @param array $array
     * @return string
     */
    public static function md5(array $array)
    {
        array_multisort($array);

        return md5(serialize($array));
    }
}
