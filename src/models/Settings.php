<?php
/**
 * Salesforce Leads plugin for Craft CMS 3.x
 *
 * Generate Salesforce leads from form submissions.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2018 Luke Youell
 */

namespace lukeyouell\salesforceleads\models;

use lukeyouell\salesforceleads\SalesforceLeads;

use Craft;
use craft\base\Model;

/**
 * @author    Luke Youell
 * @package   SalesforceLeads
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $organisationId = null;

    /**
     * @var boolean
     */
    public $honeypot = false;

    /**
     * @var string
     */
    public $honeypotParam = 'honeypot';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['organisationId', 'honeypotParam'], 'string'],
            [['organisationId'], 'required'],
        ];
    }
}
