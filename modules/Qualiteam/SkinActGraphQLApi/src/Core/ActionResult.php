<?php


namespace Qualiteam\SkinActGraphQLApi\Core;


class ActionResult extends \XLite\Base\Singleton
{

    protected $isEnabled = false;
    /**
     * @var \XLite\Model\ActionStatus
     */
    protected $actionStatus;

    public function setActionError($message = '', $code = 0)
    {
        if ($this->isEnabled) {
            $this->actionStatus = new \XLite\Model\ActionStatus(\XLite\Model\ActionStatus::STATUS_ERROR, $message, $code);
        }

    }

    public function isError()
    {
        if ($this->isEnabled) {
            return isset($this->actionStatus) && $this->actionStatus->isError();
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * @param bool $isEnabled
     */
    public function setIsEnabled(bool $isEnabled): void
    {
        $this->isEnabled = $isEnabled;
    }
}