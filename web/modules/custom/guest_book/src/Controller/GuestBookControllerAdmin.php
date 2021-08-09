<?php

namespace Drupal\guest_book\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;

/**
 * Class DisplayTableController.
 *
 * @package Drupal\guest_book\Controller;
 */
class GuestBookControllerAdmin extends ControllerBase {

  /**
   * Data from the table.
   */
  public function dataGuestBookAdmin() {
    $query = \Drupal::database();
    $result = $query->select('guest_book', 'b')
      ->fields('b', [
        'name',
        'email',
        'telephone',
        'message',
        'avatar',
        'image',
        'timestamp',
        'id',
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
      // Get data.
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
    // Render page admin guest book page.
    return [
      'posts' => [
        '#theme' => 'guestbook_admin',
        '#rows' => $data,
      ],
    ];
  }

}
