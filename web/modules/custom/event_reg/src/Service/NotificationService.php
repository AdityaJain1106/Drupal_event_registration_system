<?php

namespace Drupal\event_reg\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Mail\MailManagerInterface;

class NotificationService {

  protected MailManagerInterface $mailManager;
  protected ConfigFactoryInterface $configFactory;

  public function __construct(MailManagerInterface $mail_manager, ConfigFactoryInterface $config_factory) {
    $this->mailManager = $mail_manager;
    $this->configFactory = $config_factory;
  }

  // Example: send method (tumhare module ke hisaab se adjust karna)
  public function sendRegistrationMail(array $payload): void {
    $config = $this->configFactory->get('event_reg.settings');

    $module = 'event_reg';
    $key = 'registration_confirmation';
    $langcode = 'en';

    // IMPORTANT: payload key used in hook_mail
    $params = ['payload' => $payload];

    // user mail
    $this->mailManager->mail($module, $key, $payload['email'], $langcode, $params);

    // admin mail (if enabled)
    if ($config->get('enable_admin_notifications')) {
      $admin_mail = $config->get('admin_notification_email');
      if (!empty($admin_mail)) {
        $this->mailManager->mail($module, $key, $admin_mail, $langcode, $params);
      }
    }
  }

}
