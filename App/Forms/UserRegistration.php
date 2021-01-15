<?php

namespace App\Forms;

use \MvcCore\Ext\Forms,
	\MvcCore\Ext\Forms\Fields;

class UserRegistration extends \MvcCore\Ext\Form
{
	protected $id = 'user_registration';

	protected $cssClasses = ['theme', 'registration'];

	protected $method = \MvcCore\IRequest::METHOD_POST;

	protected $viewScript = 'user-registration';

	protected $enctype = \MvcCore\Ext\IForm::ENCTYPE_MULTIPART;

	//protected $csrfEnabled = FALSE;

	/**
	 * @return \MvcCore\Ext\Forms\Validators\Password
	 */
	public static function GetPasswordValidator () {
		return (new \MvcCore\Ext\Forms\Validators\Password)

			// dev rules:
			->SetMustHaveMinLength(3)
			->SetMustHaveDigits(FALSE)
			->SetMustHaveLowerCaseChars(FALSE)
			->SetMustHaveUpperCaseChars(FALSE)
			->SetMustHaveSpecialChars(FALSE)
			->SetMustHaveMaxLength(30);

			/*
			// production rules:
			->SetMustHaveDigits(TRUE, 3)
			->SetMustHaveLowerCaseChars(TRUE, 1)
			->SetMustHaveUpperCaseChars(TRUE, 1)
			->SetMustHaveSpecialChars(TRUE, 2)
			->SetMustHaveMinLength(10)
			->SetMustHaveMaxLength(30);
			*/
	}

	public function Init ($submit = FALSE) {
		parent::Init($submit);

		$passwordValidator = self::GetPasswordValidator();

		$fullName = (new Fields\Text)
			//->SetValidators([])
			->SetMinLength(3)
			->SetMaxLength(100)
			->SetRequired(TRUE)
			->SetPlaceHolder('John Doe')
			->SetName('full_name')
			->SetLabel('Full Name');;

		$userName = (new Fields\Text)
			//->SetValidators([])
			->SetMaxLength(100)
			->SetRequired(TRUE)
			->SetPlaceHolder('user.name')
			->SetName('user_name')
			->SetLabel('Login');;

		$email = (new Fields\Email)
			->AddValidators(new \App\Forms\UserRegistrations\EmailValidator)
			->SetMaxLength(100)
			->SetRequired(TRUE)
			->SetPlaceHolder('username@example.com')
			->SetLabel('Email')
			->SetName('email');

		$password1 = (new Fields\Password)
			->AddValidators($passwordValidator)
			->SetRequired(TRUE)
			->SetName('password_first')
			->SetLabel('Password')
			/** @see https://stackoverflow.com/questions/2530/how-do-you-disable-browser-autocomplete-on-web-form-field-input-tag */
			->SetControlAttrs([
				'readonly'	=> 'readonly',
				'onfocus'	=> "if(this.hasAttribute('readonly')){this.removeAttribute('readonly');this.blur();this.focus()}",
			])
			->SetAutoComplete('off');

		$password2 = (new Fields\Password)
			->AddValidators($passwordValidator)
			//->SetRequired()
			->SetName('password_second')
			->SetLabel('Password (check)')
			/** @see https://stackoverflow.com/questions/2530/how-do-you-disable-browser-autocomplete-on-web-form-field-input-tag */
			->SetControlAttrs([
				'readonly'	=> 'readonly',
				'onfocus'	=> "if(this.hasAttribute('readonly')){this.removeAttribute('readonly');this.blur();this.focus()}",
			])
			->SetAutoComplete('off');

		$avatarImg = (new Fields\File)
			->SetMaxSize(10485760) // 10 MB
			->SetMultiple(FALSE)
			->SetMaxCount(1)
			->SetAllowedFileNameChars('\-\.\,_a-zA-Z0-9')
			->SetAccept(['image/jpeg','image/png','image/gif'])
			->SetName('avatar_image')
			->SetLabel('Avatar image');


		$send = (new Fields\SubmitButton)
			->SetName('send');

		return $this->AddFields(
			$fullName, 
			$userName, 
			$email, 
			$password1, 
			$password2, 
			$avatarImg,
			$send
		);
	}

	public function PreDispatch($submit = FALSE) {
		parent::PreDispatch($submit);
		if (!$this->viewEnabled) return $this;
		$recaptchaCfg = \MvcCore\Config::GetSystem()->recaptcha;
		$this->view->useRecaptcha = $this->environment->IsProduction();
		$this->view->recaptchaSiteKey = $recaptchaCfg->sitekey;
	}

	public function Submit (array & $rawRequestParams = []) {
		parent::Submit($rawRequestParams);
		if ($this->result == self::RESULT_SUCCESS) {
			$useRecaptcha = $this->environment->IsProduction();
			$recaptchaCfg = \MvcCore\Config::GetSystem()->recaptcha;
			try {
				if ($useRecaptcha) 
					$this->submitRecaptcha(
						$rawRequestParams, 
						$recaptchaCfg->secret
					);

				$data = (object) $this->values;

				$avatarUrl = NULL;
				if (
					$data->avatar_image && 
					count($data->avatar_image) === 1
				) {
					$avatarFile = $data->avatar_image[0];
					if ($avatarFile->error === 0) {
						$targetRelativePath = '/Var/Avatars/' . $avatarFile->name;
						$moved = move_uploaded_file(
							$avatarFile->tmpFullPath,
							$this->request->GetAppRoot() . $targetRelativePath
						);
						if ($moved) 
							$avatarUrl = $this->request->GetDomainUrl() . $targetRelativePath;
					}
				}

				$newUser = new \App\Models\User;
				$newUser
					->SetEmail($data->email)
					->SetAdmin(FALSE)
					->SetActive(TRUE)
					->SetFullName($data->full_name)
					->SetUserName($data->user_name)
					->SetPasswordHash(
						\App\Models\User::EncodePasswordToHash($data->password_first)
					);
				if ($avatarUrl)
					$newUser->SetAvatarUrl($avatarUrl);

				$newUser->Save(
					TRUE, 
					\MvcCore\IModel::PROPS_INHERIT |
					\MvcCore\IModel::PROPS_PROTECTED
				);

			} catch (\Throwable $e) {
				\MvcCore\Debug::Exception($e);
				$this->AddError('Error when registering new user. See more in application log.');
			}
		}
		return [
			$this->result,
			$this->values,
			$this->errors,
		];
	}

	protected function submitRecaptcha (& $rawRequestParams, $secret) {
		$recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
		$recaptchaResponse = $rawRequestParams['g-recaptcha-response'];

		$recaptcha = file_get_contents(
			$recaptchaUrl 
			. '?secret=' . urlencode($secret )
			. '&response=' . urlencode($recaptchaResponse)
		);
		$recaptcha = json_decode($recaptcha, TRUE);

		if ($recaptcha->success) {
			return TRUE;
		} else {
			throw new \Exception('Recaptcha error: ' . $recaptcha->{'error-codes'});
		}
	}
}