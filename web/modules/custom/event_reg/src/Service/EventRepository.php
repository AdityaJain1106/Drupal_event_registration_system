<?php

namespace Drupal\event_reg\Service;

use Drupal\Core\Database\Connection;

class EventRepository {

  protected Connection $database;

  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * Insert event config row.
   */
  public function insertEventConfig(array $data): int {
    return (int) $this->database->insert('event_reg_event_config')
      ->fields([
        'registration_start_date' => $data['registration_start_date'],
        'registration_end_date' => $data['registration_end_date'],
        'event_date' => $data['event_date'],
        'event_name' => $data['event_name'],
        'category' => $data['category'],
      ])
      ->execute();
  }

  /**
   * Get all categories that exist in event config table.
   */
  public function getCategories(): array {
    $result = $this->database->select('event_reg_event_config', 'e')
      ->fields('e', ['category'])
      ->distinct()
      ->orderBy('category', 'ASC')
      ->execute()
      ->fetchCol();

    $options = [];
    foreach ($result as $cat) {
      $options[$cat] = $cat;
    }
    return $options;
  }

  /**
   * Get event dates for a category.
   */
  public function getEventDatesByCategory(string $category): array {
    $rows = $this->database->select('event_reg_event_config', 'e')
      ->fields('e', ['event_date'])
      ->condition('category', $category)
      ->orderBy('event_date', 'ASC')
      ->execute()
      ->fetchCol();

    $options = [];
    foreach ($rows as $date) {
      $clean = substr((string) $date, 0, 10);
      $options[$clean] = $clean;
    }

    // remove duplicates safely
    return array_unique($options);
  }


  /**
   * Get event names for category + date.
   * Returns [event_id => event_name]
   */
  public function getEventsByCategoryAndDate(string $category, string $event_date): array {
    $wanted = substr((string) $event_date, 0, 10);

    $rows = $this->database->select('event_reg_event_config', 'e')
      ->fields('e', ['id', 'event_name', 'event_date'])
      ->condition('category', $category)
      ->orderBy('event_name', 'ASC')
      ->execute()
      ->fetchAllAssoc('id');

    $options = [];
    foreach ($rows as $id => $row) {
      $db_date = substr((string) $row->event_date, 0, 10);
      if ($db_date === $wanted) {
        $options[(string) $id] = (string) $row->event_name;
      }
    }

    return $options;
  }

  /**
   * Get one event row by ID.
   */
  public function getEventById(int $id): ?array {
    $row = $this->database->select('event_reg_event_config', 'e')
      ->fields('e')
      ->condition('id', $id)
      ->execute()
      ->fetchAssoc();

    return $row ?: NULL;
  }

  /**
   * Check if registration window is open for given event_id.
   */
  public function isRegistrationOpen(int $event_id): bool {
    $event = $this->getEventById($event_id);
    if (!$event) {
      return FALSE;
    }

    $now = strtotime(date('Y-m-d'));
    $start = strtotime($event['registration_start_date']);
    $end = strtotime($event['registration_end_date']);

    return ($now >= $start && $now <= $end);
  }

}
