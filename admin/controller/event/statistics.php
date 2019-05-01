<?php
class ControllerEventStatistics extends Controller {
	// model/catalog/review/addReview/after
	public function addReview(&$route, &$args, &$output) {
		$this->load->model('report/statistics');

		$this->model_report_statistics->addValue('review', 1);
	}

	// model/catalog/review/deleteReview/before
	public function deleteReview(&$route, &$args, &$output) {
		$this->load->model('setting/statistics');

		$this->model_report_statistics->removeValue('review', 1);
	}

	// model/sale/return/addReturn/after
	public function addReturn(&$route, &$args, &$output) {
		$this->load->model('report/statistics');

		$this->model_report_statistics->addValue('return', 1);
	}

	// model/sale/return/deleteReturn/before
	public function deleteReturn(&$route, &$args, &$output) {
		$this->load->model('setting/statistics');

		$this->model_report_statistics->removeValue('return', 1);
	}
}
