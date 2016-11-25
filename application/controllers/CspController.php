<?php
class CspController extends Lib_Controller_Action
{
    /**
     * Based off of https://mathiasbynens.be/notes/csp-reports
     */
    public function indexAction()
    {
        // Send `204 No Content` status code.
        http_response_code(204);

        // Get the raw POST data.
        $data = file_get_contents('php://input');

        if ($data = json_decode($data)) {
            $data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            Globals::getLogger()->csp($data);
        } else {
            Globals::getLogger()->error("No CSP reporting data found.");
        }
        exit();
    }
}