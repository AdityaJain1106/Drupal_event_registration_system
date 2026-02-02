<?php

namespace Drupal\event_reg\Service;

use Drupal\Core\Database\Connection;

class RegistrationRepository {

  public function __construct(private Connection $db) {}

  /**
   * Duplicate check: Email + Event Date (as per requirement).
   */
  public function existsDuplicate(string $email, string $event_date): bool {
    $count = (int) $this->db->select('event_reg_registrations', 'r')
      ->condition('email', $email)
      ->condition('event_date', $event_date)
      ->countQuery()
      ->execute()
      ->fetchField();

    return $count > 0;
  }

  /**
   * Insert new registration row.
   */
  public function addRegistration(array $data): int {
    return (int) $this->db->insert('event_reg_registrations')
      ->fields([
        'event_id' => (int) $data['event_id'],
        'full_name' => (string) $data['full_name'],
        'email' => (string) $data['email'],
        'college_name' => (string) $data['college_name'],
        'department' => (string) $data['department'],
        'category' => (string) $data['category'],
        'event_date' => (string) $data['event_date'],
        'created' => time(),
      ])
      ->execute();
  }

  public function getCountByEvent(int $event_id): int {
    return (int) $this->db->select('event_reg_registrations', 'r')
      ->condition('event_id', $event_id)
      ->countQuery()
      ->execute()
      ->fetchField();
  }

  public function getRowsByEvent(int $event_id): array {
    return $this->db->select('event_reg_registrations', 'r')
      ->fields('r', ['full_name', 'email', 'college_name', 'department', 'event_date', 'created'])
      ->condition('event_id', $event_id)
      ->orderBy('created', 'DESC')
      ->execute()
      ->fetchAllAssoc(NULL, \PDO::FETCH_ASSOC) ?: [];
  }

}
