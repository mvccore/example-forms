<?php

namespace App;

class Bootstrap {

	/**
	 * @return \MvcCore\Application
	 */
	public static function Init () {

		$app = \MvcCore\Application::GetInstance();


		// Patch core to use extended debug class:
		if (class_exists('MvcCore\Ext\Debugs\Tracy')) {
			\MvcCore\Ext\Debugs\Tracy::$Editor = 'MSVS2019';
			$app->SetDebugClass('MvcCore\Ext\Debugs\Tracy');
		}


		$app->GetEnvironment()->GetName();
		$sysCfg = \MvcCore\Config::GetConfigSystem();
		$cache = \MvcCore\Ext\Caches\Redis::GetInstance([ // `default` connection to:
			\MvcCore\Ext\ICache::CONNECTION_NAME		=> $sysCfg->cache->storeName,
			\MvcCore\Ext\ICache::CONNECTION_DATABASE	=> $sysCfg->cache->databaseName,
		]);
		\MvcCore\Ext\Cache::RegisterStore($sysCfg->cache->storeName, $cache, TRUE);
		if ($sysCfg->cache->enabled) 
			$cache->Connect();

		
		/**
		 * Uncomment this line before generate any assets into temporary directory, before application
		 * packing/building, only if you want to pack application without JS/CSS/fonts/images inside
		 * result PHP package and you want to have all those files placed on hard drive.
		 * You can use this variant in modes `PHP_PRESERVE_PACKAGE`, `PHP_PRESERVE_HDD` and `PHP_STRICT_HDD`.
		 */
		//\MvcCore\Ext\Views\Helpers\Assets::SetAssetUrlCompletion(FALSE);


		// Initialize authentication service extension and set custom user class
		\MvcCore\Ext\Auths\Basic::GetInstance()

			// Set unique password hash:
			->SetPasswordHashSalt('s9E56/QH6.a69sJML9aS6s')

			// To use credentials from system config file:
			//->SetUserClass('MvcCore\Ext\Auths\Basics\Users\SystemConfig')

			// To use credentials from database:
			->SetUserClass('\App\Models\User')

			// To use custom authentication submitting controller:
			->SetControllerClass('//App\Controllers\Auth')

			->SetExpirationAuthorization(3600)
			->SetExpirationIdentity(86400 * 30);

		// Display db password hash value by unique password hash for desired user name:
		//die(\MvcCore\Ext\Auths\Basics\User::EncodePasswordToHash('demo'));


		// Set up application routes (without custom names),
		// defined basically as `Controller::Action` combinations:
		\MvcCore\Router::GetInstance([
			'Index:Index'			=> [
				'match'				=> '#^/(index\.php)?$#',
				'reverse'			=> '/',
				'defaults'			=> ['order' => 'desc'],
				'constraints'		=> ['order' => 'a-z'],
			],
			'Index:SignIn'			=> '/login',
			'Users:List'			=> '/users',
		]);
		
		return $app;
	}
}
