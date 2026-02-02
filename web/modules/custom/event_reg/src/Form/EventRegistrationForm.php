<?php

namespace Drupal\event_reg\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\event_reg\Service\EventRepository;
use Drupal\event_reg\Service\RegistrationRepository;
use Drupal\event_reg\Service\NotificationService;

class EventRegistrationForm extends FormBase {

  public function __construct(
    protected EventRepository $eventRepository,
    protected RegistrationRepository $registrationRepository,
    protected NotificationService $notificationService,
  ) {}

  public static function create(ContainerInterface $container): self {
    return new static(
      $container->get('event_reg.event_repository'),
      $container->get('event_reg.registration_repository'),
      $container->get('event_reg.notification_service'),
    );
  }

  public function getFormId(): string {
    return 'event_reg_registration_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state): array {

    // -------------------------
    // Basic fields
    // -------------------------
    $form['full_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Full Name'),
      '#required' => TRUE,
      '#maxlength' => 100,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email Address'),
      '#required' => TRUE,
      '#maxlength' => 150,
    ];

    $form['college_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('College Name'),
      '#required' => TRUE,
      '#maxlength' => 150,
    ];

    $form['department'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Department'),
      '#required' => TRUE,
      '#maxlength' => 150,
    ];

    // -------------------------
    // Category (simple key)
    // -------------------------
    $categories = $this->eventRepository->getCategories();
    $selected_category = (string) $form_state->getValue('category');

    $form['category'] = [
      '#type' => 'select',
      '#title' => $this->t('Category of the event'),
      '#required' => TRUE,
      '#options' => ['' => $this->t('- Select -')] + $categories,
      '#ajax' => [
        'callback' => '::ajaxUpdateEventDate',
        'wrapper' => 'event-date-wrapper',
        'event' => 'change',
      ],
    ];

    // -------------------------
    // Event Date (simple key)
    // -------------------------
    $date_options = ['' => $this->t('- Select -')];
    if (!empty($selected_category)) {
      $date_options += $this->eventRepository->getEventDatesByCategory($selected_category);
    }

    $selected_date = (string) $form_state->getValue('event_date');

    $form['event_date_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'event-date-wrapper'],
    ];

    $form['event_date_wrapper']['event_date'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Date'),
      '#required' => TRUE,
      '#options' => $date_options,
      '#default_value' => $selected_date ?: '',
      '#disabled' => empty($selected_category),
      '#ajax' => [
        'callback' => '::ajaxUpdateEventName',
        'wrapper' => 'event-name-wrapper',
        'event' => 'change',
      ],
    ];

    // IMPORTANT: event_date value yahi se set hota hai
    $selected_date = (string) $form_state->getValue('event_date');

    // -------------------------
    // Event Name (simple key: event_id)
    // -------------------------
    $event_options = ['' => $this->t('- Select -')];
    if (!empty($selected_category) && !empty($selected_date)) {
      $event_options += $this->eventRepository->getEventsByCategoryAndDate($selected_category, $selected_date);
    }

    $selected_event_id = (string) $form_state->getValue('event_id');

    $form['event_name_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'event-name-wrapper'],
    ];

    $form['event_name_wrapper']['event_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Name'),
      '#required' => TRUE,
      '#options' => $event_options,
      '#default_value' => $selected_event_id ?: '',
      '#disabled' => (count($event_options) <= 1),
    ];

    // -------------------------
    // Submit
    // -------------------------
    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Register'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  // Category changed => reset event_date + event_id and return date wrapper
  public function ajaxUpdateEventDate(array &$form, FormStateInterface $form_state) {
    $form_state->setValue('event_date', '');
    $form_state->setValue('event_id', '');
    return $form['event_date_wrapper'];
  }

  // Date changed => reset event_id and return name wrapper
  public function ajaxUpdateEventName(array &$form, FormStateInterface $form_state) {
    $form_state->setValue('event_id', '');
    return $form['event_name_wrapper'];
  }

  public function validateForm(array &$form, FormStateInterface $form_state): void {

    $pattern = '/^[a-zA-Z0-9\s\.\-]+$/';
    foreach (['full_name', 'college_name', 'department'] as $field) {
      $value = (string) $form_state->getValue($field);
      if (!empty($value) && !preg_match($pattern, $value)) {
        $form_state->setErrorByName($field, $this->t('Special characters are not allowed in this field.'));
      }
    }

    $email = (string) $form_state->getValue('email');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $form_state->setErrorByName('email', $this->t('Please enter a valid email address.'));
    }

    $event_date = (string) $form_state->getValue('event_date');
    if (empty($event_date)) {
      $form_state->setErrorByName('event_date', $this->t('Please select an event date.'));
      return;
    }

    $event_id = (int) $form_state->getValue('event_id');
    if (empty($event_id)) {
      $form_state->setErrorByName('event_id', $this->t('Please select an event.'));
      return;
    }

    if (!$this->eventRepository->isRegistrationOpen($event_id)) {
      $form_state->setErrorByName('event_id', $this->t('Registration is closed for the selected event.'));
      return;
    }

    // Duplicate: Email + Event Date
    if (!empty($email) && $this->registrationRepository->existsDuplicate($email, $event_date)) {
      $form_state->setErrorByName('email', $this->t('You have already registered for this event date.'));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void {
    try {
      $full_name = (string) $form_state->getValue('full_name');
      $email = (string) $form_state->getValue('email');
      $college_name = (string) $form_state->getValue('college_name');
      $department = (string) $form_state->getValue('department');

      $category = (string) $form_state->getValue('category');
      $event_date = (string) $form_state->getValue('event_date');
      $event_id = (int) $form_state->getValue('event_id');

      $event = $this->eventRepository->getEventById($event_id);
      if (!$event) {
        $this->messenger()->addError($this->t('Selected event not found.'));
        return;
      }

      $this->registrationRepository->addRegistration([
        'event_id' => $event_id,
        'full_name' => $full_name,
        'email' => $email,
        'college_name' => $college_name,
        'department' => $department,
        'category' => $category,
        'event_date' => $event_date,
      ]);

      $this->notificationService->sendRegistrationMail([
        'full_name' => $full_name,
        'email' => $email,
        'category' => $category,
        'event_date' => $event_date,
        'event_name' => (string) $event['event_name'],
      ]);

      $this->messenger()->addStatus($this->t('Registered successfully'));
      $form_state->setRebuild(TRUE);
    }
    catch (\Throwable $e) {
      \Drupal::logger('event_reg')->error('Registration failed: @msg', ['@msg' => $e->getMessage()]);
      $this->messenger()->addError($this->t('Registration failed. Please try again later.'));
    }
  }

}
