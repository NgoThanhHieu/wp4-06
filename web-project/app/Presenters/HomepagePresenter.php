<?php



namespace App\Presenters;

use App\Model\PostFacade;
use App\Model\UserFacade;
use Nette;



final class HomepagePresenter extends Nette\Application\UI\Presenter
{
	private PostFacade $facade;
	private UserFacade $userFacade;

	public function __construct(PostFacade $facade, /*UserFacade $userFacade*/)
	{
		$this->facade = $facade;
		#$this->userFacade = $userFacade;
	}

	public function renderDefault(): void
	{
		#$this->userFacade->add("Admin", "admin@ossp.cz" , "secret");

		$this->template->posts = $this->facade
			->getPublicArticles()
			->limit(5);
	}
}
