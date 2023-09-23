<?php
declare(strict_types=1);

namespace Assets\Model\Behavior;

use ArrayObject;
use Assets\Model\Entity\Asset;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior;

/**
 * BelongsToAssets behavior
 */
class BelongsToAssetsBehavior extends Behavior
{
    /**
     * Default configuration.
     *
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [];

    /**
     * @param \Cake\Event\EventInterface $event The beforeSave event
     * @param \Cake\Datasource\EntityInterface $entity The entity related to the event
     * @param \ArrayObject $options Options passed to the event
     * @return bool
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): bool
    {
        $this->handleEmptyAssets($entity);

        return true;
    }

    /**
     * @param \Cake\Datasource\EntityInterface $entity The entity
     * @return void
     */
    private function handleEmptyAssets(EntityInterface $entity): void
    {
        foreach ($entity->toArray() as $key => $item) {
            if (!is_a($entity->$key, Asset::class)) {
                continue;
            }

            /**
             * @var \Laminas\Diactoros\UploadedFile $file
             */
            $file = $item['filename'];
            if (!$file->getClientFilename()) {
                $entity->$key = null;
            }
        }
    }
}
