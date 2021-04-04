<?php

namespace lulzapps\Feed\Entity;

use XF\Mvc\Entity\Structure;

class Entry extends \XF\Mvc\Entity\Entity
{

public function getLikesCount()
{
    return $this->db()->fetchOne("
        SELECT COUNT(*)
        FROM 
            lulzapps_feed_reaction
        WHERE 
            entry_id = ?
            AND reaction='like'
    ", 
    $this->entry_id);
}

public function getDislikesCount()
{
    return $this->db()->fetchOne("
        SELECT COUNT(*)
        FROM 
            lulzapps_feed_reaction
        WHERE 
            entry_id = ?
            AND reaction='dislike'
    ", 
    $this->entry_id);
}

public function getRepliesCount()
{
    return $this->db()->fetchOne("
        SELECT COUNT(*)
        FROM 
            lulzapps_feed_entry
        WHERE 
            reply_to = ?
    ", 
    $this->entry_id);
}

public static function getStructure(Structure $structure)
{
    $structure->table = 'lulzapps_feed_entry';
    $structure->shortName = 'lulzapps\Feed:Feed';
    $structure->primaryKey = 'entry_id';
    $structure->contentType = 'lulzapps_feed_entry';
    $structure->columns = 
        [
            'entry_id' => ['type' => self::UINT, 'nullable' => true, 'autoIncrement' => true, 'required' => false],
            'user_id' => ['type' => self::UINT, 'required' => true],
            'comment' => ['type' => self::STR, 'required' => true, 'maxLength' => 255],
            'date' => ['type' => self::UINT, 'default' => time()],
            'reply_to' => ['type' => self::UINT, 'required' => false],
        ];
    $structure->getters = 
        [
            'likes_count' => true,
            'dislikes_count' => true,
            'replies_count' => true
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