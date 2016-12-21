<?php

namespace Karambol\Saml\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use \OneLogin_Saml2_Settings;

class SamlSettingsFactoryProvider implements ServiceProviderInterface
{

  /** @var array */
  protected $samlConfig;

  public function __construct(array $samlConfig)
  {
    $this->samlConfig = $samlConfig;
  }

  public function register(Application $app)
  {
    $app['saml.settings_factory'] = new SamlSettingsFactory($app, $this->samlConfig);
  }

  public function boot(Application $app) {}

}

class SamlSettingsFactory
{
  /** @var array */
  protected $samlConfig;
  /** @var Application */
  protected $app;

  public function __construct(Application $app, array $samlConfig)
  {
    $this->app = $app;
    $this->samlConfig = $samlConfig;
  }

  public function getSettings($spValidationOnly = false) {
    return new OneLogin_Saml2_Settings($this->getRawArray(), $spValidationOnly);
  }

  public function getRawArray() {

    $samlConfig = $this->samlConfig;
    $urlGen = $this->app['url_generator'];
    $debug = $this->app['debug'];

    if(empty($samlConfig['debug'])) $samlConfig['debug'] = $debug;

    if(empty($samlConfig['sp']['entityId'])) {
      $samlConfig['sp']['entityId'] = $this->app['request']->getSchemeAndHttpHost();
    }

    if(empty($samlConfig['sp']['assertionConsumerService']['url'])) {
      $acsUrl = $urlGen->generate('plugins_karambol_saml_acs', [], $urlGen::ABSOLUTE_URL);
      $samlConfig['sp']['assertionConsumerService']['url'] = $acsUrl;
    }

    if(empty($samlConfig['sp']['singleLogoutService']['url'])) {
      $slsUrl = $urlGen->generate('plugins_karambol_saml_sls', [], $urlGen::ABSOLUTE_URL);
      $samlConfig['sp']['singleLogoutService']['url'] = $slsUrl;
    }

    return $samlConfig;

  }

}
