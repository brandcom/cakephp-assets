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
use Laminas\Diactoros\UploadedFile;

/**
 * Assets Model
 *
 * @method \Assets\Model\Entity\Asset newEmptyEntity()
 * @method \Assets\Model\Entity\Asset newEntity(array $data, array $options = [])
 * @method \Assets\Model\Entity\Asset[] newEntities(array $data, array $options = [])
 * @method \Assets\Model\Entity\Asset get($primaryKey, $options = [])
 * @method \Assets\Model\Entity\Asset findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \Assets\Model\Entity\Asset patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Assets\Model\Entity\Asset[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \Assets\Model\Entity\Asset|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Assets\Model\Entity\Asset saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Assets\Model\Entity\Asset[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \Assets\Model\Entity\Asset[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \Assets\Model\Entity\Asset[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \Assets\Model\Entity\Asset[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AssetsTable extends Table
{
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
        $this->setDisplayField(Configure::read('AssetsPlugin.AssetsTable.displayField', 'title'));
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->addBehavior('Josegonzalez/Upload.Upload', [
            'filename' => [
                'nameCallback' => function ($table, EntityInterface $entity, UploadedFile $data, $field, $settings
                ): string {

                    if (method_exists($this, 'fileNameCallback')) {
                        return $this->fileNameCallback($table, $entity, $data, $field, $settings);
                    }

                    $now = new FrozenTime();
                    $pathInfo = pathinfo((string)$data->getClientFilename());

                    return $now->format('ymd') . '-' . $now->format('His') . '_' . $pathInfo['basename'];
                },
                'fields' => [
                    'dir' => 'directory',
                    'size' => 'filesize',
                    'type' => 'mimetype',
                ],
                'path' => self::getAssetsDir(),
            ],
        ]);

        $this->addCustomBehaviors();
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

    public static function getAssetsDir(): string
    {
        return Configure::read('AssetsPlugin.AssetsTable.assetsDir');
    }

    /**
     * Example:
     * Pass your AssetFileNameBehavior to the table, where you define a fileNameCallback() method
     * to manipulate how your uploaded files will be renamed in the Josegonzalez/UploadBehavior.
     * See AssetsTable::initialize()
     *
     * You can also define methods like beforeFind() or afterSave()
     * @link https://book.cakephp.org/4/en/orm/table-objects.html#lifecycle-callbacks
     * @link https://book.cakephp.org/4/en/orm/behaviors.html
     */
    private function addCustomBehaviors()
    {
        $customBehaviors = Configure::read('AssetsPlugin.AssetsTable.Behaviors') ?? [];
        foreach ($customBehaviors as $behavior) {
            $this->addBehavior($behavior);
        }
    }
}
