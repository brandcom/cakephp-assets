<?php
declare(strict_types=1);

namespace Assets\Model\Behavior;

use Assets\Model\Entity\Asset;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior;
use Laminas\Diactoros\UploadedFile;

/**
 * BelongsToAssets behavior
 */
class BelongsToAssetsBehavior extends Behavior
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function beforeSave(EventInterface $event, EntityInterface $entity, $options): bool
    {
        $this->handleEmptyAssets($entity);

        return true;
    }

    private function handleEmptyAssets(EntityInterface $entity)
    {
        foreach ($entity->toArray() as $key => $item) {
            if (!is_a($entity->$key, Asset::class)) {
                continue;
            }

            /**
             * @var UploadedFile $file
             */
            $file = $item['filename'];
            if (!$file->getClientFilename()) {
                $entity->$key = null;
            }
        }
    }
}
