<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * REST services end-point
 */
class Rest extends \XLite\Controller\Customer\ACustomer
{
    /**
     *  Response status codes
     */
    public const STATUS_ERROR   = 'error';
    public const STATUS_INAPPLY = 'inapply';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED  = 'failed';


    /**
     * REST actions
     *
     * @var array
     */
    protected $restActions = ['get', 'post', 'put', 'delete'];

    /**
     * REST repository classes
     *
     * @var array
     */
    protected $restClasses;

    /**
     * Current REST repository
     *
     * @var object
     */
    protected $currentRepo;

    /**
     * Response data
     *
     * @var array
     */
    protected $data = [
        'status' => self::STATUS_ERROR,
        'data'   => null,
    ];

    /**
     * Handles the request.
     * Parses the request variables if necessary. Attempts to call the specified action function
     *
     * @return void
     */
    public function handleRequest()
    {
        if (in_array(\XLite\Core\Request::getInstance()->action, $this->restActions)) {
            $this->currentRepo = $this->getRepo(
                \XLite\Core\Request::getInstance()->name,
                \XLite\Core\Request::getInstance()->action
            );

            if (!$this->currentRepo) {
                $this->data['status'] = self::STATUS_INAPPLY;
            } else {
                $this->data['status'] = self::STATUS_SUCCESS;
            }
        }

        parent::handleRequest();
    }

    /**
     * Perform some actions before redirect
     *
     * FIXME: check. Action should not be an optional param
     *
     * @param string|null $action Performed action OPTIONAL
     *
     * @return void
     */
    protected function actionPostprocess($action = null)
    {
        parent::actionPostprocess($action);

        header('Content-type: application/json');
        $data = json_encode($this->data);
        header('Content-Length: ' . strlen($data));

        echo ($data);

        exit(0);
    }

    /**
     * Get REST repository classes
     *
     * @return array
     */
    protected function getRESTClasses()
    {
        if (!isset($this->restClasses)) {
            $this->restClasses = [];

            foreach ($this->defineRESTClasses() as $class) {
                $repo = null;
                if (is_object($class)) {
                    $repo = $class;
                } elseif (is_string($class)) {
                    $repo = \XLite\Core\Database::getRepo($class);
                }

                if ($repo && $repo instanceof \XLite\Base\IREST) {
                    foreach ($repo->getRESTNames() as $name) {
                        $mname = \Includes\Utils\Converter::convertToUpperCamelCase($name);
                        $this->restClasses[$name] = [
                            'repo'   => $repo,
                            'get'    => method_exists($repo, 'get' . $mname . 'REST') ? 'get' . $mname . 'REST' : null,
                            'post'   => method_exists($repo, 'post' . $mname . 'REST') ? 'post' . $mname . 'REST' : null,
                            'put'    => method_exists($repo, 'put' . $mname . 'REST') ? 'put' . $mname . 'REST' : null,
                            'delete' => method_exists($repo, 'delete' . $mname . 'REST') ? 'delete' . $mname . 'REST' : null,
                        ];
                    }
                }
            }
        }

        return $this->restClasses;
    }

    /**
     * Define REST repository classes
     *
     * @return array
     */
    protected function defineRESTClasses()
    {
        return [
            \XLite\Core\Translation::getInstance(),
            'XLite\Model\Product',
        ];
    }

    /**
     * Get repository by name and type
     *
     * @param string $name Repository name
     * @param string $type Operation type name OPTIONAL
     *
     * @return object|void
     */
    protected function getRepo($name, $type = null)
    {
        $list = $this->getRESTClasses();

        $repo = $list[$name] ?? null;

        if ($type && $repo && !isset($repo[$type])) {
            $repo = null;
        }

        return $repo;
    }

    /**
     * Get
     *
     * @return void
     */
    protected function doActionGet()
    {
        if ($this->currentRepo) {
            $this->data['data'] = $this->currentRepo['repo']->{$this->currentRepo['get']}(
                \XLite\Core\Request::getInstance()->id,
                \XLite\Core\Request::getInstance()->data
            );
        }
    }

    /**
     * Post
     *
     * @return void
     */
    protected function doActionPost()
    {
        if ($this->currentRepo) {
            $status = $this->currentRepo['repo']->{$this->currentRepo['post']}(
                \XLite\Core\Request::getInstance()->id,
                \XLite\Core\Request::getInstance()->data
            );

            if (!$status) {
                $this->data['status'] = self::STATUS_FAILED;
            }
        }
    }

    /**
     * Put
     *
     * @return void
     */
    protected function doActionPut()
    {
        if ($this->currentRepo) {
            $status = $this->currentRepo['repo']->{$this->currentRepo['put']}(
                \XLite\Core\Request::getInstance()->data
            );

            if (!$status) {
                $this->data['status'] = self::STATUS_FAILED;
            }
        }
    }

    /**
     * Delete
     *
     * @return void
     */
    protected function doActionDelete()
    {
        if ($this->currentRepo) {
            $status = $this->currentRepo['repo']->{$this->currentRepo['delete']}(
                \XLite\Core\Request::getInstance()->id,
                \XLite\Core\Request::getInstance()->data
            );

            if (!$status) {
                $this->data['status'] = self::STATUS_FAILED;
            }
        }
    }
}
