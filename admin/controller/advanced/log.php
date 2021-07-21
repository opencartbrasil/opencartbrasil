<?php
class ControllerAdvancedLog extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('advanced/log');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addScript('view/javascript/jquery/datetimepicker/moment/moment.min.js');
		$this->document->addScript('view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
		$this->document->addScript('view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('advanced/log', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (isset($this->request->get['filter_date'])) {
			$filter_date = $this->request->get['filter_date'];
		} else {
			$filter_date = date('Y-m-d');
		}

		$data['download'] = $this->url->link('advanced/log/download', 'user_token=' . $this->session->data['user_token'] . '&date=' . $filter_date, true);
		$data['clear'] = $this->url->link('advanced/log/clear', 'user_token=' . $this->session->data['user_token'], true);

		$data['log'] = '';

		$files = array();

        $fsIterator = new FilesystemIterator(DIR_LOGS);

        while ($fsIterator->valid()) {
            $filename = $fsIterator->getFilename();

            if ($fsIterator->isFile() && strpos($filename, 'api-') !== false) {
                preg_match('/api-(\d{4}-\d{2}-\d{2})/', $filename, $date);
                $files[$date[1]] = $fsIterator->getPathname();
            }

            $fsIterator->next();
        }

		if (isset($files[$filter_date])) {
			$file = $files[$filter_date];
		} else {
			$file = '';
		}

		if (file_exists($file)) {
			$size = filesize($file);

			if ($size >= 5242880) {
				$suffix = array(
					'B',
					'KB',
					'MB',
					'GB',
					'TB',
					'PB',
					'EB',
					'ZB',
					'YB'
				);

				$i = 0;

				while (($size / 1024) > 1) {
					$size = $size / 1024;
					$i++;
				}

				$data['error_warning'] = sprintf($this->language->get('error_warning'), basename($file), round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i]);
			} else {
				$data['log'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
			}
		}

		$data['filter_date'] = $filter_date;
		$data['filter_url'] = $this->url->link('advanced/log', 'user_token=' . $this->session->data['user_token'], true);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('advanced/log', $data));
	}

	public function download() {
		$this->load->language('advanced/log');

		$file = DIR_LOGS . 'api-' . $this->request->get['date'] . '.log';

		if (file_exists($file) && filesize($file) > 0) {
			$this->response->addheader('Pragma: public');
			$this->response->addheader('Expires: 0');
			$this->response->addheader('Content-Description: File Transfer');
			$this->response->addheader('Content-Type: application/octet-stream');
			$this->response->addheader('Content-Disposition: attachment; filename="' . $this->config->get('config_name') . '_' . $this->request->get['date'] . '_error.log"');
			$this->response->addheader('Content-Transfer-Encoding: binary');

			$this->response->setOutput(file_get_contents($file, FILE_USE_INCLUDE_PATH, null));
		} else {
			$this->session->data['error'] = sprintf($this->language->get('error_warning'), basename($file), '0B');

			$this->response->redirect($this->url->link('advanced/log', 'user_token=' . $this->session->data['user_token'], true));
		}
	}

	public function clear() {
		$this->load->language('advanced/log');

		if (!$this->user->hasPermission('modify', 'advanced/log')) {
			$this->session->data['error'] = $this->language->get('error_permission');
		} else {
			$fsIterator = new FilesystemIterator(DIR_LOGS);

			while ($fsIterator->valid()) {
				$filename = $fsIterator->getFilename();

				if ($fsIterator->isFile() && strpos($filename, 'api-') !== false) {
					unlink($fsIterator->getPathname());
				}

				$fsIterator->next();
			}

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$this->response->redirect($this->url->link('advanced/log', 'user_token=' . $this->session->data['user_token'], true));
	}
}
