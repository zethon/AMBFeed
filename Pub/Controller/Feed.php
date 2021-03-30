<?php

namespace lulzapps\Feed\Pub\Controller;

class Feed extends \XF\Pub\Controller\AbstractController
{

public function actionIndex()
{
    $viewParams = [];
    return $this->view('lulzapps\Feed:View', 'lulzapps_feed_view', $viewParams);
}

}