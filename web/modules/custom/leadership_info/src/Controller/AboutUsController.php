<?php

namespace Drupal\leadership_info\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;

/**
 * Class AboutUsController for the page of about us.
 *
 * It is a controller class that will show on the /about-us route.
 */
class AboutUsController extends ControllerBase {

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
   * Method build to show the data.
   *
   * @return array
   *   The data fetched from the 'leadership_info_table' table.
   */
  public function build(): array {

    // Select the data from the leadership table.
    $query = $this->database->select('leadership_info_form', 'l')
      ->fields('l', ['leader_name', 'designation', 'linkedin', 'profile_image'])
      ->execute();
    $results = $query->fetchAll();

    $output = [];
    // Set the data in output array.
    $output = '<h3>Leaderships</h3><ul>';
    foreach ($results as $row) {
      $output .= "<br>Leader name: $row->leader_name<br>Designation: $row->designation<br>linkedin: $row->linkedin<br>Profile: $row->profile_image";
    }
    $output .= '</ul>';
    return [
      '#type' => 'markup',
      '#markup' => $output,
      '#cache' => [
        'tags' => ['leadership_info_form'],
      ],
    ];
  }

}
