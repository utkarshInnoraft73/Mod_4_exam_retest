<?php

namespace Drupal\leadership_info\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\leadership_info\services\CustomServices;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a leadership form.
 *
 * This class build, submit a form for leadership on the abou-us page.
 */
class LeadershipInfoForm extends FormBase {

  /**
   * Method getFormId.
   *
   * @return string
   *   The form id.
   */
  public function getFormId(): string {
    return 'leadership_info_form';
  }

  /**
   * Protected loaddata.
   *
   * @var mixed
   */
  protected $loaddata;

  public function __construct(CustomServices $loaddata) {
    $this->loaddata = $loaddata;
  }

  /**
   * Method create.
   *
   * @param CSymfony\Component\DependencyInjection\ContainerInterface $container
   *   [Explicite description].
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('leadership_info.db_operations')
    );
  }

  /**
   * Method buildForm to build the form.
   *
   * This form is a AJAX form that has a button for add form. When the user
   * Clicks the button the same form will be added one next time again. There is
   * Also a remove button that remove the forms.
   *
   * @param array $form
   *   The primary structure that represents the form's components and
   *   configuration.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   This object provides methods to get, set, and manage form values and
   *   other related states.
   *
   * @return array
   *   Return the form elements in the form of array.
   */
  public function buildForm(array $form, FormStateInterface $form_state) : array {

    $i = 0;
    $name_field = $form_state->get('num_names');
    $form['#tree'] = TRUE;
    $form['names_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Add/Remove new groups.'),
      '#prefix' => '<div id="names-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];

    if (empty($name_field)) {
      $name_field = $form_state->set('num_names', 1);
    }
    for ($i = 0; $i < $form_state->get('num_names'); $i++) {
      $form['names_fieldset'][$i]['group_heading'] = [
        '#type' => 'markup',
        '#markup' => "<h2>" . $this->t('Group @i', ['@i' => $i + 1]) . "</h2>",
        '#prefix' => '<div>',
        '#suffix' => '</div>',
      ];
      $form['names_fieldset'][$i]['leader_name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Leader name'),
        '#required' => TRUE,
      ];
      $form['names_fieldset'][$i]['designation'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Designation'),
        '#required' => TRUE,
      ];
      $form['names_fieldset'][$i]['linkedin'] = [
        '#type' => 'url',
        '#title' => $this->t('linkedin'),
        '#required' => TRUE,
      ];
      $form['names_fieldset'][$i]['profile_image'] = [
        '#type' => 'managed_file',
        '#title' => $this->t('Profile Image'),
        '#required' => TRUE,
      ];
    }

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['names_fieldset']['actions']['add_name'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add one more'),
      '#submit' => ['::addOne'],
      '#ajax' => [
        'callback' => '::addmoreCallback',
        'wrapper' => 'names-fieldset-wrapper',
      ],
    ];
    if ($form_state->get('num_names') > 1) {
      $form['names_fieldset']['actions']['remove_name'] = [
        '#type' => 'submit',
        '#value' => $this->t('Remove one'),
        '#submit' => ['::removeCallback'],
        '#ajax' => [
          'callback' => '::addmoreCallback',
          'wrapper' => 'names-fieldset-wrapper',
        ],
      ];
    }
    $form_state->setCached(FALSE);
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    return $form;
  }

  /**
   * Method addOne to add one form.
   *
   * @param array $form
   *   The primary structure that represents the form's components and
   *   configuration.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   This object provides methods to get, set, and manage form values and
   *   other related states.
   */
  public function addOne(array &$form, FormStateInterface $form_state) : void {
    $name_field = $form_state->get('num_names');
    $add_button = $name_field + 1;
    $form_state->set('num_names', $add_button);
    $form_state->setRebuild();
  }

  /**
   * Method addmoreCallback.
   *
   * @param array $form
   *   The primary structure that represents the form's components and
   *   configuration.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   This object provides methods to get, set, and manage form values and
   *   other related states.
   *
   * @return array
   *   Return the forms.
   */
  public function addmoreCallback(array &$form, FormStateInterface $form_state) : array {
    $form_state->get('num_names');
    return $form['names_fieldset'];
  }

  /**
   * Method removeCallback to remove callback.
   *
   * @param array $form
   *   Form elements.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   Form state.
   */
  public function removeCallback(array &$form, FormStateInterface $form_state) : void {
    $name_field = $form_state->get('num_names');
    if ($name_field > 1) {
      $remove_button = $name_field - 1;
      $form_state->set('num_names', $remove_button);
    }
    $form_state->setRebuild();
  }

  /**
   * Method submitForm to submit the form.
   *
   * @param array $form
   *   The primary structure that represents the form's components and
   *   configuration.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   This object provides methods to get, set, and manage form values and
   *   other related states.
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->loaddata->setData($form_state);
    $this->messenger()->addStatus($this->t('The data has been saved succesfully'), 'status');
    $form_state->setRedirect('leadership_info.aboutus');

  }

}
