<?php
class ControllerEventStatistics extends Controller {
	// model/catalog/review/removeReview/after
	public function removeReview(&$route, &$args, &$output) {
		$this->load->model('setting/statistics');

		$this->model_report_statistics->removeValue('review', 1);
	}

	// model/sale/return/removeReturn/after
	public function removeReturn(&$route, &$args, &$output) {
		$this->load->model('setting/statistics');

		$this->model_report_statistics->removeValue('return', 1);
	}
}
