<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

trait FormIdsTrait
{
    /**
     * Maximum TTL of form id (1 hour)
     */
    public static $MAX_FORM_ID_TTL = 3600;

    /**
     * Form id length
     */
    public static $FORM_ID_LENGTH = 32;

    /**
     * Currently used form ID
     *
     * @var string
     */
    protected static $xliteFormId;

    /**
     * Last form id
     *
     * @var string
     */
    protected $lastFormId;

    /**
     * Form id characters list
     *
     * @var array
     */
    protected $chars = [
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j',
        'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't',
        'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D',
        'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
        'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
        'Y', 'Z',
    ];

    /**
     * Reset session form id
     *
     * @return string|null
     */
    public function resetFormId(): ?string
    {
        if ($this->getSessionFormId()) {
            return $this->createFormId(true);
        }

        return null;
    }

    /**
     * Get session formId
     *
     * @return string|null
     */
    public function getSessionFormId(): ?string
    {
        $formIds = array_keys($this->getFormIds());
        if (!empty($formIds)) {
            return array_pop($formIds);
        }

        return null;
    }

    /**
     * Create form id
     *
     * @param boolean $force Flag for forcing form id creation OPTIONAL
     *
     * @return string Form id
     */
    public function createFormId(bool $force = false): string
    {
        if (!$this->getLastFormId() || $force) {
            if ($this->getSessionId()) {
                $formIdStrategy = \XLite::getInstance()->getFormIdStrategy();

                if ($formIdStrategy === 'per-session') {
                    $formId = $this->getSessionFormId();
                }

                if ($formIdStrategy !== 'per-session' || !$formId) {
                    $formId = $this->generateFormId();
                    $this->addFormId($formId);
                }

                $this->lastFormId = $formId;
            } else {
                $this->lastFormId = md5(microtime(true));
            }
        }

        return $this->getLastFormId();
    }

    /**
     * Generate public session id
     *
     * @return string
     */
    public function generateFormId(): string
    {
        $iterationLimit = 30;
        $limit = count($this->chars) - 1;

        $isDuplicate = function ($id) {
            return in_array($id, array_keys($this->getFormIds()), true);
        };

        do {
            $id = '';
            for ($i = 0; static::$FORM_ID_LENGTH > $i; $i++) {
                $id .= $this->chars[mt_rand(0, $limit)];
            }
            $iterationLimit--;
        } while ($isDuplicate && 0 < $iterationLimit);

        if ($iterationLimit == 0) {
            // TODO - add throw exception
        }

        return $id;
    }

    public function getFormIds()
    {
        return $this->__get('formIds') ?? [];
    }

    public function addFormId(string $formId)
    {
        $formIds = $this->getFormIds();
        $formIds[$formId] = Converter::time() + static::$MAX_FORM_ID_TTL;
        $this->__set('formIds', $formIds);
    }

    public function removeFormId(string $formId)
    {
        $formIds = $this->getFormIds();
        unset($formIds[$formId]);
        $this->__set('formIds', $formIds);
    }

    public function removeExpiredFormIds()
    {
        $formIds = array_filter($this->getFormIds(), static function ($expiry) {
            return (int)$expiry > Converter::time();
        });
        $this->__set('formIds', $formIds);
    }

    /**
     * Restore form id
     *
     * @return string
     */
    public function restoreFormId(): string
    {
        $formIdStrategy = \XLite::getInstance()->getFormIdStrategy();

        if ($formIdStrategy === 'per-session') {
            return $this->getLastFormId();
        }

        $request = Request::getInstance();

        if (!empty($request->{\XLite::FORM_ID})) {
            $this->addFormId($request->{\XLite::FORM_ID});
            $this->lastFormId = $request->{\XLite::FORM_ID};
        }

        return $this->getLastFormId();
    }

    /**
     * @return string|null
     */
    public function getLastFormId(): ?string
    {
        return $this->lastFormId ?? $this->getSessionFormId();
    }
}
