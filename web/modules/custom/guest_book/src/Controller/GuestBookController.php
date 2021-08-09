<?php

namespace Drupal\guest_book\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for guest_book routes.
 */
class GuestBookController extends ControllerBase {
  /**
   * {@inheritdoc}
   */
  protected $formBuild;

  /**
   * Creating.
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->formBuild = $container->get('form_builder');
    return $instance;
  }

  /**
   * Builds the response.
   */
  public function build() {
    $guestBookForm = \Drupal::formBuilder()->getForm('Drupal\guest_book\Form\GuestBookForm');
    return $guestBookForm;
  }

  /**
   * Data from the table.
   */
  public function dataGuestBook() {
    $this->build();
    $query = \Drupal::database();
    $result = $query->select('guest_book', 'b')
      ->fields('b', [
        'id',
        'name',
        'email',
        'telephone',
        'message',
        'avatar',
        'image',
        'timestamp',
      ])
      ->orderBy('timestamp', 'DESC')
      ->execute()->fetchAll();
    $data = [];
    foreach ($result as $row) {
      $file = File::load($row->avatar);
      if (is_null($file)) {
        $row->avatar = '';
        $avatar_variables = [
          '#theme' => 'image',
          '#uri' => '/modules/custom/guest_book/files/default_user.png',
          '#width' => 100,
        ];
      }
      else {
        $avatar_uri = $file->getFileUri();
        $avatar_variables = [
          '#theme' => 'image',
          "#uri" => $avatar_uri,
          '#alt' => 'Profile avatar',
          '#title' => 'Profile avatar',
          '#width' => 100,
        ];
      }
      $image = File::load($row->image);
      if (!isset($image)) {
        $row->image = '';
        $image_variables = [
          '#theme' => 'image',
          '#uri' => 'empty_image',
          '#width' => 100,
        ];
      }
      else {
        $uri = $image->getFileUri();
        $uri = file_create_url($uri);
        $image_variables = [
          '#theme' => 'image',
          '#uri' => $uri,
          '#alt' => 'Feedback image',
          '#title' => 'Feedback image',
          '#width' => 200,
        ];
      }
      $data[] = [
        'name' => $row->name,
        'email' => $row->email,
        'telephone' => $row->telephone,
        'message' => $row->message,
        'avatar' => [
          'data' => $avatar_variables,
        ],
        'image' => [
          'data' => $image_variables,
        ],
        'timestamp' => $row->timestamp,
        'id' => $row->id,
        'edit' => t('Edit'),
        'delete' => t('Delete'),
        'uri' => isset($uri) ? $uri : '',
      ];
    }
    return [
      'form' => $this->build(),
      'guests' => [
        '#theme' => 'guestbook',
        '#rows' => $data,
      ],
    ];
  }

}
