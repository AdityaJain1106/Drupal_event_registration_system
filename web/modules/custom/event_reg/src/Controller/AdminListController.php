<?php

namespace Drupal\event_reg\Controller;

use Drupal\Core\Controller\ControllerBase;

final class AdminListController extends ControllerBase {

  public function page(): array {
    return [
      '#markup' => $this->t('Admin listing will come here.'),
    ];
  }

}
