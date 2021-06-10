<?php

class ControllerMarketplaceBrasil extends Controller {
	public function index() {
		$data = $this->language->load('marketplace/brasil');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
			'text' => $this->language->get('text_home'),
		);

		$data['breadcrumbs'][] = array(
			'href' => $this->url->link('marketplace/brasil', 'user_token=' . $this->session->data['user_token'], true),
			'text' => $this->language->get('heading_title'),
		);

		$url = '';

		if (isset($this->request->get['filter_search'])) {
			$url .= '&filter_search=' . $this->request->get['filter_search'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		$data['categories'] = array();

		$data['categories'][] = array(
			'text'  => $this->language->get('text_all'),
			'value' => '',
			'href'  => $this->url->link('marketplace/brasil', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['categories'][] = array(
			'text'  => $this->language->get('text_theme'),
			'value' => 'theme',
			'href'  => $this->url->link('marketplace/brasil', 'user_token=' . $this->session->data['user_token'] . $url . '&filter_category=theme', true)
		);

		$data['categories'][] = array(
			'text'  => $this->language->get('text_marketplace'),
			'value' => 'marketplace',
			'href'  => $this->url->link('marketplace/brasil', 'user_token=' . $this->session->data['user_token'] . $url . '&filter_category=marketplace', true)
		);

		$data['categories'][] = array(
			'text'  => $this->language->get('text_language'),
			'value' => 'language',
			'href'  => $this->url->link('marketplace/brasil', 'user_token=' . $this->session->data['user_token'] . $url . '&filter_category=language', true)
		);

		$data['categories'][] = array(
			'text'  => $this->language->get('text_payment'),
			'value' => 'payment',
			'href'  => $this->url->link('marketplace/brasil', 'user_token=' . $this->session->data['user_token'] . $url . '&filter_category=payment', true)
		);

		$data['categories'][] = array(
			'text'  => $this->language->get('text_shipping'),
			'value' => 'shipping',
			'href'  => $this->url->link('marketplace/brasil', 'user_token=' . $this->session->data['user_token'] . $url . '&filter_category=shipping', true)
		);

		$data['categories'][] = array(
			'text'  => $this->language->get('text_module'),
			'value' => 'module',
			'href'  => $this->url->link('marketplace/brasil', 'user_token=' . $this->session->data['user_token'] . $url . '&filter_category=module', true)
		);

		$data['categories'][] = array(
			'text'  => $this->language->get('text_total'),
			'value' => 'total',
			'href'  => $this->url->link('marketplace/brasil', 'user_token=' . $this->session->data['user_token'] . $url . '&filter_category=total', true)
		);

		$data['categories'][] = array(
			'text'  => $this->language->get('text_feed'),
			'value' => 'feed',
			'href'  => $this->url->link('marketplace/brasil', 'user_token=' . $this->session->data['user_token'] . $url . '&filter_category=feed', true)
		);

		$data['categories'][] = array(
			'text'  => $this->language->get('text_report'),
			'value' => 'report',
			'href'  => $this->url->link('marketplace/brasil', 'user_token=' . $this->session->data['user_token'] . $url . '&filter_category=report', true)
		);

		$data['categories'][] = array(
			'text'  => $this->language->get('text_other'),
			'value' => 'other',
			'href'  => $this->url->link('marketplace/brasil', 'user_token=' . $this->session->data['user_token'] . $url . '&filter_category=other', true)
		);

		$url = '';

		if (isset($this->request->get['filter_search'])) {
			$url .= '&filter_search=' . $this->request->get['filter_search'];
		}

		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . $this->request->get['filter_category'];
		}

		$data['sorts'] = array();

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_date_modified'),
			'value' => 'date_modified',
			'href'  => $this->url->link('marketplace/brasil', 'user_token=' . $this->session->data['user_token'] . $url . '&sort=date_modified')
		);

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_date_added'),
			'value' => 'date_added',
			'href'  => $this->url->link('marketplace/brasil', 'user_token=' . $this->session->data['user_token'] . $url . '&sort=date_added')
		);

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_rating'),
			'value' => 'rating',
			'href'  => $this->url->link('marketplace/brasil', 'user_token=' . $this->session->data['user_token'] . $url . '&sort=rating')
		);

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_name'),
			'value' => 'name',
			'href'  => $this->url->link('marketplace/brasil', 'user_token=' . $this->session->data['user_token'] . $url . '&sort=name')
		);

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_price'),
			'value' => 'price',
			'href'  => $this->url->link('marketplace/brasil', 'user_token=' . $this->session->data['user_token'] . $url . '&sort=price')
		);

		$url = '';

		if (isset($this->request->get['filter_search'])) {
			$url .= '&filter_search=' . $this->request->get['filter_search'];
		}

		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . $this->request->get['filter_category'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		$pagination = new Pagination();
		$pagination->total = 100;
		$pagination->page = 1;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('marketplace/brasil', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['filter_search'] = $this->request->get['filter_search'] ?? '';
		$data['filter_category'] = $this->request->get['filter_category'] ?? '';
		$data['sort'] = $this->request->get['sort'] ?? '';

		$response_mock = array(
			array(
				'id' => 1,
				'name' => 'Extension Name #1',
				'price' => 0,
				'image' => 'https://picsum.photos/357/152',
			),
			array(
				'id' => 2,
				'name' => 'Extension Name #2',
				'price' => 'R$15,00',
				'image' => 'https://picsum.photos/357/152',
			),
			array(
				'id' => 3,
				'name' => 'Extension Name #3',
				'price' => 'R$20,00',
				'image' => 'https://picsum.photos/357/152',
			),
			array(
				'id' => 4,
				'name' => 'Extension Name #4',
				'price' => 'R$25,00',
				'image' => 'https://picsum.photos/357/152',
			),
			array(
				'id' => 5,
				'name' => 'Extension Name #5',
				'price' => 'R$30,00',
				'image' => 'https://picsum.photos/357/152',
			)
		);

		$data['extensions'] = array();

		foreach($response_mock as $extension) {
			$data['extensions'][] = array(
				'name' => $extension['name'],
				'image' => $extension['image'],
				'price' => ($extension['price'] == 0) ? $this->language->get('text_free') : $extension['price'],
				'href' => $this->url->link('marketplace/brasil/info', 'user_token=' . $this->session->data['user_token'] . '&extension_id=' . $extension['id'], true),
			);
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('marketplace/marketplace_brasil_list', $data));
	}

	public function info() {
		$data = $this->language->load('marketplace/brasil');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
			'text' => $this->language->get('text_home'),
		);

		$data['breadcrumbs'][] = array(
			'href' => $this->url->link('marketplace/brasil', 'user_token=' . $this->session->data['user_token'], true),
			'text' => $this->language->get('heading_title'),
		);

		$data['breadcrumbs'][] = array(
			'href' => $this->url->link('marketplace/brasil/info', 'user_token=' . $this->session->data['user_token'] . '&extension_id=' . $this->request->get['extension_id'], true),
			'text' => 'Extension Name',
		);

		$response_mock = array(
			'name' => 'Extension Name',
			'images' => array(
				'https://picsum.photos/1010/380',
				'https://picsum.photos/1010/380',
				'https://picsum.photos/1010/380',
				'https://picsum.photos/1010/380',
			),
			'thumbnails' => array(
				array(
					'href' => 'https://picsum.photos/500',
					'src' => 'https://picsum.photos/100'
				),
				array(
					'href' => 'https://picsum.photos/500',
					'src' => 'https://picsum.photos/100'
				),
				array(
					'href' => 'https://picsum.photos/500',
					'src' => 'https://picsum.photos/100'
				),
				array(
					'href' => 'https://picsum.photos/500',
					'src' => 'https://picsum.photos/100'
				),
				array(
					'href' => 'https://picsum.photos/500',
					'src' => 'https://picsum.photos/100'
				),
				array(
					'href' => 'https://picsum.photos/500',
					'src' => 'https://picsum.photos/100'
				),
				array(
					'href' => 'https://picsum.photos/500',
					'src' => 'https://picsum.photos/100'
				),
				array(
					'href' => 'https://picsum.photos/500',
					'src' => 'https://picsum.photos/100'
				),
				array(
					'href' => 'https://picsum.photos/500',
					'src' => 'https://picsum.photos/100'
				),
			),
			'price' => 0.00,
			'lists' => array(
				'Licença GPL V3',
				'Documentação incluída'
			),
			'updated_at' => '2020-09-25',
			'released_at' => '2016-12-26',
			'downloads' => '13071993',
			'description' => 'Descrição Long',
			'documentation' => 'Documentação Long'
		);

		$data['extension_name'] = $response_mock['name'];

		$data['images'] = $response_mock['images'];

		$data['thumbnails'] = $response_mock['thumbnails'];

		$data['price'] = ($response_mock['price'] == 0.00)
			? $this->language->get('text_free')
			: $response_mock['price'];

		$data['lists'] = $response_mock['lists'];

		$data['updated_at'] = date('d/m/Y', strtotime($response_mock['updated_at']));

		$data['released_at'] = date('d/m/Y', strtotime($response_mock['released_at']));

		$data['downloads'] = number_format($response_mock['downloads'], 0, '.', '.');

		$data['description'] = $response_mock['description'];

		$data['documentation'] = $response_mock['documentation'];

		$data['cancel'] = $this->url->link('marketplace/brasil', 'user_token=' . $this->session->data['user_token'], true);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('marketplace/marketplace_brasil_info', $data));
	}
}
