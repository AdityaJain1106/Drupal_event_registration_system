<?php

namespace Drupal\event_reg\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\event_reg\Service\EventRepository;

class EventConfigForm extends FormBase {

  protected EventRepository $eventRepository;

  public function __construct(EventRepository $eventRepository) {
    $this->eventRepository = $eventRepository;
  }

  public static function create(ContainerInterface $container): self {
    return new static(
      $container->get('event_reg.event_repository')
    );
  }

  public function getFormId(): string {
    return 'event_reg_event_config_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['registration_start_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Event Registration start date'),
      '#required' => TRUE,
    ];

    $form['registration_end_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Event Registration end date'),
      '#required' => TRUE,
    ];

    $form['event_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Event Date'),
      '#required' => TRUE,
    ];

    $form['event_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Event Name'),
      '#required' => TRUE,
      '#maxlength' => 255,
    ];

    $form['category'] = [
      '#type' => 'select',
      '#title' => $this->t('Category of the event'),
      '#required' => TRUE,
      '#options' => [
        'Online Workshop' => $this->t('Online Workshop'),
        'Hackathon' => $this->t('Hackathon'),
        'Conference' => $this->t('Conference'),
        'One-day Workshop' => $this->t('One-day Workshop'),
      ],
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save Event'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state): void {
    $start = $form_state->getValue('registration_start_date');
    $end = $form_state->getValue('registration_end_date');

    if ($start && $end && strtotime($end) < strtotime($start)) {
      $form_state->setErrorByName('registration_end_date', $this->t('End date cannot be earlier than start date.'));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->eventRepository->insertEventConfig([
      'registration_start_date' => $form_state->getValue('registration_start_date'),
      'registration_end_date' => $form_state->getValue('registration_end_date'),
      'event_date' => $form_state->getValue('event_date'),
      'event_name' => $form_state->getValue('event_name'),
      'category' => $form_state->getValue('category'),
    ]);

    $this->messenger()->addStatus($this->t('Event saved successfully.'));
  }

}
