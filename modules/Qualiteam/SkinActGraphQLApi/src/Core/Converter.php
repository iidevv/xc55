<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core;

class Converter
{
    /**
     * Sanitize product description from unnecessary tags
     *
     * @param string $data String data
     *
     * @return string
     */
    public static function sanitizeProductDescriptionData($data)
    {
        if (!empty($data)) {
            $result = array();

            $dom = new \DOMDocument();
            $dom->loadHTML('<?xml encoding="utf-8" ?>' . $data);

            if ($dom->hasChildNodes()) {
                for ($i = 0; $i < $dom->childNodes->length; $i++) {
                    $result[] = static::processTagContent($dom->childNodes->item($i));
                }
            }

            $sanitizedData = '';
            foreach ($result as $res) {
                $sanitizedData .= static::reconstructTagContent($res);
            }

            $sanitizedData = trim(preg_replace('/[\r\n]+/', "\n", $sanitizedData));
        } else {
            $sanitizedData = $data;
        }

        return $sanitizedData;
    }

    /**
     * Process DOM nodes recursively
     *
     * @param \DOMNode $node
     *
     * @return array
     */
    protected static function processTagContent($node)
    {
        if ($node->hasChildNodes()) {
            $data = array();

            for ($i = 0; $i < $node->childNodes->length; $i++) {
                $data[] = static::processTagContent($node->childNodes->item($i));
            }
        } else {
            $data = htmlspecialchars_decode($node->textContent);
        }

        if ($node->nodeName == 'a') {
            $result = array(
                'node'  => $node->nodeName,
                'url'   => $node->getAttribute('href'),
                'data'  => $data,
            );
        } elseif ($node->nodeName == 'img') {
            $result = array(
                'node'  => $node->nodeName,
                'url'   => $node->getAttribute('src'),
                'data'  => $data,
            );
        } elseif ($node->nodeName == 'xml') {
            $result = array(
                'node'  => $node->nodeName,
                'data'  => '',
            );
        } else {
            $result = array(
                'node'  => $node->nodeName,
                'data'  => $data,
            );
        }

        return $result;
    }

    /**
     * Reconstruct string form the array of tag hierarchy
     *
     * @param $data
     *
     * @return string
     */
    protected static function reconstructTagContent($data)
    {
        $string = '';

        if (is_array($data['data'])) {
            foreach ($data['data'] as $child) {
                $string .= static::reconstructTagContent($child);
            }
        } else {
            $string .= $data['data'];
        }

        if (in_array($data['node'], static::getAllowedDescriptionTags())) {
            switch ($data['node']) {
                case 'a':
                    $string = '<a href="' . $data['url'] . '">' . $string . '</a>';
                    break;
                case 'img':
                    $string = '<img src="' . $data['url'] . '">' . $string . '</img>';
                    break;
                default:
                    $string = "<$data[node]>$string</$data[node]>";
                    break;
            }
        }

        return $string;
    }

    /**
     * Get allowed tags
     *
     * @return array
     */
    protected static function getAllowedDescriptionTags() {
        return array(
            'p',
            'b',
            'i',
            'strong',
            'em',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'ul',
            'li',
            'a',
            'img',
        );
    }
}
