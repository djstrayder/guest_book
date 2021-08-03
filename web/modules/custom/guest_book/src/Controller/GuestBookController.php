<?php

namespace Drupal\guest_book\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for guest_book routes.
 */
class GuestBookController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('Hello!'),
    ];

    return $build;
  }
}
