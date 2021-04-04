<?php

namespace lulzapps\Feed\Reaction;

use XF\Mvc\Entity\Structure;

class Entry extends \XF\Mvc\Entity\Entity
{

public static function getStructure(Structure $structure)
{
    $structure->table = 'lulzapps_feed_reaction';
    $structure->shortName = 'lulzapps\Feed:Reaction';
    $structure->primaryKey = 'entry_reaction_id';
    $structure->contentType = 'lulzapps_feed_reaction';
    $structure->columns = 
        [
            'entry_reaction_id' => ['type' => self::UINT, 'nullable' => true, 'autoIncrement' => true, 'required' => false],
            'entry_id' => ['type' => self::UINT, 'required' => true],
            'user_id' => ['type' => self::UINT, 'required' => true],
            'date' => ['type' => self::UINT, 'default' => time()],
            'reaction' => ['type' => self::STR, 'required' => true, 'maxLength' => 8],
        ];
    $structure->getters = [];
    $structure->relations['Entry'] = 
        [
            'entity' => 'lulzapps\Feed:Feed',
            'type' => self::TO_ONE,
            'conditions' => 'entry_id',
            'primary' => true
        ];
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