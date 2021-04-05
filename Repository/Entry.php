<?php

namespace lulzapps\Feed\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Entry extends Repository
{
    /**
     * @return Finder
     */
    public function findEntriesForFeedView()
    {
        $visitor = \XF::visitor();

        $finder = $this->finder('lulzapps\Feed:Entry');
        $finder
            ->setDefaultOrder('date', 'DESC')
            ->with('User')
            ->with('Original', false);

        return $finder;
    }
}