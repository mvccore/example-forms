<?php

namespace App\Controllers;

class Users extends Index {

	/**
	 * Render homepage with registered users.
	 * @return void
	 */
	public function ListAction () {
		$this->view->title = 'Registered Users';
		
		$orderDir = $this->GetParam('order', 'a-z', 'desc');
		$users = \App\Models\User::GetAll('created', $orderDir);
		$this->view->users = $users;

		$defaultOrder = $orderDir == 'desc';
		$this->view->orderLinkText = $defaultOrder
			? 'From oldest'
			: 'From newest';
		$this->view->orderLinkValue = $this->Url(
			'self', ['order' => $defaultOrder ? 'asc' : 'desc']
		);
	}
}
