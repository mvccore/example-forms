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
		
		$gender = (new Fields\RadioGroup)
			->SetFieldOrder(0)
			->SetGroupLabelCssClasses('main')
			->SetRenderMode(\MvcCore\Ext\IForm::FIELD_RENDER_MODE_LABEL_AROUND)
			->SetOptions([
				'F'	=> 'Female',
				'M'	=> 'Male',
				'O'	=> 'Other',
			])
			->SetRequired(TRUE)
			->SetName('gender')
			->SetLabel('Gender');
		
		$fullName = (new Fields\Text)
			->SetFieldOrder(1)
			->SetMinLength(3)
			->SetMaxLength(100)
			->SetRequired(TRUE)
			->SetPlaceHolder('John Doe')
			->SetName('full_name')
			->SetLabel('Full Name');

		$email = (new Fields\Email)
			->AddValidators(new \App\Forms\UserRegistrations\EmailValidator)
			->SetMaxLength(200)
			->SetRequired(TRUE)
			->SetPlaceHolder('username@example.com')
			->SetLabel('E-mail')
			->SetName('email');
		
		$urlValidator = (new \MvcCore\Ext\Forms\Validators\Url)
			->SetAllowedSchemes('http', 'https')
			// DNS validation is commented out to be able to submit the form offline.
			/*->SetDnsValidation(
				\MvcCore\Ext\Forms\Validators\Url::VALIDATE_DNS_TYPE_A
			)*/;
		
		$websiteUrl = (new Fields\Url)
			->SetValidators([$urlValidator])
			->SetMaxLength(1000)
			->SetRequired(FALSE)
			->SetPlaceHolder('http(s)://domain.com')
			->SetLabel('Website')
			->SetName('website_url');
		
		$avatarImg = (new Fields\File)
			->SetMaxSize(10485760) // 10 MB
			->SetMultiple(FALSE)
			->SetMaxCount(1)
			->SetAllowedFileNameChars('\-\.\,_a-zA-Z0-9')
			->SetAccept(['image/jpeg','image/png','image/gif'])
			->AddBombScanners(
				\MvcCore\Ext\Forms\Validators\Files\Validations\BombScanners\ZipArchive::class,
				\MvcCore\Ext\Forms\Validators\Files\Validations\BombScanners\PngImage::class
			)
			->SetName('avatar_image')
			->SetLabel('Avatar image');

		$credentials = new Forms\Fieldset(
			name: 'credentials',
			legend: 'Credentials',
			fieldOrder: 5
		);

		$userName = (new Fields\Text)
			->SetMaxLength(100)
			->SetRequired(TRUE)
			->SetPlaceHolder('user.name')
			->SetName('user_name')
			->SetLabel('Login')
			->AddCssClasses('middle');

		$password1 = (new Fields\Password)
			->AddValidators($passwordValidator)
			->SetRequired(TRUE)
			->SetName('password_first')
			->SetLabel('Password')
			/** @see https://stackoverflow.com/questions/2530/how-do-you-disable-browser-autocomplete-on-web-form-field-input-tag */
			->SetControlAttrs(['readonly' => 'readonly',]) // removed in JS
			->SetAutoComplete('off')
			->AddCssClasses('middle');

		$password2 = (new Fields\Password)
			->AddValidators($passwordValidator)
			->SetRequired(TRUE)
			->SetName('password_second')
			->SetLabel('Password (check)')
			/** @see https://stackoverflow.com/questions/2530/how-do-you-disable-browser-autocomplete-on-web-form-field-input-tag */
			->SetControlAttrs([ 'readonly' => 'readonly',]) // removed in JS
			->SetAutoComplete('off')
			->AddCssClasses('middle');

		$credentials->AddFields($userName, $password1, $password2);

		$country = (new Fields\CountrySelect)
			->SetNullOptionText(' ')
			->FilterOptions([
				'GB', 'FR', 'DE', 'CZ', 'SK', 'SZ', 'AT', 
				'PL', 'DK', 'IT', 'ES', 'PT', 'IE', 'HU'
			])
			->SetMultiple(FALSE)
			->SetRequired(FALSE)
			->SetName('country')
			->SetLabel('Born Country')
			->AddCssClasses('middle');

		$localization = (new Fields\LocalizationSelect)
			->SetNullOptionText(' ')
			->FilterOptions([
				'en_GB', 'fr_FR', 'de_DE', 'cs_CZ', 'sk_SK', 'gsw_CH', 'de_AT', 
				'pl_PL', 'da_DK', 'it_IT', 'ca_ES', 'pt_PT', 'ga_IE', 'hu_HU'
			])
			->SetRequired(FALSE)
			->SetName('localization')
			->SetLabel('Mother Tongue')
			->AddCssClasses('middle');

		$languages = (new Fields\CheckboxGroup)
			->SetGroupLabelCssClasses('main')
			->SetRenderMode(\MvcCore\Ext\IForm::FIELD_RENDER_MODE_LABEL_AROUND)
			->SetOptions([
				'en'	=> 'English',
				'ch'	=> 'Chinese',
				'hi'	=> 'Hindi',
				'sp'	=> 'Spanish',
				'fr'	=> 'French',
				'ar'	=> 'Arabic',
				'be'	=> 'Bengali',
				'ru'	=> 'Russian',
			])
			->SetRequired(FALSE)
			->SetName('languages')
			->SetLabel('Languages');
		
		$bornDate = (new Fields\Date)
			->SetMin('1900-01-01')
			->SetMax(new \Datetime('now'))
			->SetLabel('Born Date')
			->SetName('born_date')
			->SetRequired(FALSE)
			->AddCssClasses('short');

		$marital = (new Fields\Select)
			->SetNullOptionText(' ')
			->SetOptions([
				0	=> 'Single',
				1	=> 'Married',
				2	=> 'Separated',
				3	=> 'Divorced',
				4	=> 'Widowed',
			])
			->SetRequired(FALSE)
			->SetName('marital_status')
			->SetLabel('Marital Status')
			->AddCssClasses('short');

		$children = (new Fields\Number)
			->SetValidators(['IntNumber'])
			->SetMin(0)
			->SetMax(50)
			->SetStep(1)
			->SetRequired(FALSE)
			->SetLabel('Children')
			->SetName('children')
			->AddCssClasses('short');
		
		$workingTime = (new Fields\Range)
			->SetMultiple(TRUE)
			->SetMin(0)
			->SetMax(24)
			->SetStep(0.5)
			->SetRequired(FALSE)
			->SetLabel('Working Time')
			->SetName('working_time')
			->AddCssClasses('short');
		
		$color = (new Fields\Color)
			->SetRequired(FALSE)
			->SetName('color')
			->SetLabel('Favourite Color')
			->AddCssClasses('short');

		$newsletter = (new Fields\Checkbox)
			->SetRequired(FALSE)
			->SetName('newsletter')
			->SetLabel("I don't want to receive any newsletters.");

		$send = (new Fields\SubmitButton)
			->SetName('send');
		
		$reset = (new Fields\ResetButton)
			->SetName('reset');
		
		$this->AddFields(
			$email, $websiteUrl, $websiteUrl, $avatarImg, 
			//$userName, $password1, $password2, 
			$country, $localization, $languages,
			$bornDate, $marital, $children,
			$workingTime, $color,
			$newsletter, 
			$send, $reset,
			
			$gender, $fullName
		);

		$this->AddFieldset($credentials);
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

				$data = (object) $this->GetValues();
				
				$data->password_hash = \App\Models\User::EncodePasswordToHash($data->password_first);
				unset($data->password_first, $data->password_second);

				$data->avatar_url = NULL;
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
							$data->avatar_url = $this->request->GetBaseUrl() . $targetRelativePath;
					}
				}
				unset($data->avatar_image);

				$data->languages = is_array($data->languages) ? implode(',', $data->languages) : [$data->languages];

				$data->working_from = $data->working_time[0];
				$data->working_to = $data->working_time[1];
				unset($data->working_time);

				$newUser = new \App\Models\User;
				$newUser
					->SetAdmin(FALSE)
					->SetActive(TRUE)
					->SetCreated(new \DateTime('now'));
				
				$newUser->SetValues(
					(array) $data,
					\MvcCore\Model::PROPS_INHERIT |
					\MvcCore\Model::PROPS_CONVERT_UNDERSCORES_TO_CAMELCASE
				);
				
				//x($data);
				//xxx($newUser);

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