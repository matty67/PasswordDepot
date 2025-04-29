<?php
namespace OCA\PasswordDepot\Db;

use OCP\AppFramework\Db\Entity;

class Share extends Entity {
    protected $passwordId;
    protected $shareType;
    protected $shareWith;
    protected $sharedBy;
    protected $createdAt;

    public function __construct() {
        $this->addType('id', 'integer');
        $this->addType('passwordId', 'integer');
        $this->addType('shareType', 'integer');
        $this->addType('createdAt', 'integer');
    }

    /**
     * Set creation timestamp before saving
     */
    public function beforeSave() {
        if ($this->getId() === null) {
            $this->setCreatedAt(time());
        }
    }
}