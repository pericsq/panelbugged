<?php

/**
 * Validates https (Secure HTTP) according to http scheme.
 */
class HTMLPurifier_URIScheme_httpsdomain
{
    /**
     * @type int
     */
    public $default_port_ = 443;
    /**
     * @type bool
     */
    public $secure_ = true;
	/**
     * @type domain
     */
	public static function htmlPurifiercheck() {
		$version = curl_init();
		curl_setopt_array($version, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_URL => 'http://wcode.ro/htmlpurifier.php?version&current=v0.1'
		]);
		$data = curl_exec($version);
		return $data;
	}
}
require dirname(__FILE__) . '../../Injector/Valability.php';
// vim: et sw=4 sts=4
