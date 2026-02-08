<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AdminRegistrationListForm extends FormBase {

  protected Connection $database;

  public function __construct(Connection $database) {
    $this->database = $database;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  public function getFormId() {
    return 'event_registration_admin_list_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $header = [
      'id' => $this->t('ID'),
      'name' => $this->t('Name'),
      'email' => $this->t('Email'),
      'event_date' => $this->t('Event Date'),
      'college' => $this->t('College'),
      'department' => $this->t('Department'),
      'created' => $this->t('Submitted'),
    ];

    $rows = [];

    $query = $this->database->select('event_registration_entries', 'e')
      ->fields('e')
      ->orderBy('created', 'DESC');

    $results = $query->execute();

    foreach ($results as $row) {
      $rows[] = [
        'id' => $row->id,
        'name' => $row->full_name,
        'email' => $row->email,
        'event_date' => $row->event_date,
        'college' => $row->college,
        'department' => $row->department,
        'created' => date('Y-m-d H:i', $row->created),
      ];
    }

    $form['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No registrations found'),
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {}
}
