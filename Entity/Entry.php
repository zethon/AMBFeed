<?php

namespace lulzapps\Feed\Entity;

use XF\Mvc\Entity\Structure;

class Entry extends \XF\Mvc\Entity\Entity
{

public static function getStructure(Structure $structure)
{
    $structure->table = 'lulzapps_feed_entry';
    $structure->shortName = 'lulzapps\Feed:Feed';
    $structure->primaryKey = 'entry_id';
    $structure->columns = 
        [
            'entry_id' => ['type' => self::UINT, 'nullable' => true, 'autoIncrement' => true, 'required' => false],
            'user_id' => ['type' => self::UINT, 'required' => true],
            'comment' => ['type' => self::STR, 'required' => true, 'maxLength' => 255],
            'date' => ['type' => self::UINT, 'default' => time()]
        ];
    $structure->getters = [];
    $structure->relations['User'] = 
        [
            'entity' => 'XF:User',
            'type' => self::TO_ONE,
            'conditions' => 'user_id',
            'primary' => true
        ];

    return $structure;
}

}