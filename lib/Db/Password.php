<?php
namespace OCA\PasswordDepot\Db;

use OCP\AppFramework\Db\Entity;

class Password extends Entity {
    protected $title;
    protected $username;
    protected $password;
    protected $url;
    protected $notes;
    protected $category;
    protected $userId;
    protected $createdAt;
    protected $updatedAt;

    public function __construct() {
        $this->addType('id', 'integer');
        $this->addType('createdAt', 'integer');
        $this->addType('updatedAt', 'integer');
    }

    /**
     * Set creation and update timestamps before saving
     */
    public function beforeSave() {
        $now = time();
        if ($this->getId() === null) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
    }
}