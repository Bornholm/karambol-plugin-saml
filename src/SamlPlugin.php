<?php

namespace Karambol\Saml;

use Karambol\KarambolApp;
use Karambol\Plugin\Plugin;
use Karambol\Saml\Controller;
use Karambol\Saml\Provider\SamlSettingsFactoryProvider;

class SamlPlugin extends Plugin {

  public function boot(KarambolApp $app, array $options) {

    parent::boot($app, $options);

    $app->register(new SamlSettingsFactoryProvider($options['saml']));

    $this->registerViews(__DIR__.'/Views');
    $this->registerEntities(__DIR__.'/Entity');
    $this->registerTranslation('fr', __DIR__.'/../locales/fr.yml');
    $this->registerControllers([
      Controller\SamlController::class
    ]);

  }

}
