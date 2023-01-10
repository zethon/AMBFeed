<?php

namespace lulzapps\Feed\Widget;

use XF\Widget\AbstractWidget;

class FeedPreview extends AbstractWidget
{
    /**
     * @return bool|\XF\Widget\WidgetRenderer
     */
    public function render()
    {
        $repo = $this->repository('lulzapps\Feed:Entry');
        $finder = $repo->findEntriesForFeedWidget();
        $viewParams = 
            [ 
                'feedEntries' => $finder->fetch(),
            ];
    
        return $this->renderer('lz_feed_preview_template', $viewParams);
    }
}