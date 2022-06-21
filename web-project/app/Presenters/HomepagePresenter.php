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
	
	public function handleShowRandomnumber() {
		$this->template->randomnumber = rand(1, 100);
		$this->redrawControl();
	}

	public function renderDefault(int $page = 1): void
	{
			// Zjistíme si celkový počet publikovaných článků
			$articlesCount = $this->facade->getPublishedArticlesCount();

			// Vyrobíme si instanci Paginatoru a nastavíme jej
			$paginator = new Nette\Utils\Paginator;
			$paginator->setItemCount($articlesCount); // celkový počet článků
			$paginator->setItemsPerPage(3); // počet položek na stránce
			$paginator->setPage($page); // číslo aktuální stránky
	
			// Z databáze si vytáhneme omezenou množinu článků podle výpočtu Paginatoru
			$posts = $this->facade->findPublishedArticles($paginator->getLength(), $paginator->getOffset());
	
			// kterou předáme do šablony
			$this->template->posts = $posts;
			// a také samotný Paginator pro zobrazení možností stránkování
			$this->template->paginator = $paginator;

		#$this->userFacade->add("Admin", "admin@ossp.cz" , "secret");
		$this->template->refreshNumber = rand(1,55);

		/*$this->template->posts = $this->facade
			->getPublicArticles()
			->limit(10);
			*/
	}
	
	public function renderCategory(int $categoryId): void
	{
		$category = $this->facade->getCategoryName($categoryId);
		$posts = $this->facade->getPostsByCategoryId($categoryId);
		$this->template->category = $category;
		$this->template->posts = $posts;
	}
}
