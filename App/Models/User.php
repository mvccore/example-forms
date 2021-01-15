<?php

namespace App\Models;

use \MvcCore\Ext\Models\Db\Statement;
use function \MvcCore\Ext\Models\Db\FuncHelpers\{Table, Columns};

/** 
 * @table users
 */
class User 
extends \App\Models\Base
implements \MvcCore\Ext\Auths\Basics\IUser {

	use \MvcCore\Ext\Auths\Basics\User\Features;
	
	/**
	 * @column created
	 * @var \DateTime
	 */
	protected $created = NULL;

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
	 * @param string|NULL $email
	 * @return \App\Models\User
	 */
	public function SetEmail ($email) {
		$this->email = $email;
		return $this;
	}

	/**
	 * @return null|string
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
	 * @return null|string
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
	 * @return null|string
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