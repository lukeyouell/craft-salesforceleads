<?php
/**
 * Salesforce Leads plugin for Craft CMS 3.x
 *
 * Generate Salesforce leads from form submissions.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2018 Luke Youell
 */

namespace lukeyouell\salesforceleads\events;

use yii\base\Event;

class SendEvent extends Event
{
    /**
     * @var Submission The user submission.
     */
    public $submission;
}
