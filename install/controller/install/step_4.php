<?php
class ControllerInstallStep4 extends Controller {
	public function index() {
		$data = $this->load->language('install/step_4');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['maxmind'] = $this->url->link('3rd_party/maxmind');
		$data['openbay'] = $this->url->link('3rd_party/openbay');
		$data['extension'] = $this->url->link('3rd_party/extension');

		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('install/step_4', $data));
	}
}