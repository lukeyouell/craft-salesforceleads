<?php

namespace lukeyouell\salesforceleads\services;

use lukeyouell\salesforceleads\SalesforceLeads;
use lukeyouell\salesforceleads\events\SendEvent;
use lukeyouell\salesforceleads\records\Log as LogRecord;

use Craft;
use craft\base\Component;

class PostService extends Component
{
    // Constants
    // =========================================================================

    const EVENT_BEFORE_SEND = 'beforeSend';

    const EVENT_AFTER_SEND = 'afterSend';

    // Public Methods
    // =========================================================================

    public static function cleanPost($post)
    {
        unset(
          $post['CRAFT_CSRF_TOKEN'],
          $post['action'],
          $post['redirect']
        );

        return $post;
    }

    public static function postRequest($submission)
    {
        $settings = SalesforceLeads::$plugin->getSettings();

        // Fire a 'beforeSend' event
        $event = new SendEvent([
            'submission' => $submission,
        ]);
        $self = new static;
        $self->trigger(self::EVENT_BEFORE_SEND, $event);

        if ($event->isSpam) {
            SalesforceLeads::getInstance()->log->insertLog(LogRecord::STATUS_FAIL, 'Submission suspected to be spam.');

            return [
                'success' => true,
                'isSpam'  => true,
                'payload' => $submission
            ];
        }

        $client = new \GuzzleHttp\Client([
          'base_uri' => 'https://webto.salesforce.com',
          'http_errors' => false,
          'timeout' => 10
        ]);

        try {

          $response = $client->request(
            'POST',
            'servlet/servlet.WebToLead',
            [
              'form_params' => $submission
            ]
          );

          // Fire an 'afterSend' event
          $event = new SendEvent([
            'submission' => $submission,
          ]);
          $self = new static;
          $self->trigger(self::EVENT_AFTER_SEND, $event);

          // Unset data we don't want to return in the response
          unset($submission['oid'], $submission[$settings->honeypotParam]);

          SalesforceLeads::getInstance()->log->insertLog(LogRecord::STATUS_SUCCESS, 'Submission handled.');

          return [
            'success' => true,
            'statusCode' => $response->getStatusCode(),
            'reason' => $response->getReasonPhrase(),
            'body' => (string) $response->getBody(),
            'payload' => $submission
          ];

        } catch (\Exception $e) {

          // Unset data we don't want to return in the response
          unset($submission['oid'], $submission[$settings->honeypotParam]);

          SalesforceLeads::getInstance()->log->insertLog(LogRecord::STATUS_FAIL, 'Submission failed ('.$e->getMessage().')');

          return [
            'success' => false,
            'reason' => $e->getMessage(),
            'payload' => $submission
          ];

        }
    }
}
