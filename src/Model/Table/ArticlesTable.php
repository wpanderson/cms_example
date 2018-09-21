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

// Query class
use Cake\ORM\Query;

class ArticlesTable extends Table
{
    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');

        // Allow association with database.
        // More info at https://book.cakephp.org/3.0/en/orm/associations.html
        $this->belongsToMany('Tags');
    }

    // Allows us to add or edit articles with a comma sepparated list of
    // tags for a given article.
    protected function _buildTags($tagString)
    {
        // Trim tags
        $newTags = array_map('trim', explode(',', $tagString));
        // Remove all empty tags
        $newTags = array_filter($newTags);
        // Reduce duplicated tags
        $newTags = array_unique($newTags);

        $out = [];
        $query = $this->Tags->find()
            ->where(['Tags.title IN' => $newTags]);

        // Remove existing tags from the list of new tags.
        foreach ($query->extract('title') as $existing) {
            $index = array_search($existing, $newTags);
            if ($index !== false) {
                unset($newTags[$index]);
            }
        }
        // Add existing tags.
        foreach ($query as $tag) {
            $out[] = $tag;
        }
        // Add new tags.
        foreach ($newTags as $tag) {
            $out[] = $this->Tags->newEntity(['title' => $tag]);
        }
        return $out;
    }

    // Allows adding of new Articles when a new article is entered this will
    // provide a slug value which is required.
    public function beforeSave($event, $entity, $options)
    {
        // Validate tag data.
        if ($entity->tag_string) {
            $entity->tags = $this->_buildTags($entity->tag_string);
        }

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

    // The $query argument is a query builder instance.
    // The $options array will contain the 'tags' option we passed
    // to find('tagged') in our controller action.
    public function findTagged(Query $query, array $options)
    {
        $columns = [
          'Articles.id', 'Articles.user_id', 'Articles.title',
          'Articles.body', 'Articles.published', 'Articles.created',
          'Articles.slug',
        ];

        $query = $query
            ->select($columns)
            ->distinct($columns);

        if (empty($options['tags'])) {
            // If no tags are provided, find articles that have no tags.
            $query->leftJoinWith('Tags')
                ->where(['Tags.title IN' => $options['tags']]);
        }

        return $query->group(['Articles.id']);
    }
}
