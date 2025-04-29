<?php
namespace OCA\PasswordDepot\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Controller;
use OCP\IUserSession;
use OCP\IGroupManager;
use OCA\PasswordDepot\Service\PasswordService;
use OCP\AppFramework\Http;

class PasswordController extends Controller {
    private $userId;
    private $passwordService;
    private $userSession;
    private $groupManager;

    public function __construct(
        $AppName,
        IRequest $request,
        IUserSession $userSession,
        IGroupManager $groupManager,
        PasswordService $passwordService,
        $userId
    ) {
        parent::__construct($AppName, $request);
        $this->userId = $userId;
        $this->passwordService = $passwordService;
        $this->userSession = $userSession;
        $this->groupManager = $groupManager;
    }

    /**
     * @NoAdminRequired
     */
    public function index() {
        try {
            $passwords = $this->passwordService->findAll($this->userId);
            return new JSONResponse($passwords);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @NoAdminRequired
     */
    public function show($id) {
        try {
            $password = $this->passwordService->find($id, $this->userId);
            return new JSONResponse($password);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_NOT_FOUND);
        }
    }

    /**
     * @NoAdminRequired
     */
    public function create() {
        $title = $this->request->getParam('title');
        $username = $this->request->getParam('username');
        $password = $this->request->getParam('password');
        $url = $this->request->getParam('url', '');
        $notes = $this->request->getParam('notes', '');
        $category = $this->request->getParam('category', '');

        try {
            $password = $this->passwordService->create(
                $title,
                $username,
                $password,
                $url,
                $notes,
                $category,
                $this->userId
            );
            return new JSONResponse($password, Http::STATUS_CREATED);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     */
    public function update($id) {
        $title = $this->request->getParam('title');
        $username = $this->request->getParam('username');
        $password = $this->request->getParam('password');
        $url = $this->request->getParam('url', '');
        $notes = $this->request->getParam('notes', '');
        $category = $this->request->getParam('category', '');

        try {
            $password = $this->passwordService->update(
                $id,
                $title,
                $username,
                $password,
                $url,
                $notes,
                $category,
                $this->userId
            );
            return new JSONResponse($password);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     */
    public function delete($id) {
        try {
            $this->passwordService->delete($id, $this->userId);
            return new JSONResponse([], Http::STATUS_NO_CONTENT);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     */
    public function share($id) {
        $shareType = $this->request->getParam('shareType');
        $shareWith = $this->request->getParam('shareWith');

        try {
            $share = $this->passwordService->share($id, $shareType, $shareWith, $this->userId);
            return new JSONResponse($share, Http::STATUS_CREATED);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     */
    public function unshare($id, $shareId) {
        try {
            $this->passwordService->unshare($id, $shareId, $this->userId);
            return new JSONResponse([], Http::STATUS_NO_CONTENT);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
        }
    }
}