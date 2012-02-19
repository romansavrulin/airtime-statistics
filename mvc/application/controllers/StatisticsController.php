<?php

/**
 * StatisticsController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';

class StatisticsController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		// TODO Auto-generated StatisticsController::indexAction() default
	// action
	}
	
	public function getStatisticsAction()
	{
		$stat = new Application_Model_Statistics();
		
		$result = $stat->getStatistics(0);
		
		foreach($result as $key => $data){
			$dt = (int) $data['users'];
			//$y_data[$num][] = $dt;
			$y_data[] = array((float) $data['date'], $dt);
		}
		 
		die(json_encode($y_data));
	}

}
