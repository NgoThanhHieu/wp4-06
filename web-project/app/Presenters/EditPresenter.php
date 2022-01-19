<?php
namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

final class EditPresenter extends Nette\Application\UI\Presenter
{
	private Nette\Database\Explorer $database;

	public function __construct(Nette\Database\Explorer $database)
	{
		$this->database = $database;
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
		$post = $this->database
			->table('posts')
			->get($postId);
		$post->update($data);

	} else {
		$post = $this->database
			->table('posts')
			->insert($data);
	}

	$this->flashMessage('Příspěvek byl úspěšně publikován.', 'success');
	$this->redirect('Post:show', $post->id);
}

public function renderEdit(int $postId): void
{
	$post = $this->database
		->table('posts')
		->get($postId);

	if (!$post) {
		$this->error('Post not found');
	}

	$this->getComponent('postForm')
		->setDefaults($post->toArray());
}

}
