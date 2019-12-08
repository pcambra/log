<?php

namespace Drupal\log\Entity;

use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the Log entity.
 *
 * @ingroup log
 *
 * @ContentEntityType(
 *   id = "log",
 *   label = @Translation("Log"),
 *   bundle_label = @Translation("Log type"),
 *   label_collection = @Translation("Logs"),
 *   label_singular = @Translation("log"),
 *   label_plural = @Translation("logs"),
 *   label_count = @PluralTranslation(
 *     singular = "@count log",
 *     plural = "@count logs",
 *   ),
 *   handlers = {
 *     "access" = "\Drupal\entity\EntityAccessControlHandler",
 *     "list_builder" = "Drupal\log\LogListBuilder",
 *     "permission_provider" = "\Drupal\entity\EntityPermissionProvider",
 *     "storage_schema" = "Drupal\log\LogStorageSchema",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "add" = "Drupal\log\Form\LogAddForm",
 *       "edit" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\entity\Routing\AdminHtmlRouteProvider",
 *       "revision" = "\Drupal\entity\Routing\RevisionRouteProvider",
 *       "delete-multiple" = "\Drupal\entity\Routing\DeleteMultipleRouteProvider",
 *     },
 *     "local_action_provider" = {
 *       "collection" = "\Drupal\entity\Menu\EntityCollectionLocalActionProvider",
 *     },
 *     "local_task_provider" = {
 *       "default" = "\Drupal\entity\Menu\DefaultEntityLocalTaskProvider",
 *     },
 *   },
 *   base_table = "log",
 *   revision_table = "log_revision",
 *   translatable = TRUE,
 *   revisionable = TRUE,
 *   show_revision_ui = TRUE,
 *   admin_permission = "administer log",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "owner" = "uid",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   bundle_entity_type = "log_type",
 *   field_ui_base_route = "entity.log_type.edit_form",
 *   common_reference_target = TRUE,
 *   permission_granularity = "bundle",
 *   links = {
 *     "canonical" = "/log/{log}",
 *     "add-page" = "/log/add",
 *     "add-form" = "/log/add/{log_type}",
 *     "collection" = "/admin/content/logs",
 *     "delete-form" = "/log/{log}/delete",
 *     "delete-multiple-form" = "/log/delete",
 *     "edit-form" = "/log/{log}/edit",
 *     "revision" = "/log/{log}/revisions/{log_revision}/view",
 *     "revision-revert-form" = "/log/{log}/revisions/{log_revision}/revert",
 *     "version-history" = "/log/{log}/revisions",
 *   }
 * )
 */
class Log extends EditorialContentEntityBase implements LogInterface {

  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    // Set default value for name and done properties.
    if (!empty($values['type'])) {
      $type = \Drupal::entityTypeManager()
        ->getStorage('log_type')
        ->load($values['type']);
      $values += [
        'name' => $type->getNamePattern(),
        'done' => $type->isAutomaticallyDone(),
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    $type = \Drupal::entityTypeManager()
      ->getStorage('log_type')
      ->load($this->bundle());
    if (!$type->isNameEditable() && $this->isNew()) {
      // Pass in an empty bubbleable metadata object, so we can avoid starting a
      // renderer, for example if this happens in a REST resource creating
      // context.
      $bubbleable_metadata = new BubbleableMetadata();
      $this->set('name', \Drupal::token()->replace(
        $type->getNamePattern(),
        ['log' => $this],
        [],
        $bubbleable_metadata
      ));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    return $this->getName();
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function getCurrentUserId() {
    return [\Drupal::currentUser()->id()];
  }

  /**
   * {@inheritdoc}
   */
  public static function getRequestTime() {
    return \Drupal::time()->getRequestTime();
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields += static::ownerBaseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setDefaultValue('')
      ->setSetting('max_length', 255)
      ->setSetting('text_processing', 0)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Log entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDefaultValueCallback('Drupal\log\Entity\Log::getCurrentUserId')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 99,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 99,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the log was created.'))
      ->setRevisionable(TRUE)
      ->setDefaultValueCallback(static::class . '::getRequestTime')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'timestamp',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 99,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time the log was last edited.'))
      ->setRevisionable(TRUE);

    $fields['timestamp'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Date'))
      ->setDescription(t('Timestamp of the event being logged.'))
      ->setDefaultValueCallback(static::class . '::getRequestTime')
      ->setRevisionable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 10,
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 80,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['done'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Done'))
      ->setDescription(t('Boolean indicating whether the log is done (the event happened).'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'boolean',
        'weight' => 10,
      ])
      ->setDisplayOptions('form', [
        'settings' => ['display_label' => TRUE],
        'weight' => 90,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    return $fields;
  }

}
