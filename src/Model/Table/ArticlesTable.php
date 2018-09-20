<?php
/**
 * Created by PhpStorm.
 * User: wpanderson
 * Date: 9/19/18
 * Time: 1:22 PM
 */
// src/Model/Table/Articles.php
namespace App\Model\Table;

// Allow class access to the validator.
use Cake\Validation\Validator;

use Cake\ORM\Table;
// the Text class
use Cake\Utility\Text;

class ArticlesTable extends Table
{
    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
    }

    // Allows adding of new Articles when a new article is entered this will
    // provide a slug value which is required.
    public function beforeSave($event, $entity, $options)
    {
        if ($entity->isNew() && !$entity->slug) {
            $sluggedTitle = Text::slug($entity->title);
            // trim slug to maximum length defied in schema
            $entity->slug = substr($sluggedTitle, 0, 191);
        }
    }

    // Allows Validation of Table entities by default when the save() function is called.
    // For further examples on validation see:
    // https://book.cakephp.org/3.0/en/orm/validation.html#validating-request-data
    public function validationDefault(Validator $validator)
    {
        // Validate title and body so they can't be empty and validate input length.
        $validator
            ->notEmpty('title')
            ->minLength('title', 10)
            ->maxLength('title', 255)

            ->notEmpty('body')
            ->minLength('body', 10);

        return $validator;
    }
}
