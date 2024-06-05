<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product feed model.
 *
 * @ORM\Entity (repositoryClass="\QSL\ProductFeeds\Model\Repo\ProductFeed")
 * @ORM\Table  (name="product_feeds",
 *      indexes={
 *          @ORM\Index (name="feed_id", columns={"feed_id"}),
 *          @ORM\Index (name="name", columns={"name"}),
 *          @ORM\Index (name="orderby", columns={"orderby"}),
 *          @ORM\Index (name="generatorClass", columns={"generatorClass","enabled"}),
 *          @ORM\Index (name="enabled", columns={"enabled"})
 *      }
 * )
 */
class ProductFeed extends \XLite\Model\AEntity
{
    /**
     * Feed statuses.
     */
    public const STATUS_NEVER      = '.';
    public const STATUS_INPROGRESS = '~';
    public const STATUS_READY      = '+';
    public const STATUS_DISABLED   = 'x';
    public const STATUS_ERROR      = '!';

    /**
     * Feed types.
     */
    public const FEED_TYPE_CSV = 'csv';
    public const FEED_TYPE_TXT = 'txt';
    public const FEED_TYPE_XML = 'xml';

    /**
     * Unique identifier of the feed.
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $feed_id;

    /**
     * Name of the target comparison shopping website for the product feed.
     *
     * @var string
     * @ORM\Column (type="string", length=255)
     */
    protected $name;

    /**
     * Feed type.
     *
     * @var string
     * @ORM\Column (type="string", length=3)
     */
    protected $type = self::FEED_TYPE_CSV;

    /**
     * Name of the class that will handle the product feed generation.
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $generatorClass;

    /**
     * Position of the product feed among others in the list.
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $orderBy = 0;

    /**
     * Whether the product feed is enabled, or not.
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Name of the feed file generated the last time.
     *
     * @var string
     * @ORM\Column (type="string", length=255)
     */
    protected $filename;

    /**
     * Server path to the generated feed file.
     *
     * @var string
     * @ORM\Column (type="string", length=255)
     */
    protected $path;

    /**
     * Date the feed was successfully generated the last time.
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $date = 0;

    /**
     * Date when the feed generation process was started the last time.
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $startedDate = 0;

    /**
     * Date when the feed generation process was finished the last time.
     * If it was completed successfully, the value will match the date field.
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $finishedDate = 0;

    /**
     * Number of products already exported into the new feed.
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * Percents of the feed done.
     *
     * @var integer
     *
     * @ORM\Column (type="float")
     */
    protected $progress = 0;
    /**
     * Feed generation errors.
     *
     * @var array
     *
     * @ORM\Column (type="array")
     */
    protected $errors = [];

    /**
     * Generator object singleton for this feed.
     *
     * @var \QSL\ProductFeeds\Logic\FeedGenerator\AFeedGeneratror
     */
    private $generator;

    /**
     * Return status code for the product feed generated for the comparison shopping website.
     *
     * @return string
     */
    public function getStatusCode()
    {
        $status = static::STATUS_NEVER;

        if (!$this->isEnabled()) {
            $status = static::STATUS_DISABLED;
        } elseif ($this->isInProgress()) {
            $status = static::STATUS_INPROGRESS;
        } elseif ($this->hasErrors()) {
            $status = static::STATUS_ERROR;
        } elseif ($this->getPath()) {
            $status = static::STATUS_READY;
        }

        return $status;
    }

    /**
     * Check whether the product feed is enabled.
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Check whether a new feed file is being generated.
     *
     * @return boolean
     */
    public function isInProgress()
    {
        return ($this->startedDate > $this->finishedDate) && !$this->hasErrors();
    }

    /**
     * Check whether errors arised when generated the product feed the last time.
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Reset errors.
     *
     * @return void
     */
    public function resetErrors()
    {
        $this->errors = [];
    }

    /**
     * Add error messages.
     *
     * @param array $errors Error messages.
     *
     * @return void
     */
    public function addErrors($errors)
    {
        $this->errors = array_merge($this->errors, $errors);
    }

