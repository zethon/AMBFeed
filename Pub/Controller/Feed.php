<?php

namespace lulzapps\Feed\Pub\Controller;

class Feed extends \XF\Pub\Controller\AbstractController
{

// http://localhost/index.php?feed/submit
public function actionSubmit()
{
    // return $this->message('Hello world!111');
    $user = \XF::visitor();
    if ($user->user_id <= 0)
    {
        throw $this->exception($this->error('Invalid user'));
    }

    $input = $this->filter([
        'user_id' => 'uint',
        'comment' => 'str'
    ]);

    $input['user_id'] = $user->user_id;
    $input['comment'] = $message = $this->plugin('XF:Editor')->fromInput('message');
    $entry = $this->em()->create('lulzapps\Feed:Entry');

    $form = $this->formAction();
    $form->basicEntitySave($entry, $input)->run();
    $returnUrl = $this->buildLink('feed');
    return $this->redirect($returnUrl, "Your feedback has been saved");
}

public function actionIndex()
{
    $repo = $this->repository('lulzapps\Feed:Entry');
    $finder = $repo->findEntriesForFeedView();

    $submitUrl = $this->buildLink('feed/submit');

    $viewParams = 
        [ 
            'feedEntries' => $finder->fetch(),
            'submitUrl' => $submitUrl
        ];

    return $this->view('lulzapps\Feed:View', 'lulzapps_feed_view', $viewParams);
}

}