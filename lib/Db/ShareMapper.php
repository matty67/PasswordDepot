<?php
namespace OCA\PasswordDepot\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

class ShareMapper extends QBMapper {

    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'passworddepot_shares', Share::class);
    }

    /**
     * Find a share by id
     * 
     * @param int $id
     * @return Share
     * @throws DoesNotExistException
     * @throws MultipleObjectsReturnedException
     */
    public function find($id) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
           ->from($this->getTableName())
           ->where(
               $qb->expr()->eq('id', $qb->createNamedParameter($id))
           );

        return $this->findEntity($qb);
    }

    /**
     * Find all shares for a password
     * 
     * @param int $passwordId
     * @return array
     */
    public function findAllByPasswordId($passwordId) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
           ->from($this->getTableName())
           ->where(
               $qb->expr()->eq('password_id', $qb->createNamedParameter($passwordId))
           );

        return $this->findEntities($qb);
    }

    /**
     * Find a specific share for a password and user/group
     * 
     * @param int $passwordId
     * @param int $shareType
     * @param string $shareWith
     * @return Share
     * @throws DoesNotExistException
     * @throws MultipleObjectsReturnedException
     */
    public function findSpecificShare($passwordId, $shareType, $shareWith) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
           ->from($this->getTableName())
           ->where(
               $qb->expr()->eq('password_id', $qb->createNamedParameter($passwordId))
           )
           ->andWhere(
               $qb->expr()->eq('share_type', $qb->createNamedParameter($shareType))
           )
           ->andWhere(
               $qb->expr()->eq('share_with', $qb->createNamedParameter($shareWith))
           );

        return $this->findEntity($qb);
    }

    /**
     * Delete all shares for a password
     * 
     * @param int $passwordId
     * @return void
     */
    public function deleteAllForPassword($passwordId) {
        $qb = $this->db->getQueryBuilder();

        $qb->delete($this->getTableName())
           ->where(
               $qb->expr()->eq('password_id', $qb->createNamedParameter($passwordId))
           );

        $qb->execute();
    }
}