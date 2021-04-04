<?php

namespace lulzapps\Feed\Pub\Controller;

class Feed extends \XF\Pub\Controller\AbstractController
{

private function canSubmitReaction($reaction)
{
    $entry_id = $this->filter('entry_id', 'uint');
    $user_id = (\XF::visitor())->user_id;

    if ($user_id <= 0)
    {
        throw $this->exception($this->error('Invalid user'));
    }

    $finder = $this->finder('lulzapps\Feed:Entry');
    $finder->where('entry_id', $entry_id);
    $finder->where('user_id', $user_id);
    if ($finder->fetchOne())
    {
        // user is trying to like their own message
        throw $this->exception($this->error('Cannot react to own feed message'));
    }

    $finder = $this->finder('lulzapps\Feed:Reaction');
    $finder->where('entry_id', $entry_id);
    $finder->where('user_id', $user_id);
    $finder->where('reaction', $reaction);
    if ($finder->fetchOne())
    {
        // user already reacted this way to this post
        throw $this->exception($this->error('Duplicate reaction to a feed message'));
    }

    return true;
}

public function actionLike()
{
    if (!$this->canSubmitReaction('like'))
    {
        throw $this->exception($this->error('Invalid action!'));
    }

    $input = $this->filter(
        [
            'entry_id' => 'uint',
            'user_id' => 'uint',
            'reaction' => 'str'
        ]);

    $user_id = (\XF::visitor())->user_id;
    $entry_id = $this->filter('entry_id', 'uint');
    $returnUrl = $this->buildLink('feed');

    $input['entry_id'] = $entry_id;
    $input['user_id'] = $user_id;
    $input['reaction'] = 'like';
    
    $reaction = $this->em()->create('lulzapps\Feed:Reaction');

    $form = $this->formAction();
    $form->basicEntitySave($reaction, $input)->run();

    return $this->redirect($returnUrl, "Your feedback has been saved");
}

public function actionDislike()
{
    if (!$this->canSubmitReaction('dislike'))
    {
        throw $this->exception($this->error('Invalid action!'));
    }

    $input = $this->filter(
        [
            'entry_id' => 'uint',
            'user_id' => 'uint',
            'reaction' => 'str'
        ]);

    $user_id = (\XF::visitor())->user_id;
    $entry_id = $this->filter('entry_id', 'uint');
    $returnUrl = $this->buildLink('feed');

    $input['entry_id'] = $entry_id;
    $input['user_id'] = $user_id;
    $input['reaction'] = 'dislike';
    
    $reaction = $this->em()->create('lulzapps\Feed:Reaction');

    $form = $this->formAction();
    $form->basicEntitySave($reaction, $input)->run();

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