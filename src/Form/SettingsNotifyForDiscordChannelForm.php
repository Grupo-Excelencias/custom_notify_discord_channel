<?php

/**
 * @file
 * Contains Drupal\custom_notify_discord_channel\Form\SettingsNotifyForDiscordChannelForm.
 */

namespace Drupal\custom_notify_discord_channel\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SettingsNotifyForDiscordChannelForm extends ConfigFormBase
{

  /**
   * {@inheritdoc}
   * getEditableConfigNames() - gets the configuration name
   */
  protected function getEditableConfigNames()
  {
    return [
      'custom_notify_discord_channel.adminsettings',
    ];
  }

  /**
   * {@inheritdoc}
   * getFormId() - returns the form’s unique ID
   */
  public function getFormId()
  {
    return 'custom_notify_discord_channel_form';
  }

  /**
   * {@inheritdoc}
   * buildForm() - returns the form array
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {

    //obtengo las configuraciones de este modulo
    $config = $this->config('custom_notify_discord_channel.adminsettings');

    //obtengo todos los tipos de contenidos en el sistema
    $content_types = \Drupal::entityTypeManager()
      ->getStorage('node_type')
      ->loadMultiple();
    //dd($content_types['a']->label());

    $form['custom_notify_discord_channel_activate'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Activate'),
      '#default_value' => $config->get('custom_notify_discord_channel_activate'),
      '#weight' => -1,
    ];

    $form['custom_notify_discord_channel_url_webhook'] = [
      '#type' => 'textfield',
      '#title' => $this->t(' Discord Webhook URL'),
      '#description' => $this->t('Enter the Discord Eebhook URL. Example: https://discord.com/api/webhooks/XXXXXXXXXXX/XXXXXXXXXXXXXXXXX'),
      '#required' => TRUE,
      '#default_value' => $config->get('custom_notify_discord_channel_url_webhook'),
      '#weight' => 0,
      //'settings' => ['max_length' => 64],
    ];

    /*$form['custom_notify_discord_channel_color_left_general'] = [
      '#type' => 'textfield',
      '#title' => $this->t(' General border left color'),
      '#description' => $this->t('Specify color in hexadecimal. Example: 3366ff'),
      '#default_value' => !empty($config->get('custom_notify_discord_channel_color_left_general')) ? $config->get('custom_notify_discord_channel_color_left_general') : '3366ff',
      '#weight' => 0,
      '#maxlength' => 6,
      '#size' => 6,
    ];*/

    $form['custom_notify_discord_channel_create_preview_details'] = [
      '#type' => 'details',
      '#title' => $this->t('Settings'),
      '#weight' => 0,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];

