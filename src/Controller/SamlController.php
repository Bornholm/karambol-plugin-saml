<?php

namespace Karambol\Saml\Controller;

use OneLogin_Saml2_Auth;
use OneLogin_Saml2_Error;
use Karambol\KarambolApp;
use Karambol\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use \OneLogin_Saml2_Settings;

class SamlController extends Controller {

  public function mount(KarambolApp $app) {
    $app->get('/saml/login', [$this, 'handleSamlLogin'])->bind('plugins_karambol_saml_login');
    $app->get('/saml/metadata', [$this, 'handleSamlMetadata'])->bind('plugins_karambol_saml_metadata');
    $app->post('/saml/acs', [$this, 'handleSamlAttributes'])->bind('plugins_karambol_saml_acs');
    $app->get('/saml/sls', [$this, 'handleSamlLogout'])->bind('plugins_karambol_saml_sls');
  }

  public function handleSamlLogin() {
    $rawSettings = $this->get('saml.settings_factory')->getRawArray();
    $samlAuth = new OneLogin_Saml2_Auth($rawSettings);
    $samlAuth->login();
  }

  public function handleSamlMetadata() {

    $samlSettings = $this->get('saml.settings_factory')->getSettings();

    $metadata = $samlSettings->getSPMetadata();
    $errors = $samlSettings->validateMetadata($metadata);

    if(!empty($errors)) {
      throw new OneLogin_Saml2_Error(
        'Invalid SP metadata: '.implode(', ', $errors),
        OneLogin_Saml2_Error::METADATA_SP_INVALID
      );
    }

    $response = new Response();
    $response->headers->set('Content-Type', 'text/xml');
    $response->setContent($metadata);

    return $response;
  }

  public function handleSamlAttributes() {

    $rawSettings = $this->get('saml.settings_factory')->getRawArray();
    $samlAuth = new OneLogin_Saml2_Auth($rawSettings);

  }

}
