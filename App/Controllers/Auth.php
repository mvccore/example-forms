<?php

namespace App\Controllers;

class Auth extends \MvcCore\Ext\Auths\Basics\Controller
{
	public function SignInAction () {
		/** @var $form \MvcCore\Ext\Auths\Basics\SignInForm */
		$form = \MvcCore\Ext\Auths\Basic::GetInstance()->GetSignInForm();
		list ($result, $data, $errors) = $form->Submit();
		if ($result === \MvcCore\Ext\Form::RESULT_SUCCESS) {
			$user = \App\Models\User::GetByUserName($data['username']);
			if ($user->IsAdmin()) {
				$successUrl = $this->Url('admin_home', ['absolute' => TRUE]);
			} else {
				$successUrl = $this->Url('front_home', ['absolute' => TRUE]);
			}
			$form->SetSuccessUrl($successUrl);
			$form->ClearSession(); // to remove all submitted data from session
		} else {
			$form->SetSuccessUrl($this->Url('front_login', ['absolute' => TRUE]));
		}
		$form->SubmittedRedirect();
	}

	public function SignOutAction () {
		parent::SignOutAction();
	}
}
