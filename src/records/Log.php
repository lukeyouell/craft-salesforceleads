<?php

namespace lukeyouell\salesforceleads\records;

use craft\db\ActiveRecord;

class Log extends ActiveRecord
{
    // Constants
    // =========================================================================

    const STATUS_SUCCESS = 'success';

    const STATUS_FAIL = 'fail';

    // Public Methods
    // =========================================================================

    public static function tableName(): string
    {
        return '{{%sf_logs}}';
    }
}
