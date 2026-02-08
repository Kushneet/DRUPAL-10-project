<?php

namespace Drupal\event_registration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExportController extends ControllerBase {

  protected Connection $database;

  public function __construct(Connection $database) {
    $this->database = $database;
  }

  public static function create(ContainerInterface $container) {
    return new static($container->get('database'));
  }

  public function export() {

    $event_date = \Drupal::request()->query->get('event_date');
    $event_id = \Drupal::request()->query->get('event_id');

    $query = $this->database->select('event_registration_entries', 'r');
    $query->join('event_registration_event', 'e', 'r.event_id = e.id');

    $query->fields('r', [
      'email',
      'college',
      'department',
      'created',
    ]);
    $query->addField('e', 'event_date');
    $query->addField('e', 'event_name');

    if ($event_date) {
      $query->condition('e.event_date', $event_date);
    }

    if ($event_id) {
      $query->condition('r.event_id', $event_id);
    }

    $results = $query->execute();

    $csv = "Email,Event Name,Event Date,College,Department,Submitted On\n";

    foreach ($results as $row) {
      $csv .= sprintf(
        "%s,%s,%s,%s,%s,%s\n",
        $row->email,
        $row->event_name,
        $row->event_date,
        $row->college,
        $row->department,
        date('Y-m-d H:i', $row->created)
      );
    }

    $response = new Response($csv);
    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition', 'attachment; filename="registrations.csv"');

    return $response;
  }
}
