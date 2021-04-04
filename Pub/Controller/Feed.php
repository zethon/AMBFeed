<?php

namespace lulzapps\Feed\Pub\Controller;

class Feed extends \XF\Pub\Controller\AbstractController
{

private function canSubmitReaction()
{
    $returnUrl = $this->buildLink('feed');
    $entry_id = $this->filter('entry_id', 'uint');
    $user_id = (\XF::visitor())->user_id;
    if ($user_id <= 0)
    {
        return array(false, 'Invalid user');
    }

    $finder = $this->finder('lulzapps\Feed:Entry');
    $finder->where('entry_id', $entry_id);
    $finder->where('user_id', $user_id);
    if ($finder->fetchOne())
    {
        // user is trying to like their own message
        return array(false, 'Cannot react to own feed message');
    }

    return array(true, '');
}

private function saveReaction($reactionType)
{
    $user_id = (\XF::visitor())->user_id;
    $entry_id = $this->filter('entry_id', 'uint');

    $finder = $this->finder('lulzapps\Feed:Reaction');
    $finder->where('entry_id', $entry_id);
    $finder->where('user_id', $user_id);
    $reaction = $finder->fetchOne();
    if ($reaction)
    {
        $reaction['reaction'] = $reactionType;
        $form = $this->formAction();
        $form->saveEntity($reaction)->run();
    }
    else
    {
        $input = $this->filter(
            [
                'entry_id' => 'uint',
                'user_id' => 'uint',
                'reaction' => 'str'
            ]);
            
        $input['entry_id'] = $entry_id;
        $input['user_id'] = $user_id;
        $input['reaction'] = $reactionType;

        $reaction = $this->em()->create('lulzapps\Feed:Reaction');
        $form = $this->formAction();
        $form->basicEntitySave($reaction, $input)->run();
    }
}

public function actionLike()
{
    list($result, $errortxt) = $this->canSubmitReaction();
    if (!$result)
    {
        return $this->message($errortxt);
    }

    $this->saveReaction('like');

    $returnUrl = $this->buildLink('feed');
    return $this->redirect($returnUrl, "Your feedback has been saved");
}

public function actionDislike()
{
    list($result, $errortxt) = $this->canSubmitReaction();
    if (!$result)
    {
        return $this->message($errortxt);
    }

    $this->saveReaction('dislike');

    $returnUrl = $this->buildLink('feed');
    return $this->redirect($returnUrl, "Your feedback has been saved");
}

public function actionReply()
{
    $entry_id = $this->filter('entry_id', 'uint');
    return $this->message('actionReply: ' . $entry_id);
}

public function actionDiscuss()
{
    $entry_id = $this->filter('entry_id', 'uint');
    return $this->message('actionDiscuss: ' . $entry_id);
}

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