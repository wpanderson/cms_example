<?php
/**
 * Created by PhpStorm.
 * User: wpanderson
 * Date: 9/19/18
 * Time: 1:24 PM
 */
//src/Model/Entity/Article.php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Article extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'slug' => false,
    ];
}
