<?php
class ControllerExtensionShippingFrenet extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load->language('extension/shipping/frenet');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('shipping_frenet', $this->request->post);		
			
			$this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true));

		}
		
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_select_all'] = $this->language->get('text_select_all');
		$data['text_unselect_all'] = $this->language->get('text_unselect_all');

        $data['entry_msg_prazo'] = $this->language->get('entry_msg_prazo');
        $data['entry_frenet_key'] = $this->language->get('entry_frenet_key');
        $data['entry_frenet_key_codigo'] = $this->language->get('entry_frenet_key_codigo');
        $data['entry_frenet_key_senha'] = $this->language->get('entry_frenet_key_senha');

		$data['entry_cost'] = $this->language->get('entry_cost');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');


		$data['help_frenet_key'] = $this->language->get('help_frenet_key');
        $data['help_msg_prazo'] = $this->language->get('help_msg_prazo');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		
		$data['tab_general'] = $this->language->get('tab_general');
		
		$data['entry_postcode']= $this->language->get('entry_postcode');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		if (isset($this->error['postcode'])) {
			$data['error_postcode'] = $this->error['postcode'];
		} else {
			$data['error_postcode'] = '';
		}

		$data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_shipping'),
            'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/shipping/frenet', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/shipping/frenet', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true);

		if (isset($this->request->post['shipping_frenet_status'])) {
			$data['shipping_frenet_status'] = $this->request->post['shipping_frenet_status'];
		} else {
			$data['shipping_frenet_status'] = $this->config->get('shipping_frenet_status');
		}

		if (isset($this->request->post['shipping_frenet_postcode'])) {
			$data['shipping_frenet_postcode'] = $this->request->post['shipping_frenet_postcode'];
		} else {
			$data['shipping_frenet_postcode'] = $this->config->get('shipping_frenet_postcode');
		}
        if (isset($this->request->post['shipping_frenet_msg_prazo'])) {
            $data['shipping_frenet_msg_prazo'] = $this->request->post['shipping_frenet_msg_prazo'];
        } else {
            $data['shipping_frenet_msg_prazo'] = $this->config->get('shipping_frenet_msg_prazo');
        }

		if (isset($this->request->post['shipping_frenet_contrato_codigo'])) {
			$data['shipping_frenet_contrato_codigo'] = $this->request->post['shipping_frenet_contrato_codigo'];
		} else {
			$data['shipping_frenet_contrato_codigo'] = $this->config->get('shipping_frenet_contrato_codigo');
		}
		if (isset($this->request->post['shipping_frenet_contrato_senha'])) {
			$data['shipping_frenet_contrato_senha'] = $this->request->post['shipping_frenet_contrato_senha'];
		} else {
			$data['shipping_frenet_contrato_senha'] = $this->config->get('shipping_frenet_contrato_senha');
		}						

		if (isset($this->request->post['shipping_frenet_sort_order'])) {
			$data['shipping_frenet_sort_order'] = $this->request->post['shipping_frenet_sort_order'];
		} else {
			$data['shipping_frenet_sort_order'] = $this->config->get('shipping_frenet_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/shipping/frenet', $data));

	}
	
	protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/shipping/frenet')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

		return !$this->error;
	}
}
?>
