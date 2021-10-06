<?php
class ModelUpdate06 extends Model {
	public function update() {
		$this->load->model('user/user_group');

		if (!$this->user->hasPermission('modify', 'advanced/api')) {
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'advanced/api');
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'advanced/api');
		}

		if (!$this->user->hasPermission('modify', 'advanced/log')) {
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'advanced/log');
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'advanced/log');
		}

		if (!$this->user->hasPermission('modify', 'advanced/webhook')) {
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'advanced/webhook');
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'advanced/webhook');
		}
	}
}
