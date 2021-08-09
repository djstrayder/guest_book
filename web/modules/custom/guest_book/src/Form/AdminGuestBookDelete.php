<?php

namespace Drupal\guest_book\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;

/**
 * Class FormDelete.
 *
 * @package Drupal\guest_form\Form
 */
class AdminGuestBookDelete extends ConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'FormDelete';
  }

  /**
   * {@inheritdoc}
   */
  public $cid;

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Delete this entry %cid?', ['%cid' => $this->cid]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('admin.guestbook');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $cid = NULL) {
    $this->id = $cid;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $query = \Drupal::database();
    $query->delete('guest_book')
      ->condition('id', $this->id)
      ->execute();
    $this->messenger()->addStatus($this->t("Succesfully deleted"));
    $form_state->setRedirect('admin.guestbook');
  }

}