    $form['custom_notify_discord_channel_create_preview_details']['custom_notify_discord_channel_user_settings'] = [
      '#type' => 'details',
      '#title' => $this->t('User settings'),
      '#weight' => 0,
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];
    $form['custom_notify_discord_channel_create_preview_details']['custom_notify_discord_channel_user_settings']['custom_notify_discord_channel_user_author_name_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Machine name from the author field'),
      '#description' => $this->t('Machine name of the field that will be used as a author of the publication. This field must be and configured in the user fields. Example: field_machine_name'),
      '#default_value' => $config->get('custom_notify_discord_channel_user_author_name_field'),
      '#weight' => 0,
    ];

    $form['custom_notify_discord_channel_create_preview_details']['custom_notify_discord_channel_user_settings']['custom_notify_discord_channel_user_author_url_name_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Machine name from the author URL field'),
      '#description' => $this->t('Machine name of the field that will be used as a author URL of the publication. This field must be and configured in the user fields. Example: field_machine_name'),
      '#default_value' => $config->get('custom_notify_discord_channel_user_author_url_name_field'),
      '#weight' => 0,
    ];

    $form['custom_notify_discord_channel_create_preview_details']['custom_notify_discord_channel_user_settings']['custom_notify_discord_channel_user_footer_text_name_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Machine name from the footer field'),
      '#description' => $this->t('Machine name of the field that will be used as a footer of the publication. This field must be and configured in the user fields. The content of this field in the user must be short text. Example: field_machine_name'),
      '#default_value' => $config->get('custom_notify_discord_channel_user_footer_text_name_field'),
      '#weight' => 0,
    ];

    $form['custom_notify_discord_channel_create_preview_details']['custom_notify_discord_channel_user_settings']['custom_notify_discord_channel_user_footer_image_name_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Machine name from the footer image field'),
      '#description' => $this->t('Machine name of the field that will be used as a footer image of the publication. This field must be and configured in the user fields. Example: field_machine_name'),
      '#default_value' => $config->get('custom_notify_discord_channel_user_footer_image_name_field'),
      '#weight' => 0,
    ];


    $form['custom_notify_discord_channel_create_preview_details']['custom_notify_discord_channel_user_settings']['custom_notify_discord_channel_user_footer_image_use_media_name_field'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use the media module in "Machine name from the footer image field"'),
      '#default_value' => $config->get('custom_notify_discord_channel_user_footer_image_use_media_name_field'),
      '#weight' => 0,
      '#description' => $this->t('This field is to know if the multimedia module is used in the "Machine name from the footer image field" field.'),
    ];


    foreach ($content_types as $key => $value) {
      $form['custom_notify_discord_channel_create_preview_details']['custom_notify_discord_channel_' . $key] = [
        '#type' => 'details',
        '#title' => $this->t('Content type' . ': ' . $value->label()),
        '#weight' => 0,
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,
        /*'#states' => [
          'expanded' => [
            ':input[name="type"]' => ['value' => 'incoming'],
          ],
          'enabled' => [
            ':input[name="type"]' => ['value' => 'incoming'],
          ],
          'required' => [
            ':input[name="type"]' => ['value' => 'incoming'],
          ],
          'collapsed' => [
            ':input[name="type"]' => ['value' => 'outgoing'],
          ],
          'disabled' => [
            ':input[name="type"]' => ['value' => 'outgoing'],
          ],
          'optional' => [
            ':input[name="type"]' => ['value' => 'outgoing'],
          ],
        ],*/
      ];

      $form['custom_notify_discord_channel_create_preview_details']['custom_notify_discord_channel_' . $key]['custom_notify_discord_channel_' . $key . '_create'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Event create'),
        '#default_value' => $config->get('custom_notify_discord_channel_' . $key . '_create'),
        '#weight' => 0,
      ];

      $form['custom_notify_discord_channel_create_preview_details']['custom_notify_discord_channel_' . $key]['custom_notify_discord_channel_' . $key . '_create_preview'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Create preview'),
        '#default_value' => $config->get('custom_notify_discord_channel_' . $key . '_create_preview'),
        '#weight' => 0,
      ];

      $form['custom_notify_discord_channel_create_preview_details']['custom_notify_discord_channel_' . $key]['custom_notify_discord_channel_' . $key . '_description_name_field'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Machine name from the description field'),
        '#description' => $this->t('Machine name of the field that will be used as a description of the publication. Example: field_machine_name'),
        '#default_value' => $config->get('custom_notify_discord_channel_' . $key . '_description_name_field'),
        '#weight' => 0,
      ];

      $form['custom_notify_discord_channel_create_preview_details']['custom_notify_discord_channel_' . $key]['custom_notify_discord_channel_' . $key . '_image_name_field'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Machine name from the image field'),
        '#description' => $this->t('Machine name of the field that will be used as a image of the publication. Example: field_machine_name'),
        '#default_value' => $config->get('custom_notify_discord_channel_' . $key . '_image_name_field'),
        '#weight' => 0,
      ];

      $form['custom_notify_discord_channel_create_preview_details']['custom_notify_discord_channel_' . $key]['custom_notify_discord_channel_' . $key . '_image_use_media_name_field'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Use the media module in "Machine name from the image field"'),
        '#default_value' => $config->get('custom_notify_discord_channel_' . $key . '_image_use_media_name_field'),
        '#weight' => 0,
        '#description' => $this->t('This field is to know if the multimedia module is used in the "Machine name from the image field" field.'),
      ];

      $form['custom_notify_discord_channel_create_preview_details']['custom_notify_discord_channel_' . $key]['custom_notify_discord_channel_' . $key . '_color_left_general'] = [
        '#type' => 'textfield',
        '#title' => $this->t(' General border left color'),
        '#description' => $this->t('Specify color in hexadecimal. Example: 3366ff'),
        '#default_value' => !empty($config->get('custom_notify_discord_channel_' . $key . '_color_left_general')) ? $config->get('custom_notify_discord_channel_' . $key . '_color_left_general') : '3366ff',
        '#weight' => 0,
        '#maxlength' => 6,
        '#size' => 6,
      ];
    }


    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   * submitForm() - processes the form submission
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    parent::submitForm($form, $form_state);

    //obtengo todos los tipos de contenidos en el sistema
    $content_types = \Drupal::entityTypeManager()
      ->getStorage('node_type')
      ->loadMultiple();

    $settings = $this->config('custom_notify_discord_channel.adminsettings')

      ->set('custom_notify_discord_channel_url_webhook', $form_state->getValue('custom_notify_discord_channel_url_webhook'))

      ->set('custom_notify_discord_channel_activate', $form_state->getValue('custom_notify_discord_channel_activate'))

      //->set('custom_notify_discord_channel_color_left_general', $form_state->getValue('custom_notify_discord_channel_color_left_general'))

      ->set('custom_notify_discord_channel_user_author_name_field', $form_state->getValue('custom_notify_discord_channel_user_author_name_field'))

      //->set('custom_notify_discord_channel_create_preview', $form_state->getValue('custom_notify_discord_channel_create_preview'))

      ->set('custom_notify_discord_channel_user_author_url_name_field', $form_state->getValue('custom_notify_discord_channel_user_author_url_name_field'))

      ->set('custom_notify_discord_channel_user_footer_text_name_field', $form_state->getValue('custom_notify_discord_channel_user_footer_text_name_field'))

      ->set('custom_notify_discord_channel_user_footer_image_name_field', $form_state->getValue('custom_notify_discord_channel_user_footer_image_name_field'))

      ->set('custom_notify_discord_channel_user_footer_image_use_media_name_field', $form_state->getValue('custom_notify_discord_channel_user_footer_image_use_media_name_field'));

    foreach ($content_types as $key => $value) {

      $settings->set('custom_notify_discord_channel_' . $key . '_create', $form_state->getValue('custom_notify_discord_channel_' . $key . '_create'));
      $settings->set('custom_notify_discord_channel_' . $key . '_create_preview', $form_state->getValue('custom_notify_discord_channel_' . $key . '_create_preview'));
      $settings->set('custom_notify_discord_channel_' . $key . '_description_name_field', $form_state->getValue('custom_notify_discord_channel_' . $key . '_description_name_field'));
      $settings->set('custom_notify_discord_channel_' . $key . '_image_name_field', $form_state->getValue('custom_notify_discord_channel_' . $key . '_image_name_field'));
      $settings->set('custom_notify_discord_channel_' . $key . '_image_use_media_name_field', $form_state->getValue('custom_notify_discord_channel_' . $key . '_image_use_media_name_field'));;
      $settings->set('custom_notify_discord_channel_' . $key . '_color_left_general', $form_state->getValue('custom_notify_discord_channel_' . $key . '_color_left_general'));;

    }

    $settings->save();
  }

}