    /**
     * Return model unique identifier used by some core widgets.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->getFeedId();
    }

    /**
     * Move position in the feed by increasing it.
     *
     * @param integer $summand Number of items on which the position should be moved.
     *
     * @return void
     */
    public function movePosition($summand)
    {
        $this->position += (int) $summand;
    }

    /**
     * Reset the position to the start of the feed.
     *
     * @return void
     */
    public function resetPosition()
    {
        $this->position = 0;
    }

    /**
     * Queue for generating a new feed file.
     *
     * @return void
     */
    public function queue()
    {
        $this->resetPosition();
        $this->resetErrors();
        $this->resetProgress();
        $this->setPath('');
        $this->startedDate = \XLite\Core\Converter::time();
    }

    /**
     * Reset the generation progress counter.
     *
     * @return void
     */
    public function resetProgress()
    {
        $this->progress = 0;
    }

    /**
     * Returns the generator object for this feed.
     *
     * @return \QSL\ProductFeeds\Logic\FeedGenerator\AFeedGeneratror
     */
    public function getGenerator()
    {
        if (!isset($this->generator)) {
            $this->generator = $this->factoryGenerator();
        }

        return $this->generator;
    }

    /**
     * Creates a new instance of the generator class for this feed.
     *
     * @return \QSL\ProductFeeds\Logic\FeedGenerator\AFeedGeneratror
     */
    protected function factoryGenerator()
    {
        $class = $this->getGeneratorClass();

        return class_exists($class) ? new $class($this) : null;
    }

    /**
     * Returns the feed identifier.
     *
     * @return integer
     */
    public function getFeedId()
    {
        return $this->feed_id;
    }

    /**
     * Sets the feed name.
     *
     * @param string $name Name
     *
     * @return ProductFeed
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the feed name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the feed type.
     *
     * @param string $type Type
     *
     * @return ProductFeed
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Returns the feed type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the generator class name.
     *
     * @param string $generatorClass Class name
     *
     * @return ProductFeed
     */
    public function setGeneratorClass($generatorClass)
    {
        $this->generatorClass = $generatorClass;

        return $this;
    }

    /**
     * Returns the generator class name.
     *
     * @return string
     */
    public function getGeneratorClass()
    {
        return $this->generatorClass;
    }

    /**
     * Sets the position among other feeds.
     *
     * @param integer $orderBy New position
     *
     * @return ProductFeed
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * Returns the position among other feeds.
     *
     * @return integer
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * Sets the "enabled" flag.
     *
     * @param boolean $enabled New state
     *
     * @return ProductFeed
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Checks if the feed is enabled, or not.
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Sets the feed filename.
     *
     * @param string $filename Filename
     *
     * @return ProductFeed
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Returns the feed filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Sets the feed path.
     *
     * @param string $path Path on the server
     *
     * @return ProductFeed
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Returns the feed path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the feed generation date (timestamp).
     *
     * @param integer $date Date
     *
     * @return ProductFeed
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Returns the feed generation date (timestamp).
     *
     * @return integer
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Sets the date when the feed generation started (timestamp).
     *
     * @param integer $startedDate Date
     *
     * @return ProductFeed
     */
    public function setStartedDate($startedDate)
    {
        $this->startedDate = $startedDate;

        return $this;
    }

    /**
     * Returns the date when the feed generation started (timestamp).
     *
     * @return integer
     */
    public function getStartedDate()
    {
        return $this->startedDate;
    }

    /**
     * Sets the date when the feed generation finished (timestamp).
     *
     * @param integer $finishedDate
     * @return ProductFeed
     */
    public function setFinishedDate($finishedDate)
    {
        $this->finishedDate = $finishedDate;
        return $this;
    }

    /**
     * Returns the date when the feed generation finished (timestamp).
     *
     * @return integer
     */
    public function getFinishedDate()
    {
        return $this->finishedDate;
    }

    /**
     * Sets the generator position.
     *
     * @param integer $position Position
     *
     * @return ProductFeed
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Returns the generator position.
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set the progress percent.
     *
     * @param float $progress Percent
     * @return ProductFeed
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;

        return $this;
    }

    /**
     * Returns the progress percent.
     *
     * @return float
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * Set registered generator errors.
     *
     * @param array $errors Errors
     *
     * @return ProductFeed
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Returns registered generator errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
