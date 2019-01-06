<?php
class ModelUpdate05 extends Model {
	public function update() {
		$this->load->model('user/user_group');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/extension/advertise');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/extension/advertise');
	}
}