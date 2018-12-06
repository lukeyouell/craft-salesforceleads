<?php

namespace lukeyouell\salesforceleads\services;

use lukeyouell\salesforceleads\SalesforceLeads;
use lukeyouell\salesforceleads\records\Log as LogRecord;

use Craft;
use craft\base\Component;

class ValidationService extends Component
{
    // Public Properties
    // =========================================================================

    public $settings;

    // Public Methods
    // =========================================================================

    public function init()
    {
        $this->settings = SalesforceLeads::$plugin->settings;
    }

    public static function checkHoneypot($param = null, $val = null)
    {
        if ($val === null) {
            SalesforceLeads::getInstance()->log->insertLog(LogRecord::STATUS_FAIL, 'Couldn\'t check honeypot field because no POST parameter named "'.$param.'" exists.');
            return false;
        }

        if ($val !== '') {
            SalesforceLeads::getInstance()->log->insertLog(LogRecord::STATUS_FAIL, 'Submission detected as spam.');
            return true;
        }
    }

    public function validateEmail($param = null, $email = null)
    {
        if ($email === null) {
            SalesforceLeads::getInstance()->log->insertLog(LogRecord::STATUS_FAIL, 'Couldn\'t check email field because no POST parameter named "'.$param.'" exists.');
            return false;
        }

        $validator = Craft::$app->plugins->getPlugin('email-validator');
        $validation = $validator::getInstance()->validationService->validateEmail($email);

        $errors = false;

        if (!$validation['format_valid']) {
            $errors = true;
        }

        if (!$this->settings->evAllowNoMX and !$validation['mx_found']) {
            $errors = true;
        }

        if (!$this->settings->evAllowCatchAll and $validation['catch_all']) {
            $errors = true;
        }

        if (!$this->settings->evAllowRoles and $validation['role']) {
            $errors = true;
        }

        if (!$this->settings->evAllowFree and $validation['free']) {
            $errors = true;
        }

        if (!$this->settings->evAllowDisposable and $validation['disposable']) {
            $errors = true;
        }

        if ($errors) {
            SalesforceLeads::getInstance()->log->insertLog(LogRecord::STATUS_FAIL, 'Submission failed email validation.');
            return true;
        }

        return $errors;
    }
}
