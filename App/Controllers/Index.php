<?php

namespace App\Controllers;

class Index extends Base {

	protected $autoInitProperties = TRUE;

	/**
	 * @autoInit createRegisterForm, 0
	 * @var \App\Forms\UserRegistration
	 */
	protected $registerForm;

	/**
	 * Render homepage with registration form.
	 * @return void
	 */
	public function IndexAction () {
		$this->view->title = 'Registration';
		$this->view->registerForm = $this->registerForm;
	}

	public function SubmitAction () {
		list ($result) = $this->registerForm->Submit();
		if ($result === \MvcCore\Ext\Form::RESULT_SUCCESS)
			$this->registerForm->ClearSession();
		$this->registerForm->SubmittedRedirect();
	}
	
	protected function createRegisterForm () {
		return (new \App\Forms\UserRegistration($this))
			->SetAction($this->Url(':Submit', ['absolute' => TRUE]))
			->SetErrorUrl($this->Url(':Index', ['absolute' => TRUE]))
			->SetSuccessUrl($this->Url('Users:List', ['absolute' => TRUE]));
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
				'successUrl' => $this->Url('Index:Index', ['absolute' => TRUE]),
			]);
	}

	/**
	 * Render not found action.
	 * @return void
	 */
	public function NotFoundAction () {
		$this->controllerName = 'index';
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
		$this->view->title = "Error {$code}";
		$this->view->message = $message;
		$this->Render('error');
	}

}
