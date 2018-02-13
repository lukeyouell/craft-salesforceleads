<?php
/**
 * Salesforce Leads plugin for Craft CMS 3.x
 *
 * Generate Salesforce leads from form submissions.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2018 Luke Youell
 */

namespace lukeyouell\salesforceleads\controllers;

use lukeyouell\salesforceleads\SalesforceLeads;

use Craft;
use craft\web\Controller;
use lukeyouell\salesforceleads\services\PostService;

/**
 * @author    Luke Youell
 * @package   SalesforceLeads
 * @since     1.0.0
 */
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
        $request = Craft::$app->getRequest();

        // Clean post
        $post = PostService::cleanPost($request->post());

        // Set Salesforce params
        $salesforce = [];
        $oid = $request->getBodyParam('oid');
        $retUrl = $request->getBodyParam('retURL');
        $leadSource = $request->getBodyParam('lead_source');
        $salesforce['oid'] = $oid ? Craft::$app->security->validateData($oid) : $settings->organisationId;
        $salesforce['retURL'] = $retUrl ? Craft::$app->security->validateData($retUrl) : Craft::$app->sites->currentSite->baseUrl;
        $salesforce['lead_source'] = $leadSource ? Craft::$app->security->validateData($leadSource) : null;

        // Merge form submission and salesforce params
        $data = array_merge($post, $salesforce);

        // Post request
        $response = PostService::postRequest($data);

        if ($response['success']) {
          Craft::$app->session->setFlash('payload', $response['payload']);
        } else {
          Craft::$app->getSession()->setError($response['reason']);
        }

        return $request->getBodyParam('redirect') ? $this->redirectToPostedUrl() : $this->asJson($response);
    }
}
