<?php

namespace Drupal\date_formatter_vedic\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure date_formatter_vedic settings.
 */
class DateFormatterVedicSettingsForm extends ConfigFormBase {
  /**
   * Total number of string fields.
   */
  const STRING_ARRAY_SIZE = 30;

  /**
   * Default character setting.
   */
  const DEFAULT_CHARACTER = 'q';

  /**
   * Default Muhurta names.
   */
  const MUHURTAS = [
    'Cryer',
    'Serpent',
    'Friend',
    'Father',
    'Bright',
    'Boar',
    'Heavenly Lights in the Universe',
    'Insight',
    'Goat/Charioteer-Face',
    'Many Offerings',
    'Possessed of Chariot',
    'Night Maker',
    'All-Enveloping Night Sky',
    'Possessed of Nobility',
    'Stake',
    'Lord who Lifted the Mount (Krishna)',
    'Unborn Foot',
    'Serpent at the Bottom',
    'Nourishment',
    'Horsement',
    'Restrainer',
    'Ignition',
    'Distributor',
    'Ornament',
    'Limitless',
    'Immortal',
    'All Pervading',
    'Resounding Light',
    'Universe',
    'Ocean',
  ];

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'date_formatter_vedic_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['date_formatter_vedic.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('date_formatter_vedic.settings');

    // Character input field
    $form['replacement_character'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Replacement character'),
      '#description' => $this->t('Enter a single character not already a formatting option of the DateTime.'),
      '#maxlength' => 1,
      '#default_value' => $config->get('replacement_character') ?? self::DEFAULT_CHARACTER,
      '#required' => TRUE,
      '#element_validate' => [
        [$this, 'validateCharacter']
      ],
    ];

    // String array input fields
    $form['muhurtas'] = [
      '#type' => 'container',
      '#tree' => TRUE,
    ];

    // Get existing strings
    $muhurtas = $config->get('muhurtas') ?? self::MUHURTAS;

    // Create 30 string input fields
    for ($i = 0; $i < self::STRING_ARRAY_SIZE; $i++) {
      $form['muhurtas'][$i] = [
        '#type' => 'textfield',
        '#title' => $this->t('Muhurta @num (@muhurta)', ['@num' => $i + 1,'@muhurta' => self::MUHURTAS[$i]]),
        '#default_value' => $muhurtas[$i],
        '#attributes' => [
          'placeholder' => $this->t('Override @muhurta', ['@muhurta' => self::MUHURTAS[$i]]),
        ],
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * Validate that the input is a single character.
   */
  public function validateCharacter($element, FormStateInterface $form_state) {
    $value = $element['#value'];
    if (strlen($value) !== 1) {
      $form_state->setError($element, $this->t('Please enter exactly one character.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Collect muhurtas array, preserving empty strings
    $muhurtas = $form_state->getValue('muhurtas');
    $muhurtas = array_pad(array_slice($muhurtas, 0, self::STRING_ARRAY_SIZE), self::STRING_ARRAY_SIZE, '');

    // Save configuration
    $this->config('date_formatter_vedic.settings')
      ->set('replacement_character', $form_state->getValue('replacement_character'))
      ->set('muhurtas', $muhurtas)
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * Provide a reset to default values method.
   */
  public function submitFormReset(array &$form, FormStateInterface $form_state) {

    $this->config('date_formatter_vedic.settings')
      ->set('replacement_character', self::DEFAULT_CHARACTER)
      ->set('muhurtas', self::MUHURTAS)
      ->save();

    parent::submitForm($form, $form_state);
  }
}
