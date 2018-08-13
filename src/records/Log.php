<?php
/**
 * Salesforce Leads plugin for Craft CMS 3.x
 *
 * Generate Salesforce leads from form submissions.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2018 Luke Youell
 */

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

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%sf_logs}}';
    }
}
