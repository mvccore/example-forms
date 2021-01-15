<?php

namespace App\Forms\UserRegistrations;

class UserNameValidator
extends \MvcCore\Ext\Forms\Validator
implements \MvcCore\Ext\Forms\IValidator
{
	/** @var int */
	const ERROR_MSG_ANOTHER_NAME_EXISTS = 0;

	/** @var array */
	protected static $errorMessages = [
		self::ERROR_MSG_ANOTHER_NAME_EXISTS => 	"Another user with given name: `{1}` already exists.",
	];

	/**
	 * Validation method.
	 * Check submitted value by validator specific rules and
	 * if there is any error, call: `$this->field->AddValidationError($errorMsg, $errorMsgArgs, $replacingCallable);`
	 * with not translated error message. Return safe submitted value as result or `NULL` if there
	 * is not possible to return safe valid value.
	 * @param string|array			$submitValue	Raw submitted value, string or array of strings.
	 * @return string|array|NULL	Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		// try to load any user by given name
		$user = \App\Models\User::GetByUserName($rawSubmittedValue);
		if ($user !== NULL)
			$this->field->AddValidationError(
				self::GetErrorMessage(self::ERROR_MSG_ANOTHER_NAME_EXISTS),
				[$rawSubmittedValue]
			);
		return $rawSubmittedValue;
	}
}
