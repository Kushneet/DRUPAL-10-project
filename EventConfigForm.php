<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EventConfigForm extends FormBase {

  protected Connection $database;

  public function __construct(Connection $database) {
    $this->database = $database;
  }

  public static function create(ContainerInterface $container) {
    return new static($container->get('database'));
  }

  public function getFormId() {
    return 'event_config_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['registration_start'] = ['#type' => 'date', '#title' => 'Registration Start', '#required' => TRUE];
    $form['registration_end'] = ['#type' => 'date', '#title' => 'Registration End', '#required' => TRUE];
    $form['event_date'] = ['#type' => 'date', '#title' => 'Event Date', '#required' => TRUE];
    $form['event_name'] = ['#type' => 'textfield', '#title' => 'Event Name', '#required' => TRUE];
    $form['category'] = [
      '#type' => 'select',
      '#title' => 'Category',
      '#options' => [
        'Online Workshop' => 'Online Workshop',
        'Hackathon' => 'Hackathon',
        'Conference' => 'Conference',
        'One-day Workshop' => 'One-day Workshop',
      ],
      '#required' => TRUE,
    ];

    $form['submit'] = ['#type' => 'submit', '#value' => 'Save Event'];
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->database->insert('event_registration_event')->fields([
      'registration_start' => $form_state->getValue('registration_start'),
      'registration_end' => $form_state->getValue('registration_end'),
      'event_date' => $form_state->getValue('event_date'),
      'event_name' => $form_state->getValue('event_name'),
      'category' => $form_state->getValue('category'),
      'created' => time(),
    ])->execute();

    $this->messenger()->addStatus('Event saved.');
  }
}
