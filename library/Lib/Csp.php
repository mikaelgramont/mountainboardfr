<?php
class Lib_Csp
{
	public static function header() {
		$content = self::_getHeaderContent();
		$name = CSP_REPORT_ONLY ? "Content-Security-Policy-Report-Only" : "Content-Security-Policy";
		header("$name: $content");
	}

	/**
	 * @see https://developers.google.com/web/fundamentals/security/csp/
	 */
	private static function _getHeaderContent() {
		$bits = array();
		if (CSP_REPORT_ONLY) {
			$bits[] = "report-uri " . CSP_REPORT_URI;
		}

		$bits[] = "script-src 'self' " . CSP_SCRIPT_SRC;

		if (USE_SSL) {
			$bits[] = "upgrade-insecure-requests: 1";
		}
		return implode($bits, '; ');
	}
}