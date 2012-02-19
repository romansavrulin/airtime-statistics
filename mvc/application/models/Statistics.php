<?php

class Application_Model_Statistics {
	
	function __construct() {
	
	}
	
	public static function getStatistics($channel = 0) {
		global $CC_DBC;
		
		$sql_gen = "SELECT extract(epoch from s.date)*1000 date, s.users users FROM cc_stat_intervals s where s.date between now() - interval '1 week' and now() AND s.channel = $channel";
		$sql = $sql_gen;
	
		//$sql = $sql ." ORDER BY id";
		//$CC_DBC->setFetchMode(DB_FETCHMODE_ORDERED);
		return  $CC_DBC->GetAll($sql);
	}
	
}

?>