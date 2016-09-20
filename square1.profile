<?php
/**
 * @file
 * Enables modules and site configuration for a standard site installation.
 */

use Drupal\contact\Entity\ContactForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_FORM_ID_alter() for install_configure_form().
 *
 * Allows the profile to alter the site configuration form.
 */
function square1_form_install_configure_form_alter(&$form, FormStateInterface $form_state) {
  $form['site_information']['site_name']['#attributes']['placeholder'] = t('Example Site Name');
  $form['site_information']['site_mail']['#default_value'] = 'core@augustash.com';
  $form['admin_account']['account']['name']['#default_value'] = 'augustash';
  $form['admin_account']['account']['mail']['#default_value'] = 'core@augustash.com';
  $form['regional_settings']['site_default_country']['#default_value'] = 'US';
  $form['regional_settings']['date_default_timezone']['#default_value'] = 'America/Chicago';
  $form['update_notifications']['update_status_module']['#default_value'] = array(1);
  $form['#submit'][] = 'square1_form_install_configure_submit';
}

/**
 * Submission handler to sync the contact.form.feedback recipient.
 */
function square1_form_install_configure_submit($form, FormStateInterface $form_state) {
  $site_mail = $form_state->getValue('site_mail');
  ContactForm::load('feedback')->setRecipients([$site_mail])->trustData()->save();
}
