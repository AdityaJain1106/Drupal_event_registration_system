<?php

namespace Drupal\event_reg\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\event_reg\Service\EventRepository;
use Drupal\event_reg\Service\RegistrationRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RegistrationAdminForm extends FormBase {

  public function __construct(
    private EventRepository $events,
    private RegistrationRepository $registrations
  ) {}

  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('event_reg.event_repository'),
      $container->get('event_reg.registration_repository')
    );
  }

  public function getFormId() {
    return 'event_reg_admin_list_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $dates = $this->events->getDistinctDates();
    $selected_date = (string) ($form_state->getValue('filter_date') ?? '');
    $event_options = $selected_date ? $this->events->getEventsByDate($selected_date) : [];
    $selected_event = (int) ($form_state->getValue('filter_event') ?? 0);

    $form['filters'] = [
      '#type' => 'details',
      '#title' => $this->t('Filters'),
      '#open' => TRUE,
    ];

    $form['filters']['filter_date'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Date'),
      '#options' => $dates,
      '#empty_option' => $this->t('- Select -'),
      '#ajax' => [
        'callback' => '::ajaxUpdateEventDropdown',
        'wrapper' => 'event-wrapper',
      ],
    ];

    $form['filters']['event_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'event-wrapper'],
    ];

    $form['filters']['event_wrapper']['filter_event'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Name'),
      '#options' => $event_options, // event_id => name
      '#empty_option' => $this->t('- Select -'),
      '#disabled' => empty($selected_date),
      '#ajax' => [
        'callback' => '::ajaxUpdateResults',
        'wrapper' => 'results-wrapper',
      ],
    ];

    $form['filters']['actions'] = [
      '#type' => 'actions',
    ];

    $form['filters']['actions']['export_csv'] = [
      '#type' => 'submit',
      '#value' => $this->t('Export CSV'),
      '#submit' => ['::exportCsvSubmit'],
      '#disabled' => empty($selected_event),
    ];

    $form['results'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'results-wrapper'],
    ];

    if ($selected_event) {
      $count = $this->registrations->getCountByEvent($selected_event);
      $form['results']['count'] = [
        '#type' => 'markup',
        '#markup' => '<p><b>Total participants:</b> ' . $count . '</p>',
      ];

      $rows = [];
      foreach ($this->registrations->getRowsByEvent($selected_event) as $r) {
        $rows[] = [
          $r['full_name'],
          $r['email'],
          $selected_date,
          $r['college_name'],
          $r['department'],
          date('Y-m-d H:i:s', (int) $r['created']),
        ];
      }

      $form['results']['table'] = [
        '#type' => 'table',
        '#header' => [
          $this->t('Name'),
          $this->t('Email'),
          $this->t('Event Date'),
          $this->t('College Name'),
          $this->t('Department'),
          $this->t('Submission Date'),
        ],
        '#rows' => $rows,
        '#empty' => $this->t('No registrations found.'),
      ];
    }
    else {
      $form['results']['hint'] = [
        '#type' => 'markup',
        '#markup' => '<p>Select date and event to view registrations.</p>',
      ];
    }

    return $form;
  }

  public function ajaxUpdateEventDropdown(array &$form, FormStateInterface $form_state) {
    $form_state->setValue('filter_event', NULL);
    return $form['filters']['event_wrapper'];
  }

  public function ajaxUpdateResults(array &$form, FormStateInterface $form_state) {
    return $form['results'];
  }

  public function exportCsvSubmit(array &$form, FormStateInterface $form_state) {
    $event_id = (int) $form_state->getValue('filter_event');
    $date = (string) $form_state->getValue('filter_date');

    $rows = $this->registrations->getRowsByEvent($event_id);

    $response = new StreamedResponse(function () use ($rows, $date) {
      $out = fopen('php://output', 'w');
      fputcsv($out, ['Name', 'Email', 'Event Date', 'College Name', 'Department', 'Submission Date']);
      foreach ($rows as $r) {
        fputcsv($out, [
          $r['full_name'],
          $r['email'],
          $date,
          $r['college_name'],
          $r['department'],
          date('Y-m-d H:i:s', (int) $r['created']),
        ]);
      }
      fclose($out);
    });

    $filename = 'registrations_' . $date . '.csv';
    $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
    $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

    $form_state->setResponse($response);
  }
}
