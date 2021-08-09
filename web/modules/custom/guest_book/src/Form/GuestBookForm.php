<?php

namespace Drupal\guest_book\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Url;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\file\Entity\File;

/**
 * Class GuestBookForm.
 *
 * Provides a guest_book form.
 */
class GuestBookForm extends FormBase {

  /**
   * For guest book ID.
   */
  public function getFormId() {
    return 'guest_book';
  }

  /**
   * My form for guest book.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['name'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#placeholder' => $this->t('minimum length 2, maximum length 100'),
      '#title' => $this->t('Name:'),
      '#ajax' => [
        'callback' => '::nameValidateAjax',
        'event' => 'change',
      ],
    ];
    $form['name_result_message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="name_result_message"></div>',
    ];
    $form['email'] = [
      '#type' => 'email',
      '#required' => TRUE,
      '#placeholder' => $this->t('guestbook@gmail.com'),
      '#title' => $this->t('Email:'),
      '#ajax' => [
        'callback' => '::emailValidateAjax',
        'event' => 'change',
      ],
    ];
    $form['email_result_message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="email_result_message"></div>',
    ];

    $form['telephone'] = [
      '#type' => 'tel',
      '#required' => TRUE,
      '#placeholder' => $this->t('like this +380997548675'),
      '#title' => $this->t('Telephone Number:'),
      '#ajax' => [
        'callback' => '::telephoneValidateAjax',
        'event' => 'change',
      ],
    ];
    $form['telephone_result_message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="telephone_result_message"></div>',
    ];
    $form['message'] = [
      '#type' => 'textarea',
      '#required' => TRUE,
      '#placeholder' => $this->t('Message:'),
      '#title' => $this->t('Message:'),
    ];
    $form['avatar'] = [
      '#type' => 'managed_file',
      '#description' => $this->t('Only png, jpeg, jpg and < 2MB'),
      '#title' => $this->t('Your Avatar:'),
      '#upload_location' => 'public://images/',
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpeg jpg'],
        'file_validate_size' => ['2097152'],
      ],
    ];
    $form['image'] = [
      '#type' => 'managed_file',
      '#description' => $this->t('Only png, jpeg, jpg and < 5MB'),
      '#title' => $this->t('Adding a Picture To The Review:'),
      '#upload_location' => 'public://images/',
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpeg jpg'],
        'file_validate_size' => ['5242880'],
      ],
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send'),
      '#ajax' => [
        'callback' => '::submitAjax',
        'event' => 'click',
        'effect' => 'fade',
      ],
    ];
    $form['system-messages'] = [
      '#type' => 'markup',
      '#markup' => '<div id="form-system-messages"></div>',
      '#weight' => '-100',
    ];
    return $form;
  }

  /**
   * Validation for guest book.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $emailVL = $form_state->getValue('email');
    $telephoneVL = $form_state->getValue('telephone');
    if ((!filter_var($emailVL, FILTER_VALIDATE_EMAIL))) {
      $form_state->setErrorByName('email', $this->t('The your email not correct'));
    }
    if (strlen($form_state->getValue('name')) < 2) {
      $form_state->setErrorByName('name', $this->t('The your name is too short. Please enter a  full name.'));
    }
    if (strlen($form_state->getValue('name')) > 100) {
      $form_state->setErrorByName('name', $this->t('The your name is too long. Please enter a  full name.'));
    }
    if (!filter_var($telephoneVL, FILTER_VALIDATE_INT) || !preg_match('/^\+?3?8?(0\d{9})$/', $telephoneVL)) {
      $form_state->setErrorByName('phone', $this->t('Enter the phone number correctly'));
    }
  }

  /**
   * Validation for username.
   */
  public function nameValidateAjax(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    if (strlen($form_state->getValue('name')) < 2) {
      $response->addCommand(
        new HtmlCommand(
        '.name_result_message',
        '<div style="color:red; padding-bottom:10px">The your name is too short.</div>'
        )
      );
    }
    elseif (strlen($form_state->getValue('name')) > 100) {
      $response->addCommand(
        new HtmlCommand(
          '.name_result_message',
          '<div style="color:red; padding-bottom:10px;">The your name is too long.</div>'
        )
      );
    }
    else {
      $response->addCommand(new HtmlCommand(
        '.name_result_message',
          '<div style="color:#05ff05; padding-bottom:15px;">██</div> ',
        )
      );
    }
    return $response;
  }

  /**
   * Validation for user_email.
   */
  public function emailValidateAjax(array $form, FormStateInterface $form_state) {
    $emailVl = $form_state->getValue('email');
    $response = new AjaxResponse();
    if ((!filter_var($emailVl, FILTER_VALIDATE_EMAIL))) {
      $response->addCommand(new HtmlCommand(
        '.email_result_message',
        '<div style="color:red; padding-bottom:10px;">The your email not correct.</div>'
        )
      );
    }
    else {
      $response->addCommand(new HtmlCommand(
        '.email_result_message',
        '<div style="color:#05ff05; padding-bottom:15px;">██</div> ',
        )
      );
    }
    return $response;
  }

  /**
   * Validation for user_telephone.
   */
  public function telephoneValidateAjax(array $form, FormStateInterface $form_state) {
    $telephoneVL = $form_state->getValue('telephone');
    $response = new AjaxResponse();
    if (!filter_var($telephoneVL, FILTER_VALIDATE_INT) || !preg_match('/^\+?3?8?(0\d{9})$/', $telephoneVL)) {
      $response->addCommand(new HtmlCommand(
        '.telephone_result_message',
        '<div style="color:red; padding-bottom:10px;">Enter the phone number correctly.</div>'));
    }
    else {
      $response->addCommand(new HtmlCommand(
        '.telephone_result_message',
        '<div style="color:#05ff05; padding-bottom:15px;">██</div> ',
        )
      );
    }
    return $response;
  }

  /**
   * Validation for your Submit.
   */
  public function submitAjax(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $message = [
      '#theme' => 'status_messages',
      '#message_list' => $this->messenger()->all(),
      '#status_headings' => [
        'status' => t('Status message'),
        'error' => t('Error message'),
        'warning' => t('Warning message'),
      ],
    ];
    $message = \Drupal::service('renderer')->render($message);
    $response->addCommand(new HtmlCommand('#form-system-messages', $message));
    if (!$form_state->hasAnyerrors()) {
      $url = Url::fromRoute('guest.book');
      $command = new RedirectCommand($url->toString());
      $response->addCommand($command);
    }
    return $response;
  }

  /**
   * Sending the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $avatar = $form_state->getValue('avatar');
    $image = $form_state->getValue('image');
    // Save files as Permanent.
    $data = [
      'name' => $form_state->getValue('name'),
      'email' => $form_state->getValue('email'),
      'telephone' => $form_state->getValue('telephone'),
      'message' => $form_state->getValue('message'),
      'avatar' => $avatar[0],
      'image' => $image[0],
      'timestamp' => time(),
    ];
    if (is_null($avatar[0])) {
      $data['avatar'] = 0;
    }
    else {
      $avatarfile = File::load($avatar[0]);
      $avatarfile->setPermanent();
      $avatarfile->save();
    }
    if (is_null($image[0])) {
      $data['image'] = 0;
    }
    else {
      $imagefile = File::load($image[0]);
      $imagefile->setPermanent();
      $imagefile->save();
    }
    // Insert data to database.
    $query = \Drupal::database()->insert('guest_book');
    $query
      ->fields($data)
      ->execute();
    // Show message and redirect to this page.
    $this->messenger()->addStatus($this->t('Successfully sent'));
    $form_state->setRedirect('guest.book');
  }

}
