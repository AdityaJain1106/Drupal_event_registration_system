<?php

namespace Drupal\webform_test_handler_remote_post;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Overrides the http client factory Service.
 */
class WebformTestHandlerRemotePostServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('http_client_factory');
    $definition->setClass('Drupal\webform_test_handler_remote_post\WebformTestHandlerRemotePostClientFactory');
  }

}
