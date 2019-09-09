<?php
// All new code for Ghoster 2.2 based on AGSLayouts class

class DiviGhosterAntiBot {
	
	public static function run() {
		if (!empty($_SERVER['REMOTE_ADDR'])) {
			$clientIp = $_SERVER['REMOTE_ADDR'];
			$isIPv6 = strpos($clientIp, ':') !== false;
			$bannedIps = get_transient($isIPv6 ? 'divi_ghoster_antibot_ips' : 'divi_ghoster_antibot_ipsv6');
			$bannedIps = $bannedIps === false ? self::getBannedIps($isIPv6) : explode(' ', $bannedIps);
			if (!empty($bannedIps)) {
				if (in_array($clientIp, $bannedIps)) {
					exit;
				}
			}
		}
	}
	
	public static function getBannedIps($v6) {
		$bannedIps = 
			$v6 ? array(
			
			)
			: array(
			
			);
		$bannedHosts = array(
			'wpthemedetector.com'
		);
		
		$lookupType = $v6 ? DNS_AAAA : DNS_A;
		$lookupTypeString = $v6 ? 'AAAA' : 'A';
		$lookupField = $v6 ? 'ipv6' : 'ip';
		foreach ($bannedHosts as $host) {
			$records = dns_get_record($host, $lookupType);
			if (!empty($records)) {
				foreach ($records as $record) {
					if ($record['type'] == $lookupTypeString && !empty($record[$lookupField])) {
						$bannedIps[] = $record[$lookupField];
					}
				}
			}
		}
		
		set_transient($v6 ? 'divi_ghoster_antibot_ips' : 'divi_ghoster_antibot_ipsv6', implode(' ', $bannedIps), 86400);
		
		return $bannedIps;
	}
	
}

DiviGhosterAntiBot::run();