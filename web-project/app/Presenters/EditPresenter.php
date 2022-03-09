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
	$form->addUpload('image', 'Soubor')
		->setRequired()
		->addRule(Form::IMAGE, 'Thumbnail must be JPEG, PNG or GIF');
	$form->addSubmit('send', 'Uložit a publikovat');
	$form->onSuccess[] = [$this, 'postFormSucceeded'];

	return $form;
}
public function postFormSucceeded($form, $data): void
{
	$postId = $this->getParameter('postId');
	if (filesize($data->image->size) > 0) {
		if ($data->image->isOk()) {
			$data->image->move('upload/' . $data->image->getSanitizedName());
			$data['image'] = ('upload/' . $data->image->getSanitizedName());
		}
	} else {
		$this->flashMessage('Soubor nebyl přidán', 'failed');
		//$this->redirect('this');
	}
	if ($postId) {

		$post = $this->facade->editPost($postId, (array) $data);

	} else {
		$post = $this->facade->insertPost((array) $data);
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
