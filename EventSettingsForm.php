<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class EventSettingsForm extends ConfigFormBase {

  protected function getEditableConfigNames() {
    return ['event_registration.settings'];
  }

  public function getFormId() {
    return 'event_registration_settings';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('event_registration.settings');

    $form['admin_email'] = [
      '#type' => 'email',
      '#title' => 'Admin notification email',
      '#default_value' => $config->get('admin_email'),
      '#required' => TRUE,
    ];

    $form['send_admin_mail'] = [
      '#type' => 'checkbox',
      '#title' => 'Send admin notification',
      '#default_value' => $config->get('send_admin_mail'),
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('event_registration.settings')
      ->set('admin_email', $form_state->getValue('admin_email'))
      ->set('send_admin_mail', $form_state->getValue('send_admin_mail'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
