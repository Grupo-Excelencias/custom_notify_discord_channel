<?php

namespace Drupal\custom_notify_discord_channel\Event;

//use Symfony\Component\EventDispatcher\Event;
use Symfony\Contracts\EventDispatcher\Event;
//use Drupal\Component\EventDispatcher\Event;
use Drupal\Core\Entity\EntityInterface;

/**
 * Wraps a node insertion event for event listeners.
 */
class NodeInsertNotifyDiscordChannelEvent extends Event
{

  const NOTIFY_DISCORD_CHANNEL_NODE_INSERT = 'event_subscriber_notify_discord_channel.node.insert';

  /**
   * Node entity.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $entity;

  /**
   * Constructs a node insertion demo event object.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   */
  public function __construct(EntityInterface $entity) {
    $this->entity = $entity;
  }

  /**
   * Get the inserted entity.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   */
  public function getEntity() {
    return $this->entity;
  }

}
