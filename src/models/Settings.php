<?php

namespace lukeyouell\salesforceleads\models;

use lukeyouell\salesforceleads\SalesforceLeads;

use Craft;
use craft\base\Model;

class Settings extends Model
{
    // Public Properties
    // =========================================================================

    public $organisationId = null;

    public $honeypot = false;

    public $honeypotParam = 'honeypot';

    public $emailValidation = false;

    public $evFormParam = 'email';

    public $evAllowNoMX = false;

    public $evAllowCatchAll = true;

    public $evAllowRoles = true;

    public $evAllowFree = true;

    public $evAllowDisposable = false;

    // Public Methods
    // =========================================================================

    public function rules()
    {
        return [
            [['organisationId', 'honeypotParam', 'evFormParam'], 'string'],
            [['honeypot', 'emailValidation', 'evAllowNoMX', 'evAllowCatchAll', 'evAllowRoles', 'evAllowFree', 'evAllowDisposable'], 'boolean'],
            [['organisationId'], 'required'],
        ];
    }
}
