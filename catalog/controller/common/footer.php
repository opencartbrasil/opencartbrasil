<?php
class ControllerCommonFooter extends Controller {
	public function index() {
		$this->load->language('common/footer');

		$this->load->model('catalog/information');

		$data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['bottom']) {
				$data['informations'][] = array(
					'title' => $result['title'],
					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}
		}

		$data['contact'] = $this->url->link('information/contact');
		$data['return'] = $this->url->link('account/return/add', '', true);
		$data['sitemap'] = $this->url->link('information/sitemap');
		$data['tracking'] = $this->url->link('information/tracking');
		$data['manufacturer'] = $this->url->link('product/manufacturer');
		$data['voucher'] = $this->url->link('account/voucher', '', true);
		$data['affiliate'] = $this->url->link('affiliate/login', '', true);
		$data['special'] = $this->url->link('product/special');
		$data['account'] = $this->url->link('account/account', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['newsletter'] = $this->url->link('account/newsletter', '', true);

		$data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));

		// Whos Online
		if ($this->config->get('config_customer_online')) {
			$ip = '';

			if (isset($this->request->server['REMOTE_ADDR']) && filter_var($this->request->server['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
				$ip = $this->request->server['REMOTE_ADDR'];
			}

			if (isset($this->request->server['HTTP_X_FORWARDED_FOR'])) {
				$xip = trim(current(explode(',', $this->request->server['HTTP_X_FORWARDED_FOR'])));

				if (filter_var($xip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
					if (isset($this->request->server['SERVER_ADDR']) && $this->request->server['SERVER_ADDR'] != $xip) {
						$ip = $xip;
					}
				}
			}

			if (isset($this->request->server['HTTP_CF_CONNECTING_IP']) && filter_var($this->request->server['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
				$ip = $this->request->server['HTTP_CF_CONNECTING_IP'];
			}

			if (isset($this->request->server['HTTP_INCAP_CLIENT_IP']) && filter_var($this->request->server['HTTP_INCAP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
				$ip = $this->request->server['HTTP_INCAP_CLIENT_IP'];
			}

			if (isset($this->request->server['HTTP_X_SUCURI_CLIENTIP']) && filter_var($this->request->server['HTTP_X_SUCURI_CLIENTIP'], FILTER_VALIDATE_IP)) {
				$ip = $this->request->server['HTTP_X_SUCURI_CLIENTIP'];
			}

			if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
				$url = ($this->request->server['HTTPS'] ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
			} else {
				$url = '';
			}

			if (isset($this->request->server['HTTP_REFERER'])) {
				$referer = $this->request->server['HTTP_REFERER'];
			} else {
				$referer = '';
			}

			$this->load->model('tool/online');
			$this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
		}

		$data['scripts'] = $this->document->getScripts('footer');

		return $this->load->view('common/footer', $data);
	}
}
