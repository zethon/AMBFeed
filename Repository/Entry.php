<?php

namespace lulzapps\Feed\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Entry extends Repository
{
    /**
     * @return Finder
     */
    public function findEntriesForFeedView($page = 1, $perPage = 20)
    {
        $visitor = \XF::visitor();

        $finder = $this->finder('lulzapps\Feed:Entry');
        $finder
            ->setDefaultOrder('date', 'DESC')
            ->with('User', false)
            ->with('Original', false)
            ->with('EntryDeleted', false)
            ->limitByPage($page, $perPage);

        return $finder;
    }
}