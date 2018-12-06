<?php

namespace lukeyouell\salesforceleads\controllers;

use lukeyouell\salesforceleads\SalesforceLeads;

use Craft;
use craft\web\Controller;

class PostController extends Controller
{

    // Protected Properties
    // =========================================================================

    protected $allowAnonymous = true;

    // Public Methods
    // =========================================================================

    public function actionIndex()
    {
        $this->requirePostRequest();
        $settings = SalesforceLeads::$plugin->getSettings();
        $isSpam = false;
        $invalid = false;
        $request = Craft::$app->getRequest();

        // Clean post
        $post = SalesforceLeads::getInstance()->post->cleanPost($request->post());

        // Honeypot captcha
        if ($settings->honeypot)
        {
            $val = Craft::$app->getRequest()->getBodyParam($settings->honeypotParam);
            $isSpam = SalesforceLeads::getInstance()->validation->checkHoneypot($settings->honeypotParam, $val);
        }

        // Email validation
        if (Craft::$app->plugins->getPlugin('email-validator') and $settings->emailValidation)
        {
            $email = Craft::$app->getRequest()->getBodyParam($settings->evFormParam);
            $invalid = SalesforceLeads::getInstance()->validation->validateEmail($settings->evFormParam, $email);
        }

        // Salesforce values
        $oid = $request->getBodyParam('oid');
        $retUrl = $request->getBodyParam('retURL');
        $leadSource = $request->getBodyParam('lead_source');
        $campaignId = $request->getBodyParam('Campaign_ID');

        // Set Salesforce params
        $salesforce = [];
        $salesforce['oid'] = $oid ? Craft::$app->security->validateData($oid) : $settings->organisationId;
        $salesforce['retURL'] = $retUrl ? Craft::$app->security->validateData($retUrl) : Craft::$app->sites->currentSite->baseUrl;
        $salesforce['lead_source'] = $leadSource ? Craft::$app->security->validateData($leadSource) : null;
        $salesforce['Campaign_ID'] = $campaignId ? Craft::$app->security->validateData($campaignId) : null;

        // Merge form submission and salesforce params
        $data = array_merge($post, $salesforce);

        // Post request (if request passes validation)
        if ($isSpam or $invalid) {
            // Unset data we don't want to return in the response
            unset($data['oid'], $data[$settings->honeypotParam]);

            $response = [
              'success'    => true,
              'statusCode' => 200,
              'payload'    => $data,
            ];
        } else {
            $response = SalesforceLeads::getInstance()->post->postRequest($data);
        }

        if ($response['success']) {
          Craft::$app->session->setFlash('payload', $response['payload']);
        } else {
          Craft::$app->getSession()->setError($response['reason']);
        }

        return $request->getBodyParam('redirect') ? $this->redirectToPostedUrl() : $this->asJson($response);
    }
}
