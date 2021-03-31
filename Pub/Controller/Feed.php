<?php

namespace lulzapps\Feed\Pub\Controller;

class Feed extends \XF\Pub\Controller\AbstractController
{

public function actionIndex()
{
    $repo = $this->repository('lulzapps\Feed:Entry');
    $finder = $repo->findEntriesForFeedView();

    $viewParams = [ 'feedEntries' => $finder->fetch() ];

    return $this->view('lulzapps\Feed:View', 'lulzapps_feed_view', $viewParams);
}

}