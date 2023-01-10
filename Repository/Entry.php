<?php

namespace lulzapps\Feed\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Entry extends Repository
{

    public function getEntryCount()
    {
        return $this->db()->fetchOne("SELECT COUNT(*) FROM lulzapps_feed_entry");
    }

    public function findEntriesForFeedView($page = 1, $perPage = 20)
    {
        $finder = $this->finder('lulzapps\Feed:Entry');
        $finder
            ->setDefaultOrder('date', 'DESC')
            ->with('User', false)
            ->with('EntryDeleted', false)
            ->limitByPage($page, $perPage);

        return $finder;
    }

    public function findEntriesForFeedWidget()
    {
        $finder = $this->finder('lulzapps\Feed:Entry');
        $finder
            ->setDefaultOrder('date', 'DESC')
            ->with('User', false)
            ->with('EntryDeleted', false)
            ->limit(3);

        return $finder;
    }
}