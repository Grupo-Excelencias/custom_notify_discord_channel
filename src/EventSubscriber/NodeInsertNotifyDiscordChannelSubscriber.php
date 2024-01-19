<?php

namespace Drupal\custom_notify_discord_channel\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\custom_notify_discord_channel\Event\NodeInsertNotifyDiscordChannelEvent;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Url;
use Drupal\user\Entity\User;

/**
 * Logs the creation of a new node.
 */
class NodeInsertNotifyDiscordChannelSubscriber implements EventSubscriberInterface
{

  /**
   * Log the creation of a new node.
   *
   * @param \Drupal\custom_notify_discord_channel\Event\NodeInsertNotifyDiscordChannelEvent $event
   */
  public function onNotifyDiscordChannelNodeInsert(NodeInsertNotifyDiscordChannelEvent $event)
  {
    $entity = $event->getEntity();
    $config = \Drupal::config('custom_notify_discord_channel.adminsettings');

    //=======================================================================================================
    // Create new webhook in your Discord channel settings and copy&paste URL
    //=======================================================================================================
    $webhookurl = $config->get("custom_notify_discord_channel_url_webhook");

    //=======================================================================================================
    // Compose message. You can use Markdown
    // Message Formatting -- https://discordapp.com/developers/docs/reference#message-formatting
    //========================================================================================================

    //usuario actual
    $current_user = \Drupal::currentUser();

    $encode_array = [

      // Username
      "username" => $current_user->getAccountName(),

      // Text-to-speech
      "tts" => false,

    ];

    if (!$config->get('custom_notify_discord_channel_' . $entity->bundle() . '_create_preview')) {

      //$content = "https://www.caribbeannewsdigital.com/es/turismo/corea-del-sur-tambien-le-declara-la-guerra-las-chinches";
      $encode_array["content"] = $entity->toUrl()->setAbsolute()->toString(); // Message

    } else {

      // Get the definitions
      $definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', $entity->bundle());

      $timestamp = date("c", $entity->getCreatedTime());

      $encode_array["content"] = 'Se ha creado un contenido nuevo con id: ' . $entity->id();
      $encode_array["embeds"][0]["title"] = $entity->label(); // Embed Title
      $encode_array["embeds"][0]["type"] = "rich"; // Embed Type
      $encode_array["embeds"][0]["url"] = $entity->toUrl()->setAbsolute()->toString(); // URL of title link
      $encode_array["embeds"][0]["timestamp"] = $timestamp; // Timestamp of embed must be formatted as ISO8601

      if (!empty($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_color_left_general'))) {
        $encode_array["embeds"][0]["color"] = hexdec($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_color_left_general'));
      } else {
        $encode_array["embeds"][0]["color"] = hexdec("3366ff"); // Embed left border color in HEX
      }

      if (!is_null($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_description_name_field'))
        && !empty($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_description_name_field'))) {

        if (in_array($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_description_name_field'), array_keys($definitions))
          && isset($entity->get($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_description_name_field'))->value)
          && !is_null($entity->get($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_description_name_field'))->value)
          && !empty($entity->get($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_description_name_field'))->value)) {

          $description = strip_tags((string)$entity->get($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_description_name_field'))->value);
          $description = substr($description, 0, 150);

          // Embed Description
          $encode_array["embeds"][0]["description"] = $description;

        }
      }

      if (!is_null($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_image_name_field'))
        && !empty($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_image_name_field'))) {

        $image_url = NULL;

        if (in_array($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_image_name_field'), array_keys($definitions))) {

          if (isset($entity->get($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_image_name_field'))->entity)) {

            if (!$config->get('custom_notify_discord_channel_' . $entity->bundle() . '_image_use_media_name_field')) {

              if (!is_null($entity->get($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_image_name_field'))->entity->getFileUri())
                && !empty($entity->get($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_image_name_field'))->entity->getFileUri())) {

                $image_uri = $entity->get($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_image_name_field'))->entity->getFileUri();

                $image_url = \Drupal::service('file_url_generator')->generateAbsoluteString($image_uri);                

              }

            } else {

              if (isset($entity->get($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_image_name_field'))->entity->field_media_image->entity)
                && !is_null($entity->get($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_image_name_field'))->entity->field_media_image->entity)
                && !is_null($entity->get($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_image_name_field'))->entity->field_media_image->entity->getFileUri())
                && !empty($entity->get($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_image_name_field'))->entity->field_media_image->entity->getFileUri())) {

                $image_uri = $entity->get($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_image_name_field'))->entity->field_media_image->entity->getFileUri();

                $image_url = \Drupal::service('file_url_generator')->generateAbsoluteString($image_uri);

              }

            }

          }

        }

        // Image to send
        if (!is_null($image_url)) {
          $encode_array["embeds"][0]["image"]["url"] = $image_url;
        }
      }

      if (!is_null($config->get('custom_notify_discord_channel_user_author_name_field'))
        && !empty($config->get('custom_notify_discord_channel_user_author_name_field'))) {

        // Get the definitions user
        $definitions_user = \Drupal::service('entity_field.manager')->getFieldDefinitions('user', 'user'); //campos del usuario

        $user_login = User::load($current_user->id());

        if (in_array($config->get('custom_notify_discord_channel_user_author_name_field'), array_keys($definitions_user))
          && isset($user_login->get($config->get('custom_notify_discord_channel_user_author_name_field'))->value)
          && !is_null($user_login->get($config->get('custom_notify_discord_channel_user_author_name_field'))->value)
          && !empty($user_login->get($config->get('custom_notify_discord_channel_user_author_name_field'))->value)) {

          // author
          $encode_array["embeds"][0]["author"]["name"] = $user_login->get($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_author_name_field'))->value;

        }
      }

      if (!is_null($config->get('custom_notify_discord_channel_user_author_url_name_field'))
        && !empty($config->get('custom_notify_discord_channel_user_author_url_name_field'))) {

        // Get the definitions user
        $definitions_user = \Drupal::service('entity_field.manager')->getFieldDefinitions('user', 'user'); //campos del usuario

        if (in_array($config->get('custom_notify_discord_channel_user_author_url_name_field'), array_keys($definitions_user))) {

          $link = NULL;

          if ($definitions_user[$config->get('custom_notify_discord_channel_user_author_url_name_field')]->getType() === "link") {

            $user_login = User::load($current_user->id());

            if (isset($user_login->get($config->get('custom_notify_discord_channel_user_author_url_name_field'))->getValue()[0]['uri'])
              && !is_null($user_login->get($config->get('custom_notify_discord_channel_user_author_url_name_field'))->getValue()[0]['uri'])
              && !empty($user_login->get($config->get('custom_notify_discord_channel_user_author_url_name_field'))->getValue()[0]['uri'])) {

              // author link
              $uri = $user_login->get($config->get('custom_notify_discord_channel_user_author_url_name_field'))->getValue()[0]['uri'];
              $link = Url::fromUri($uri, ['absolute' => TRUE])->toString();

            }

          } elseif ($definitions_user[$config->get('custom_notify_discord_channel_user_author_url_name_field')]->getType() === "string") {
            if (isset($user_login->get($config->get('custom_notify_discord_channel_user_author_url_name_field'))->value)
              && !is_null($user_login->get($config->get('custom_notify_discord_channel_user_author_url_name_field'))->value)
              && !empty($user_login->get($config->get('custom_notify_discord_channel_user_author_url_name_field'))->value)) {

              // author link
              $link = $user_login->get($config->get('custom_notify_discord_channel_user_author_url_name_field'))->value;

            }
          }

          if (!is_null($link)) {
            $encode_array["embeds"][0]["author"]["url"] = $link;
          }
        }

      }

      if (!is_null($config->get('custom_notify_discord_channel_user_footer_text_name_field'))
        && !empty($config->get('custom_notify_discord_channel_user_footer_text_name_field'))) {

        // Get the definitions user
        $definitions_user = \Drupal::service('entity_field.manager')->getFieldDefinitions('user', 'user'); //campos del usuario

        $user_login = User::load($current_user->id());

        if (in_array($config->get('custom_notify_discord_channel_user_footer_text_name_field'), array_keys($definitions_user))
          && isset($user_login->get($config->get('custom_notify_discord_channel_user_footer_text_name_field'))->value)
          && !is_null($user_login->get($config->get('custom_notify_discord_channel_user_footer_text_name_field'))->value)
          && !empty($user_login->get($config->get('custom_notify_discord_channel_user_footer_text_name_field'))->value)) {

          // Footer text
          $encode_array["embeds"][0]["footer"]["text"] = $user_login->get($config->get('custom_notify_discord_channel_' . $entity->bundle() . '_footer_text_name_field'))->value;

        }
      }

      if (!is_null($config->get('custom_notify_discord_channel_user_footer_image_name_field'))
        && !empty($config->get('custom_notify_discord_channel_user_footer_image_name_field'))) {

        $image_user_url = NULL;

        // Get the definitions user
        $definitions_user = \Drupal::service('entity_field.manager')->getFieldDefinitions('user', 'user'); //campos del usuario

        if (in_array($config->get('custom_notify_discord_channel_user_footer_image_name_field'), array_keys($definitions_user))) {

          // author
          $user_login = User::load($current_user->id());

          if (isset($user_login->get($config->get('custom_notify_discord_channel_user_footer_image_name_field'))->entity)) {

            if (!$config->get('custom_notify_discord_channel_user_footer_image_use_media_name_field')) {

              if (!is_null($user_login->get($config->get('custom_notify_discord_channel_user_footer_image_name_field'))->entity->getFileUri())
                && !empty($user_login->get($config->get('custom_notify_discord_channel_user_footer_image_name_field'))->entity->getFileUri())) {

                $image_uri = $user_login->get($config->get('custom_notify_discord_channel_user_footer_image_name_field'))->entity->getFileUri();

                $image_user_url = \Drupal::service('file_url_generator')->generateAbsoluteString($image_uri);                

              }

            } else {

              if (isset($user_login->get($config->get('custom_notify_discord_channel_user_footer_image_name_field'))->entity->field_media_image->entity)
                && !is_null($user_login->get($config->get('custom_notify_discord_channel_user_footer_image_name_field'))->entity->field_media_image->entity)
                && !is_null($user_login->get($config->get('custom_notify_discord_channel_user_footer_image_name_field'))->entity->field_media_image->entity->getFileUri())
                && !empty($user_login->get($config->get('custom_notify_discord_channel_user_footer_image_name_field'))->entity->field_media_image->entity->getFileUri())) {

                $image_uri = $user_login->get($config->get('custom_notify_discord_channel_user_footer_image_name_field'))->entity->field_media_image->entity->getFileUri();

                $image_user_url = \Drupal::service('file_url_generator')->generateAbsoluteString($image_uri);

              }

            }

          }

        }

        // Footer icon image
        if (!is_null($image_user_url)) {
          $encode_array["embeds"][0]["footer"]["icon_url"] = $image_user_url;
        }
      }
    }

    $json_data = Json::encode($encode_array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    $ch = curl_init($webhookurl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch);

    // If you need to debug, or find out why you can't send message uncomment line below, and execute script.
    // echo $response;
    curl_close($ch);

    /*\Drupal::logger('event_subscriber_notify_discord_channel')->notice('New @type: @title. Created by: @owner. @link',
      array(
        '@type' => $entity->getType(),
        '@title' => $entity->label(),
        '@owner' => $entity->getOwner()->getDisplayName(),
        '@link' => $entity->toUrl('canonical')->toString(TRUE)->getGeneratedUrl()
      ));*/
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents()
  {
    $events[NodeInsertNotifyDiscordChannelEvent::NOTIFY_DISCORD_CHANNEL_NODE_INSERT][] = ['onNotifyDiscordChannelNodeInsert'];
    return $events;
  }

}
