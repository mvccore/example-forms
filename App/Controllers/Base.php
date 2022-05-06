<?php

namespace App\Controllers;

use \MvcCore\Ext\Tools\Csp;

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
			$csp = Csp::GetInstance();
			$csp
				->Disallow(
					Csp::FETCH_DEFAULT_SRC | 
					Csp::FETCH_OBJECT_SRC
				)
				->AllowSelf(
					Csp::FETCH_SCRIPT_SRC | 
					Csp::FETCH_STYLE_SRC | 
					Csp::FETCH_IMG_SRC |
					Csp::FETCH_FONT_SRC |
					Csp::FETCH_MEDIA_SRC |
					Csp::FETCH_CONNECT_SRC |
					Csp::FETCH_FRAME_SRC
				)
				->AllowHosts(
					Csp::FETCH_SCRIPT_SRC, [
						'https://cdnjs.com/',
					]
				)
				->AllowHosts(
					Csp::FETCH_IMG_SRC, [
						'data:'
					]
				)
				->AllowNonce(Csp::FETCH_SCRIPT_SRC)
				->AllowStrictDynamic(Csp::FETCH_SCRIPT_SRC)
				->AllowUnsafeInline(Csp::FETCH_STYLE_SRC);

			$this->view->nonce = $csp->GetNonce();

			$this->application->AddPreSentHeadersHandler(function ($req, \MvcCore\IResponse $res) {
				$csp = Csp::GetInstance();
				$res->SetHeader($csp->GetHeaderName(), $csp->GetHeaderValue());
			});
		}
	}

	protected function preDispatchSetUpViewHelpers () {
		/** @var \MvcCore\Ext\Views\Helpers\FormatDateHelper $formateDate */
		$formateDate = $this->view->GetHelper('FormatDate');
		$formateDate
			->SetDefaultIntlDateType(\IntlDateFormatter::MEDIUM)
			->SetDefaultIntlTimeType(\IntlDateFormatter::NONE)
			/** @see http://php.net/strftime */
			->SetDefaultFormatMask('%e. %B %G');
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
		$this->view->Js('fixedHead')
			->Append($static . '/js/libs/class.min.js')
			->Append($static . '/js/libs/ajax.min.js')
			->Append($static . '/js/libs/Module.js');
		$this->view->Js('varFoot')
			->Append($static . '/js/Front.js');
	}
}
