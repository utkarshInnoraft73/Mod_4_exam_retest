<?php

namespace Drupal\leadership_info\services;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormStateInterface;

/**
 * CustomServices is a service class to set and get the data Flagship form.
 */
class CustomServices {

  /**
   * Protected $database .
   *
   * @var Drupal\Core\Database\Connection
   */
  protected $database;

  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * Method setData to set the leadership form data.
   *
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   Form state.
   */
  public function setData(FormStateInterface $form_state) : void {
    $values = $form_state->getValue('names_fieldset');

    foreach ($values as $value) {
      if (is_array($value) && isset($value['leader_name'])) {
        $this->database->insert('leadership_info_form')
          ->fields([
            'leader_name' => $value['leader_name'],
            'designation' => $value['designation'],
            'linkedin' => $value['linkedin'],
            'profile_image' => $value['profile_image'],
          ])
          ->execute();
      }
    }
    Cache::invalidateTags(['leadership_info_form']);
  }

  /**
   * Method getData to get the data of Flagship form.
   *
   * @return array
   *   An array of Flagship form data.
   */
  public function getData() : array {

    $query = $this->database->select('leadership_info_form', 'l')
      ->fields('l', ['leader_name', 'designation', 'linkedin', 'profile_image'])
      ->execute();
    $results = $query->fetchAll();
    $output = [];
    foreach ($results as $row) {
      $output[] = [
        'leader_name' => $row->leader_name,
        'designation' => $row->designation,
        'linkedin' => $row->linkedin,
        'profile_image' => $row->profile_image,
      ];
    }

    return $output;
  }

}
