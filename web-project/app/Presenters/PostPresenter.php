<?php
namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

final class PostPresenter extends Nette\Application\UI\Presenter
{
	private Nette\Database\Explorer $database;

	public function __construct(Nette\Database\Explorer $database)
	{
		$this->database = $database;
	}

	public function renderShow(int $postId): void
	{
		$this->template->post = $this->database
			->table('posts')
			->get($postId);
	}
}
?>