<?php
namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\PostFacade;

final class EditPresenter extends Nette\Application\UI\Presenter
{
	private PostFacade $facade;
	/*private Nette\Database\Explorer $database;*/

	public function __construct(PostFacade $facade)
	{
		$this->facade = $facade;
	}

	public function startup(): void
{
	parent::startup();

	if (!$this->getUser()->isLoggedIn()) {
		$this->redirect('Sign:in');
	}
}

protected function createComponentPostForm(): Form
{
	$form = new Form;
	$form->addText('title', 'Titulek:')
		->setRequired();
	$form->addTextArea('content', 'Obsah:')
		->setRequired();

	$form->addSubmit('send', 'Uložit a publikovat');
	$form->onSuccess[] = [$this, 'postFormSucceeded'];

	return $form;
}
public function postFormSucceeded(array $data): void
{
	$postId = $this->getParameter('postId');

	if ($postId) {

		$post = $this->facade->editPost($postId, $data);

	} else {
		$post = $this->facade->insertPost($data);
	}

	$this->flashMessage('Příspěvek byl úspěšně publikován.', 'success');
	$this->redirect('Post:show', $post->id);
}

public function renderEdit(int $postId): void
{
	$post = $this->facade->getPostById($postId);


	if (!$post) {
		$this->error('Post not found');
	}

	$this->getComponent('postForm')
		->setDefaults($post->toArray());
}

}
