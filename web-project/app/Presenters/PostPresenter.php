<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\PostFacade;

final class PostPresenter extends Nette\Application\UI\Presenter
{
	private PostFacade $facade;	/*private Nette\Database\Explorer $database;*/


	public function __construct(PostFacade $facade)
	{
		$this->facade = $facade;
	}

	public function actionShow(int $postId): void
	{
		$post = $this->facade->getPostById($postId);
		$this->getUser()->isLoggedIn();
		if (!$this->getUser()->isLoggedIn() && ($post->status == 'ARCHIVED')) {
			$this->flashMessage('Nemáš právo vidět archived, kámo!');
			$this->redirect('Sign:in');
		}
	}

	public function renderShow(int $postId): void
	{

		/*
	$this->database
		>table('posts')
		->get($postId);
*/
		$post = $this->facade
			->getPostById($postId);

		$this->facade->addView($postId);

		if (!$post) {
			$this->error('Stránka nebyla nalezena');
		}
		$this->template->post = $post;
		$this->template->comments = $this->facade->getComments($postId);
		$this->template->like = $this->facade
		->getUserRating(($postId), $this->getUser()->getId());
	}

	protected function createComponentCommentForm(): Form
	{
		$form = new Form; // means Nette\Application\UI\Form

		$form->addText('name', 'Jméno:')
			->setRequired();

		$form->addEmail('email', 'E-mail:');

		$form->addTextArea('content', 'Komentář:')
			->setRequired();

		$form->addSubmit('send', 'Publikovat komentář');
		$form->onSuccess[] = [$this, 'commentFormSucceeded'];
		return $form;
	}

	public function commentFormSucceeded(\stdClass $data): void
	{
		$postId = $this->getParameter('postId');

		$this->facade->addComment($postId, $data);
		$this->flashMessage('Děkuji za komentář', 'success');
		$this->redirect('this');
	}

	public function handleDeleteComment(int $commentId)
	{	
		$this->facade->getComment($commentId)->delete();
	}

	public function handleLike( int $like,int $postId)
	{
		if ($this->getUser()
			->isLoggedIn()
		) {
			$userId = $this->getUser()->getId();
			$this->facade->updateRating($userId, $postId, $like);
			#$this->redirect('this');
			$this->redrawControl("likee");
		}
		// budete volat PostFacade metodu updateRating

	}

}
