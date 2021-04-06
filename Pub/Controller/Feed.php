<?php

namespace lulzapps\Feed\Pub\Controller;

class Feed extends \XF\Pub\Controller\AbstractController
{

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
        if ($reaction['reaction'] == $reactionType)
        {
            $reaction->delete();
        }
        else
        {
            $reaction['reaction'] = $reactionType;
            $form = $this->formAction();
            $form->saveEntity($reaction)->run();
        }
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
    $user_id = (\XF::visitor())->user_id;
    if ($user_id <= 0)
    {
        return $this->error('invalid user');
    }

    $this->saveReaction('like');

    $returnUrl = $this->buildLink('feed');
    return $this->redirect($returnUrl, "Your feedback has been saved");
}

public function actionDislike()
{
    $user_id = (\XF::visitor())->user_id;
    if ($user_id <= 0)
    {
        return $this->error('invalid user');
    }

    $this->saveReaction('dislike');

    $returnUrl = $this->buildLink('feed');
    return $this->redirect($returnUrl, "Your feedback has been saved");
}

public function actionReply()
{
    $entry_id = $this->filter('entry_id', 'uint');
    $finder = $this->finder('lulzapps\Feed:Entry');
    $finder
        ->with('User')
        ->where('entry_id', $entry_id);

    $entry = $finder->fetchOne();

    $viewParams = 
        [
            'entry' => $entry,
            'submitUrl' => $this->buildLink('feed/submit')
        ];

    return $this->view('lulzapps\Feed:ReplyOverlay', 'lulzapps_reply_overlay', $viewParams);
}

public function actionDiscuss()
{
    $entry_id = $this->filter('entry_id', 'uint');
    return $this->message('actionDiscuss: ' . $entry_id);
}

public function actionDelete()
{
    $entry_id = $this->filter('entry_id', 'uint');
    $finder = $this->finder('lulzapps\Feed:Entry');
    $finder
        ->with('User')
        ->where('entry_id', $entry_id);

    $entry = $finder->fetchOne();
    if (!$entry->canDelete())
    {
        return $this->noPermission("You do not have permission to do that");
    }

    if ($this->isPost())
    {
        # $type = $this->filter('hard_delete', 'uint') ? 'hard' : 'soft';

        $input = $this->filter(
            [
                'entry_id' => 'uint',
                'user_id' => 'uint',
                'reason' => 'str'
            ]);
            
        $input['entry_id'] = $entry_id;
        $input['user_id'] = (\XF::visitor())->user_id;
        $input['reason'] = $this->filter('reason', 'str');

        $reaction = $this->em()->create('lulzapps\Feed:EntryDeleted');
        $form = $this->formAction();
        $form->basicEntitySave($reaction, $input)->run();

        $entry->fastUpdate('deleted', true);

        $returnUrl = $this->buildLink('feed');
        return $this->redirect($returnUrl, "The message as been deleted");
    }
    else
    {
        $confirmUrl = $this->buildLink('feed/delete', null);
        $viewParams = 
            [
                'entry' => $entry,
                'confirmUrl' => $confirmUrl
            ];
        return $this->view('lulzapps\Feed:EntryDelete', 'lulzapps_feed_entry_delete', $viewParams);
    }
}

// http://localhost/index.php?feed/submit
public function actionSubmit()
{
    $user = \XF::visitor();
    if ($user->user_id <= 0)
    {
        throw $this->exception($this->error('Invalid user'));
    }

    $input = $this->filter([
        'user_id' => 'uint',
        'comment' => 'str',
        'reply_to' => 'uint'
    ]);

    $input['user_id'] = $user->user_id;
    $input['comment'] = $message = $this->plugin('XF:Editor')->fromInput('message');
    
    $reply_to = $this->filter('reply_to', 'uint');
    if ($reply_to && $reply_to > 0)
    {
        $input['reply_to'] = $reply_to;
    }
    else
    {
        $input['reply_to'] = 0;
    }

    $entry = $this->em()->create('lulzapps\Feed:Entry');

    $form = $this->formAction();
    $form->basicEntitySave($entry, $input)->run();
    $returnUrl = $this->buildLink('feed');
    return $this->redirect($returnUrl, "Your feedback has been saved");
}

public function actionThreadView()
{
    $visitor = \XF::visitor();
    if (!$visitor->user_id)
    {
        $returnUrl = $this->buildLink('register');
        return $this->redirect($returnUrl);
    }
    
    $entry_id = $this->filter('entry_id', 'uint');
    $finder = $this->finder('lulzapps\Feed:Entry');
    $finder
        ->with('User')
        ->with('Original')
        ->where('entry_id', $entry_id);
    
        $entry = $finder->fetchOne();

    $viewParams = 
        [ 
            'entry' => $entry

        ];
    return $this->view('lulzapps\Feed:View', 'lulzapps_feed_threadview', $viewParams);
}

public function actionIndex()
{
    $visitor = \XF::visitor();
    if (!$visitor->user_id)
    {
        $returnUrl = $this->buildLink('register');
        return $this->redirect($returnUrl);
    }

    $page = $this->filter('page', 'uint');
    $perPage = $this->options()->discussionsPerPage;

    $repo = $this->repository('lulzapps\Feed:Entry');
    $finder = $repo->findEntriesForFeedView($page, $perPage);

    $viewParams = 
        [ 
            'feedEntries' => $finder->fetch(),
            'submitUrl' => $this->buildLink('feed/submit'),
            'page' => $page,
            'perPage' => $perPage
        ];

    return $this->view('lulzapps\Feed:View', 'lulzapps_feed_view', $viewParams);
}

}