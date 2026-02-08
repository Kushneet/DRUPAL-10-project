<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EventRegistrationForm extends FormBase {

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
    return 'event_registration_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    /* ---------------- BASIC FIELDS ---------------- */

    $form['full_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Full Name'),
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email Address'),
      '#required' => TRUE,
    ];

    $form['college'] = [
      '#type' => 'textfield',
      '#title' => $this->t('College Name'),
      '#required' => TRUE,
    ];

    $form['department'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Department'),
      '#required' => TRUE,
    ];

    /* ---------------- CATEGORY DROPDOWN ---------------- */

    $categories = $this->database->select('event_registration_event', 'e')
      ->fields('e', ['category'])
      ->distinct()
      ->execute()
      ->fetchCol();

    $category_options = ['' => '- Select -'];
    foreach ($categories as $category) {
      $category_options[$category] = $category;
    }

    $selected_category = $form_state->getValue('category');

    $form['category'] = [
      '#type' => 'select',
      '#title' => $this->t('Category'),
      '#options' => $category_options,
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::updateEventDates',
        'wrapper' => 'event-date-wrapper',
        'event' => 'change',
      ],
    ];

    /* ---------------- EVENT DATE DROPDOWN ---------------- */

    $form['event_date_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'event-date-wrapper'],
    ];

    $date_options = ['' => '- Select -'];

    if (!empty($selected_category)) {
      $dates = $this->database->select('event_registration_event', 'e')
        ->fields('e', ['event_date'])
        ->condition('category', $selected_category)
        ->distinct()
        ->orderBy('event_date', 'ASC')
        ->execute()
        ->fetchCol();

      foreach ($dates as $date) {
        $date_options[$date] = $date;
      }
    }

    $selected_date = $form_state->getValue('event_date');

    $form['event_date_wrapper']['event_date'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Date'),
      '#options' => $date_options,
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::updateEventNames',
        'wrapper' => 'event-name-wrapper',
        'event' => 'change',
      ],
    ];

    /* ---------------- EVENT NAME DROPDOWN ---------------- */

    $form['event_name_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'event-name-wrapper'],
    ];

    $event_options = ['' => '- Select -'];

    if (!empty($selected_category) && !empty($selected_date)) {
      $events = $this->database->select('event_registration_event', 'e')
        ->fields('e', ['id', 'event_name'])
        ->condition('category', $selected_category)
        ->condition('event_date', $selected_date)
        ->execute();

      foreach ($events as $event) {
        $event_options[$event->id] = $event->event_name;
      }
    }

    $form['event_name_wrapper']['event_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Name'),
      '#options' => $event_options,
      '#required' => TRUE,
    ];

    /* ---------------- SUBMIT ---------------- */

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Register'),
    ];

    return $form;
  }

  /* ---------------- VALIDATION ---------------- */

  public function validateForm(array &$form, FormStateInterface $form_state) {

    $email = $form_state->getValue('email');
    $event_date = $form_state->getValue('event_date');

    // Prevent duplicate registrations
    $exists = $this->database->select('event_registration_entries', 'r')
      ->fields('r', ['id'])
      ->condition('email', $email)
      ->condition('event_date', $event_date)
      ->execute()
      ->fetchField();

    if ($exists) {
      $form_state->setErrorByName(
        'email',
        $this->t('You have already registered for this event on the selected date.')
      );
    }
  }

  /* ---------------- AJAX CALLBACKS ---------------- */

  public function updateEventDates(array &$form, FormStateInterface $form_state) {
    return $form['event_date_wrapper'];
  }

  public function updateEventNames(array &$form, FormStateInterface $form_state) {
    return $form['event_name_wrapper'];
  }

  /* ---------------- SUBMIT ---------------- */

  public function submitForm(array &$form, FormStateInterface $form_state) {

    $event = $this->database->select('event_registration_event', 'e')
      ->fields('e', ['event_name', 'event_date', 'category'])
      ->condition('id', $form_state->getValue('event_id'))
      ->execute()
      ->fetchObject();

    $this->database->insert('event_registration_entries')
      ->fields([
        'event_id' => $form_state->getValue('event_id'),
        'event_date' => $event->event_date,
        'full_name' => $form_state->getValue('full_name'),
        'email' => $form_state->getValue('email'),
        'college' => $form_state->getValue('college'),
        'department' => $form_state->getValue('department'),
        'created' => time(),
      ])
      ->execute();

    $this->messenger()->addStatus(
      $this->t('Registration successful.')
    );
  }

}
