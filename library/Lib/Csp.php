<?php
class Lib_Csp
{
	const CSP_DIRECTIVE = "Content-Security-Policy";
	const CSP_DIRECTIVE_REPORT_ONLY = "Content-Security-Policy-Report-Only";

	public function __construct($reportOnly, $forceCsp)
	{
		$this->_reportOnly = !empty($reportOnly);
		$this->_forceCsp = !empty($forceCsp);

		$this->_nonce = $this->_generateNonce();
	}

	protected function _generateNonce()
	{
		$keys = array_merge(range(0,9), range('a', 'z'));

		$key = "";
		for($i=0; $i < 32; $i++) {
			$key .= $keys[mt_rand(0, count($keys) - 1)];
		}
		return $key;
	}

	public function getNonce()
	{
		return $this->_nonce;
	}

	public function getCspHeader() {
		$content = $this->_getHeaderContent();

		$name = $this->_reportOnly ? self::CSP_DIRECTIVE_REPORT_ONLY : self::CSP_DIRECTIVE;
		// Manual override
		if ($this->_forceCsp) {
			$name = self::CSP_DIRECTIVE;
		}

		return "$name: $content";
	}

	/**
	 * @see https://developers.google.com/web/fundamentals/security/csp/
	 */
	protected function _getHeaderContent() {
		$bits = array();
		if (CSP_REPORT_ONLY) {
			$bits[] = "report-uri " . CSP_REPORT_URI;
		}
		if (USE_SSL) {
			$bits[] = "upgrade-insecure-requests";
		}

		$bits[] = $this->_scripts();
		$bits[] = $this->_children();


		return implode($bits, '; ');
	}

	/**
	 * For the script-src directive.
	 */
	protected function _scripts() {
		$jsDomains = array();
		$jsDomains[] = "'self'";
		$jsDomains[] = CSP_SCRIPT_SRC;
		$jsDomains[] = 'https://' . CDN_URL;
		$jsDomains[] = "'nonce-" . $this->_nonce . "'";
		return "script-src " . implode($jsDomains, " ");
	}

	/**
	 * For the child-src directive.
	 */
	protected function _children() {
		$frameDomains = array();
		$frameDomains[] = "'self'";
		$frameDomains[] = CSP_FRAME_SRC;
		return "child-src " . implode($frameDomains, " ");
	}
}