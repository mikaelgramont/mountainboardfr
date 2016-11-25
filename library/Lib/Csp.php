<?php
class Lib_Csp
{
	public static function generateNonce() {
		$keys = array_merge(range(0,9), range('a', 'z'));

		$key = "";
		for($i=0; $i < 32; $i++) {
			$key .= $keys[mt_rand(0, count($keys) - 1)];
		}
		return $key;
	}

	public static function header($nonce) {
		$content = self::_getHeaderContent($nonce);
		$name = CSP_REPORT_ONLY ? "Content-Security-Policy-Report-Only" : "Content-Security-Policy";
		header("$name: $content");
	}

	/**
	 * @see https://developers.google.com/web/fundamentals/security/csp/
	 */
	private static function _getHeaderContent($nonce) {
		$bits = array();
		if (CSP_REPORT_ONLY) {
			$bits[] = "report-uri " . CSP_REPORT_URI;
		}

		$jsDomains = array();
		$jsDomains[] = "'self'";
		$jsDomains[] = CSP_SCRIPT_SRC;
		$jsDomains[] = 'https://' . CDN_URL;
		if ($nonce) {
			$jsDomains[] = "'nonce-" . $nonce . "'";
		}

		$bits[] = "script-src " . implode($jsDomains, " ");

		if (USE_SSL) {
			$bits[] = "upgrade-insecure-requests";
		}
		return implode($bits, '; ');
	}
}