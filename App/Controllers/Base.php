<?php

namespace App\Controllers;

class Base extends \MvcCore\Controller {
	/**
	 * Authenticated user instance is automatically assigned
	 * by authentication extension before `Controller::Init();`.
	 * @var \MvcCore\Ext\Auths\Basics\IUser
	 */
	protected $user = NULL;
	
	public function PreDispatch () {
		parent::PreDispatch();
		if ($this->viewEnabled) {
			$this->preDispatchSetUpViewHelpers();
			$this->preDispatchSetUpAssetsBase();

			$this->view->user = $this->user;
			if ($this->user) {
				// set sign-out form into view, set signed-out url to homepage:
				$this->view->signOutForm = \MvcCore\Ext\Auths\Basic::GetInstance()
					->GetSignOutForm()
					->SetValues([
						'successUrl' => $this->Url('Index:Index', ['absolute' => TRUE])
					]);
			} else if ($this->controllerName != 'auth') {
				$this->view->signInLink = $this->Url('Index:SignIn');
			}
			$this->view->basePath = $this->request->GetBasePath();
			$this->view->currentRouteCssClass = str_replace(
				':', '-', strtolower(
					$this->router->GetCurrentRoute()->GetName()
				)
			);
		}
	}

	protected function preDispatchSetUpViewHelpers () {
		/** @var $formateDate \MvcCore\Ext\Views\Helpers\FormatDateHelper */
		$formateDate = $this->view->GetHelper('FormatDate');
		$formateDate
			->SetIntlDefaultDateFormatter(\IntlDateFormatter::MEDIUM)
			->SetIntlDefaultTimeFormatter(\IntlDateFormatter::NONE)
			/** @see http://php.net/strftime */
			->SetStrftimeFormatMask('%e. %B %G');
	}

	protected function preDispatchSetUpAssetsBase () {
		\MvcCore\Ext\Views\Helpers\Assets::SetGlobalOptions([
			'cssMinify'	=> 1,
			'cssJoin'	=> 1,
			'jsMinify'	=> 1,
			'jsJoin'	=> 1,
		]);
		$static = self::$staticPath;
		$this->view->Css('fixedHead')
			->Append($static . '/css/components/resets.css')
			->Append($static . '/css/components/old-browsers-warning.css')
			->AppendRendered($static . '/css/components/fonts.css')
			->AppendRendered($static . '/css/components/forms-and-controls.css')
			->AppendRendered($static . '/css/layout.css')
			->AppendRendered($static . '/css/content.css');
	}
}
