<?php namespace system\libraries;

use GeoIp2\Database\Reader;

class visitor
{
	function __construct()
	{
		$this->reader = new Reader(BASE_PATH."/geoip/GeoLite2-City.mmdb");
	}

	public function setVisitorInfo($ipVisitor = '')
	{
		$visitor['ip_addr'] 	= $_SERVER['REMOTE_ADDR'];
		$visitor['user_agent'] 	= $_SERVER['HTTP_USER_AGENT'];
		
		## get user location
		## This creates the Reader object, which should be reused across lookups.
		$record = $this->reader->city($ipVisitor);
		
		$visitor['latitude'] 	= $record->location->latitude;
		$visitor['longitude'] 	= $record->location->longitude;
		$visitor['country'] 	= $record->country->name;
		$visitor['city'] 		= $record->city->name;

		return $visitor;
	}
}