<?php

namespace App\Controllers;

class Registration extends \App\Controllers\Index
{
	public function PreDispatch() {
		parent::PreDispatch();
		if (!$this->viewEnabled) return;
		
	}

	public function IndexAction () {
		$this->view->title = 'Registration';
		$this->view->registerForm = $this->getRegistrationForm();
	}

	public function SubmitAction () {
		$form = $this->getRegistrationForm();
		list ($result/*, $values, $errors*/) = $form->Submit();
		if ($result === \MvcCore\Ext\Form::RESULT_SUCCESS)
			$form->ClearSession();
		$form->SubmittedRedirect();
	}

	protected function getRegistrationForm () {
		return (new \App\Forms\UserRegistration($this))
			->SetAction($this->Url(':Submit', ['absolute' => TRUE]))
			->SetErrorUrl($this->Url(':Register', ['absolute' => TRUE]))
			->SetSuccessUrl($this->Url('front_home', ['absolute' => TRUE]));
	}
}