<?php
namespace OCA\PasswordDepot\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Controller;
use OCP\Util;
use OCP\IUserSession;

class PageController extends Controller {
    private $userId;
    private $userSession;

    public function __construct($AppName, IRequest $request, IUserSession $userSession, $userId) {
        parent::__construct($AppName, $request);
        $this->userId = $userId;
        $this->userSession = $userSession;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index() {
        Util::addScript($this->appName, 'script');
        Util::addStyle($this->appName, 'style');

        return new TemplateResponse($this->appName, 'index', [
            'user' => $this->userId
        ]);
    }
}