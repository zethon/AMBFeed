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
        // if (empty($this->contextParams['user'])) 
        // {
        //     return false;
        // }
        // /** @var \XF\Entity\User $user */
        // $user = $this->contextParams['user'];

        // $reactionsCache = $this->app->container('reactions');

        // $reactionRepository = $this->getReactionRepository();
        // $sentCounts = $reactionRepository->countReactionsForReactionUserIdTHReactPlus($user->user_id);
        // $receivedCounts = $reactionRepository->countReactionsForContentUserIdTHReactPlus($user->user_id);

        $viewParams = [
        ];
        return $this->renderer('lz_feed_preview_template', $viewParams);
    }
}