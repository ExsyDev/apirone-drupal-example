<?php

namespace Drupal\commerce_order;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\commerce_order\Entity\OrderType;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the list builder for orders.
 */
class OrderListBuilder extends EntityListBuilder {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs a new OrderListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityTypeManagerInterface $entity_type_manager, DateFormatterInterface $date_formatter) {
    parent::__construct($entity_type, $entity_type_manager->getStorage($entity_type->id()));

    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager'),
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = [
      'order_id' => [
        'data' => $this->t('Order ID'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
      ],
      'type' => [
        'data' => $this->t('Type'),
        'class' => [RESPONSIVE_PRIORITY_MEDIUM],
      ],
      'customer' => [
        'data' => $this->t('Customer'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
      ],
      'state' => [
        'data' => $this->t('State'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
      ],
      'created' => [
        'data' => $this->t('Created'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
      ],
    ];

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\commerce_order\Entity\OrderInterface $entity  */
    $order_type = OrderType::load($entity->bundle());
    $row = [
      'order_id' => $entity->id(),
      'type' => $order_type->label(),
      'customer' => [
        'data' => [
          '#theme' => 'username',
          '#account' => $entity->getCustomer(),
        ],
      ],
      'state' => $entity->getState()->getLabel(),
      'created' => $this->dateFormatter->format($entity->getCreatedTime(), 'short'),
    ];

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);

    /** @var \Drupal\commerce_order\Entity\OrderInterface $entity */
    if ($entity->access('view')) {
      $operations['view'] = [
        'title' => $this->t('View'),
        'weight' => 5,
        'url' => $entity->toUrl('canonical'),
      ];
    }
    if ($entity->access('update') && $entity->hasLinkTemplate('reassign-form')) {
      $operations['reassign'] = [
        'title' => $this->t('Reassign'),
        'weight' => 20,
        'url' => $this->ensureDestination($entity->toUrl('reassign-form')),
      ];
    }
    if ($entity->access('unlock')) {
      $operations['unlock'] = [
        'title' => $this->t('Unlock'),
        'weight' => 25,
        'url' => $this->ensureDestination($entity->toUrl('unlock-form')),
      ];
    }
    if ($entity->access('resend_receipt')) {
      $operations['resend_receipt'] = [
        'title' => $this->t('Resend receipt'),
        'weight' => 20,
        'url' => $this->ensureDestination($entity->toUrl('resend-receipt-form')),
      ];
    }

    return $operations;
  }

}
