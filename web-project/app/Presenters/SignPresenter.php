<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\UserFacade;

final class SignPresenter extends Nette\Application\UI\Presenter
{
	private UserFacade $userfacade;
	public function __construct(UserFacade $userfacade) {
		$this->userfacade = $userfacade;
	}
	protected function createComponentRegisterForm(): Form
	{
		$form = new Form;
		$form->addText('username', 'Uživatelské jméno:')
			->setRequired('Prosím vyplňte své uživatelské jméno.');

			
		$form->addEmail('email', 'E-mail:')
		->setRequired('Prosím vyplňte svůj e-mail.');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Prosím vyplňte své heslo.');

		$form->addSubmit('send', 'Registrovat');

		$form->onSuccess[] = [$this, 'registerFormSucceeded'];
		return $form;
	}

	public function registerFormSucceeded(Form $form, \stdClass $data): void
	{	
		$this->userfacade->add($data->username, $data->email, $data->password);
		$this->flashMessage('Registrace proběhla úspěšně.');
		$this->redirect('Sign:in');
	}

	protected function createComponentPasswordForm(): Form
	{
		$form = new Form;
		
		$form->addPassword('password', 'Heslo:')
			->setRequired('Prosím vyplňte nové heslo.');

		$form->onSuccess[] = [$this, 'passwordFormSucceeded'];
		return $form;
	}
	function passwordFormSucceeded(Form $form, \stdClass $values)
	{
		$this->userfacade->changePassword($this->getUser()->getId(), $values->password);
		$this->flashMessage('Heslo bylo změněno.');
	}

	protected function createComponentSignInForm(): Form
	{
		$form = new Form;
		$form->addText('username', 'Uživatelské jméno:')
			->setRequired('Prosím vyplňte své uživatelské jméno.');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Prosím vyplňte své heslo.');

		$form->addSubmit('send', 'Přihlásit');

		$form->onSuccess[] = [$this, 'signInFormSucceeded'];
		return $form;
	}

	public function signInFormSucceeded(Form $form, \stdClass $data): void
	{
		try {
			$this->getUser()->login($data->username, $data->password);
			$this->redirect('Homepage:');
		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError('Nesprávné přihlašovací jméno nebo heslo.');
		}
	}
	public function actionOut(): void
	{
		$this->getUser()->logout();
		$this->flashMessage('Odhlášení bylo úspěšné.');
		$this->redirect('Homepage:');
	}

}
