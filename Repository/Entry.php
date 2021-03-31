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
            ->with('User');
            // ->with('Thread.User')
            // ->with('Thread.Forum', true)
            // ->with('Thread.Forum.Node.Permissions|' . $visitor->permission_combination_id)
            // ->with('Thread.FirstPost', true)
            // ->with('Thread.FirstPost.User')
            // ->where('Thread.discussion_type', '<>', 'redirect')
            // ->where('Thread.discussion_state', 'visible');

        return $finder;
    }
}