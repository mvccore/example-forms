<?php

namespace App\Controllers;

class Index extends Base {

	/**
	 * Render homepage with registered users.
	 * @return void
	 */
	public function IndexAction () {
		$this->view->title = 'Users';
		
		$orderDir = $this->GetParam('order', 'a-z', 'desc');
		$users = \App\Models\User::GetAll('created', $orderDir);
		$this->view->users = $users;

		$defaultOrder = $orderDir == 'desc';
		$this->view->orderLinkText = $defaultOrder
			? 'From oldest'
			: 'From newest';
		$this->view->orderLinkValue = $this->Url(
			'self', ['order' => $defaultOrder ? 'asc' : 'desc']
		);
	}

	/**
	 * @return void
	 */
	public function SignInAction () {
		$this->view->title = 'Login';
		$this->view->signInForm = \MvcCore\Ext\Auths\Basic::GetInstance()
			->GetSignInForm()
			->AddCssClasses('theme')
			->SetValues([// set signed in url to blog posts list by default:
				'successUrl' => $this->Url('home', ['absolute' => TRUE]),
			]);
		$this->view->registrationLink = $this->Url('register');
	}

	/**
	 * Render not found action.
	 * @return void
	 */
	public function NotFoundAction () {
		$this->controllerName = 'front/index';
		$this->ErrorAction();
	}

	/**
	 * Render possible server error action.
	 * @return void
	 */
	public function ErrorAction () {
		$code = $this->response->GetCode();
		if ($code === 200) $code = 404;
		$message = $this->request->GetParam('message', 'a-zA-Z0-9_;, \\/\-\@\:\.');
		$message = preg_replace('#`([^`]*)`#', '<code>$1</code>', $message);
		$message = str_replace("\n", '<br />', $message);
		$this->view->title = "Error $code";
		$this->view->message = $message;
		$this->Render('error');
	}

}
