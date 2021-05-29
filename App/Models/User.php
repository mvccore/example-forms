<?php

namespace App\Models;

use \MvcCore\Ext\Models\Db\Statement;
use function \MvcCore\Ext\Models\Db\FuncHelpers\Table;
use function \MvcCore\Ext\Models\Db\FuncHelpers\Columns;

/** 
 * @table users
 */
class User 
extends \App\Models\Base
implements \MvcCore\Ext\Auths\Basics\IUser {

	use \MvcCore\Ext\Auths\Basics\User\Features;
	
	/**
	 * @column gender
	 * @var string|NULL
	 */
	protected $gender = NULL;
	
	/**
	 * @column country
	 * @var string|NULL
	 */
	protected $country = NULL;
	
	/**
	 * @column localization
	 * @var string|NULL
	 */
	protected $localization = NULL;
	
	/**
	 * @column languages
	 * @var string|NULL
	 */
	protected $languages = NULL;
	
	/**
	 * @column born_date
	 * @var \DateTime|NULL
	 */
	protected $bornDate = NULL;

	/**
	 * @column marital_status
	 * @var int|NULL
	 */
	protected $maritalStatus = NULL;

	/**
	 * @column children
	 * @var int|NULL
	 */
	protected $children = NULL;
	
	/**
	 * @column working_from
	 * @var float|NULL
	 */
	protected $workingFrom = NULL;
	
	/**
	 * @column working_to
	 * @var float|NULL
	 */
	protected $workingTo = NULL;
	
	/**
	 * @column color
	 * @var string|NULL
	 */
	protected $color = NULL;

	/**
	 * @column email
	 * @keyUnique
	 * @var string|NULL
	 */
	protected $email = NULL;

	/**
	 * @column website_url
	 * @var string|NULL
	 */
	protected $websiteUrl = NULL;

	/**
	 * @column avatar_url
	 * @var string|NULL
	 */
	protected $avatarUrl = NULL;

	/**
	 * @column created
	 * @var \DateTime
	 */
	protected $created = NULL;
	
	
	/**
	 * @param string|NULL $gender
	 * @return \App\Models\User
	 */
	public function SetGender ($gender) {
		$this->gender = $gender;
		return $this;
	}

	/**
	 * @return string|NULL
	 */
	public function GetGender () {
		return $this->gender;
	}
	
	/**
	 * @param string|NULL $country
	 * @return \App\Models\User
	 */
	public function SetCountry ($country) {
		$this->country = $country;
		return $this;
	}

	/**
	 * @return string|NULL
	 */
	public function GetCountry () {
		return $this->country;
	}
	
	/**
	 * @param string|NULL $localization
	 * @return \App\Models\User
	 */
	public function SetLocalization ($localization) {
		$this->localization = $localization;
		return $this;
	}

	/**
	 * @return string|NULL
	 */
	public function GetLocalization () {
		return $this->localization;
	}
	
	/**
	 * @param \string[]|NULL $languages
	 * @return \App\Models\User
	 */
	public function SetLanguages ($languages) {
		$this->languages = implode(',', $languages);
		return $this;
	}

	/**
	 * @return \string[]|NULL
	 */
	public function GetLanguages () {
		return explode(',', $this->languages);
	}
	
	/**
	 * @param \DateTime|NULL $bornDate
	 * @return \App\Models\User
	 */
	public function SetBornDate ($bornDate) {
		$this->bornDate = $bornDate;
		return $this;
	}

	/**
	 * @return \DateTime|NULL
	 */
	public function GetBornDate () {
		return $this->bornDate;
	}
	
	/**
	 * @param int|NULL $maritalStatus
	 * @return \App\Models\User
	 */
	public function SetMaritalStatus ($maritalStatus) {
		$this->maritalStatus = $maritalStatus;
		return $this;
	}

	/**
	 * @return int|NULL
	 */
	public function GetMaritalStatus () {
		return $this->maritalStatus;
	}
	
	/**
	 * @param int|NULL $children
	 * @return \App\Models\User
	 */
	public function SetChildren ($children) {
		$this->children = $children;
		return $this;
	}

	/**
	 * @return int|NULL
	 */
	public function GetChildren () {
		return $this->children;
	}
	
	/**
	 * @param float|NULL $workingFrom
	 * @return \App\Models\User
	 */
	public function SetWorkingFrom ($workingFrom) {
		$this->workingFrom = $workingFrom;
		return $this;
	}

	/**
	 * @return float|NULL
	 */
	public function GetWorkingFrom () {
		return $this->workingFrom;
	}
	
	/**
	 * @param float|NULL $workingTo
	 * @return \App\Models\User
	 */
	public function SetWorkingTo ($workingTo) {
		$this->workingTo = $workingTo;
		return $this;
	}

	/**
	 * @return float|NULL
	 */
	public function GetWorkingTo () {
		return $this->workingTo;
	}

	/**
	 * @param string|NULL $color
	 * @return \App\Models\User
	 */
	public function SetColor ($color) {
		$this->color = $color;
		return $this;
	}

	/**
	 * @return string|NULL
	 */
	public function GetColor () {
		return $this->color;
	}

	/**
	 * @param string|NULL $email
	 * @return \App\Models\User
	 */
	public function SetEmail ($email) {
		$this->email = $email;
		return $this;
	}

	/**
	 * @return string|NULL
	 */
	public function GetEmail () {
		return $this->email;
	}
	
	/**
	 * @param string|NULL $websiteUrl 
	 * @return \App\Models\User
	 */
	public function SetWebsiteUrl ($websiteUrl) {
		$this->websiteUrl = $websiteUrl;
		return $this;
	}

	/**
	 * @return string|NULL
	 */
	public function GetWebsiteUrl () {
		return $this->websiteUrl;
	}
	
	/**
	 * @param string|NULL $avatarUrl 
	 * @return \App\Models\User
	 */
	public function SetAvatarUrl ($avatarUrl) {
		$this->avatarUrl = $avatarUrl;
		return $this;
	}

	/**
	 * @return string|NULL
	 */
	public function GetAvatarUrl () {
		if (
			mb_strpos($this->avatarUrl, 'http://') === 0 ||
			mb_strpos($this->avatarUrl, 'https://') === 0
		) return $this->avatarUrl;
		$req = \MvcCore\Application::GetInstance()->GetRequest();
		return $req->GetBaseUrl() . '/' . ltrim($this->avatarUrl, '/');
	}
	
	/**
	 * @param \DateTime|NULL $created
	 * @return \App\Models\User
	 */
	public function SetCreated ($created) {
		$this->created = $created;
		return $this;
	}

	/**
	 * @return \DateTime|NULL
	 */
	public function GetCreated () {
		return $this->created;
	}


	/**
	 * Get all users in database as array keyed by $user->id.
	 * @param string $orderCol 'created' by default.
	 * @param string $orderDir 'desc' by default.
	 * @return \MvcCore\Ext\Models\Db\Readers\Streams\Iterator
	 */
	public static function GetAll ($orderCol = 'created', $orderDir = 'desc') {
		return self::GetConnection()
			->Prepare([
				"SELECT ".Columns()."				",
				"FROM ".Table(0)." 					",
				"ORDER BY {$orderCol} {$orderDir};	",
			])
			->StreamAll()
			->ToInstances(
				get_called_class(), 0, 'id', 'int'
			);
	}


	/**
	 * Get user model instance from database or any other users list
	 * resource by submitted and cleaned `$userName` field value.
	 * @param string $userName Submitted and cleaned username. Characters `' " ` < > \ = ^ | & ~` are automatically encoded to html entities by default `\MvcCore\Ext\Auths\Basic` sign in form.
	 * @return \App\Models\User|NULL
	 */
	public static function GetByUserName ($userName) {
		return Statement::Prepare([
				"SELECT *						",
				"FROM users u					",
				"WHERE u.user_name = :user_name;",
			])
			->FetchOne([':user_name' => $userName])
			->ToInstance(
				get_called_class(),
				self::PROPS_INHERIT |
				self::PROPS_PROTECTED |
				self::PROPS_CONVERT_UNDERSCORES_TO_CAMELCASE | 
				self::PROPS_INITIAL_VALUES
			);
	}

	/**
	 * @param string $email
	 * @return \App\Models\User|NULL
	 */
	public static function GetByUserEmail ($email) {
		return Statement::Prepare([
				"SELECT *				",
				"FROM users u			",
				"WHERE u.email = :email;",
			])
			->FetchOne([':email' => $email])
			->ToInstance(
				get_called_class(),
				self::PROPS_INHERIT |
				self::PROPS_PROTECTED |
				self::PROPS_CONVERT_UNDERSCORES_TO_CAMELCASE | 
				self::PROPS_INITIAL_VALUES
			);
	}
}