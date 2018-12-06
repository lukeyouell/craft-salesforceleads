<?php

namespace lukeyouell\salesforceleads\models;

use lukeyouell\salesforceleads\records\Log as LogRecord;

use Craft;
use craft\base\Model;

class Log extends Model
{
    // Public Properties
    // =========================================================================

    public $id;

    public $dateCreated;

    public $status;

    public $details;

    // Public Methods
    // =========================================================================

    public function __toString()
    {
        return (string) $this->status;
    }

    public function rules()
    {
        return [
            [['status', 'details'], 'required'],
            [['status'], 'in', 'range' => [LogRecord::STATUS_SUCCESS, LogRecord::STATUS_FAIL]],
        ];
    }
}
