<?php
class ModelUpdate05 extends Model {
	public function update() {
		$this->load->model('user/user_group');

		if (!$this->user->hasPermission('modify', 'extension/extension/advertise')) {
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/extension/advertise');
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/extension/advertise');
		}

		if (!$this->user->hasPermission('modify', 'extension/extension/cron')) {
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/extension/cron');
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/extension/cron');
		}

		if (!$this->user->hasPermission('modify', 'marketplace/cron')) {
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'marketplace/cron');
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'marketplace/cron');
		}

		if (!$this->user->hasPermission('modify', 'extension/extension/currency')) {
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/extension/currency');
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/extension/currency');
		}

		if (!$this->user->hasPermission('modify', 'extension/currency/ecb')) {
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/currency/ecb');
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/currency/ecb');
		}

		if (!$this->user->hasPermission('modify', 'extension/currency/fix')) {
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/currency/fix');
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/currency/fix');
		}
	}
}