<?php
declare(strict_types=1);

namespace Assets\Model\Table;

use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\I18n\FrozenTime;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AssetsAssets Model
 *
 * @method \Assets\Model\Entity\AssetsAsset newEntity(array $data, array $options = [])
 * @method \Assets\Model\Entity\AssetsAsset[] newEntities(array $data, array $options = [])
 * @method \Assets\Model\Entity\AssetsAsset get($primaryKey, $options = [])
 * @method \Assets\Model\Entity\AssetsAsset findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \Assets\Model\Entity\AssetsAsset patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Assets\Model\Entity\AssetsAsset[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \Assets\Model\Entity\AssetsAsset|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Assets\Model\Entity\AssetsAsset saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Assets\Model\Entity\AssetsAsset[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \Assets\Model\Entity\AssetsAsset[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \Assets\Model\Entity\AssetsAsset[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \Assets\Model\Entity\AssetsAsset[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AssetsAssetsTable extends Table
{
    const ASSETS_DIR = "resources" . DS . "assets" . DS;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('assets_assets');
        $this->setDisplayField(Configure::read('AssetsPlugin.AssetsTable.DisplayField', 'title'));
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->addBehavior('Josegonzalez/Upload.Upload', [
            'filename' => [
                'nameCallback' => function ($table, EntityInterface $entity, array $data, $field, $settings
                ): string {

                    if (method_exists($this, 'fileNameCallback')) {
                        return $this->fileNameCallback($table, $entity, $data, $field, $settings);
                    }

                    $now = new FrozenTime();
                    $pathInfo = pathinfo((string)$data['name']);

                    return $now->format('ymd') . '-' . $now->format('His') . '_' . $pathInfo['basename'];
                },
                'fields' => [
                    'dir' => 'directory',
                    'size' => 'filesize',
                    'type' => 'mimetype',
                ],
                'path' => self::ASSETS_DIR,
            ],
        ]);

        foreach (Configure::read('AssetsPlugin.AssetsTable.Behaviors') ?? []  as $behavior) {
            $this->addBehavior($behavior);
        }
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->uuid('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->notEmptyString('title');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->scalar('category')
            ->maxLength('category', 255)
            ->allowEmptyString('category');

        $validator
            ->scalar('directory')
            ->maxLength('directory', 255)
            ->notEmptyString('directory');

        $validator
            ->scalar('mimetype')
            ->maxLength('mimetype', 255)
            ->notEmptyString('mimetype');

        $validator
            ->scalar('filesize')
            ->maxLength('filesize', 255)
            ->notEmptyString('filesize');

        return $validator;
    }

    public function beforeFind(EventInterface $e, Query $query, \ArrayObject $options, $primary)
    {
        return $query->orderDesc('modified');
    }
}
