<?php
namespace OCA\PasswordDepot\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

class PasswordMapper extends QBMapper {

    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'passworddepot_passwords', Password::class);
    }

    /**
     * Find a password by id and user id
     * 
     * @param int $id
     * @param string $userId
     * @return Password
     * @throws DoesNotExistException
     * @throws MultipleObjectsReturnedException
     */
    public function find($id, $userId) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
           ->from($this->getTableName())
           ->where(
               $qb->expr()->eq('id', $qb->createNamedParameter($id))
           )
           ->andWhere(
               $qb->expr()->eq('user_id', $qb->createNamedParameter($userId))
           );

        return $this->findEntity($qb);
    }

    /**
     * Find a password that is shared with the user
     * 
     * @param int $id
     * @param string $userId
     * @return Password
     * @throws DoesNotExistException
     * @throws MultipleObjectsReturnedException
     */
    public function findShared($id, $userId) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('p.*')
           ->from($this->getTableName(), 'p')
           ->innerJoin('p', 'passworddepot_shares', 's', 'p.id = s.password_id')
           ->where(
               $qb->expr()->eq('p.id', $qb->createNamedParameter($id))
           )
           ->andWhere(
               $qb->expr()->eq('s.share_type', $qb->createNamedParameter(0))
           )
           ->andWhere(
               $qb->expr()->eq('s.share_with', $qb->createNamedParameter($userId))
           );

        return $this->findEntity($qb);
    }

    /**
     * Find all passwords for a user
     * 
     * @param string $userId
     * @return array
     */
    public function findAllByUser($userId) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
           ->from($this->getTableName())
           ->where(
               $qb->expr()->eq('user_id', $qb->createNamedParameter($userId))
           )
           ->orderBy('title', 'ASC');

        return $this->findEntities($qb);
    }

    /**
     * Find all passwords shared directly with a user
     * 
     * @param string $userId
     * @return array
     */
    public function findAllSharedWithUser($userId) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('p.*')
           ->from($this->getTableName(), 'p')
           ->innerJoin('p', 'passworddepot_shares', 's', 'p.id = s.password_id')
           ->where(
               $qb->expr()->eq('s.share_type', $qb->createNamedParameter(0))
           )
           ->andWhere(
               $qb->expr()->eq('s.share_with', $qb->createNamedParameter($userId))
           )
           ->orderBy('p.title', 'ASC');

        return $this->findEntities($qb);
    }

    /**
     * Find all passwords shared with groups the user belongs to
     * 
     * @param string $userId
     * @return array
     */
    public function findAllSharedWithUserGroups($userId) {
        // This is a placeholder. In a real implementation, you would need to:
        // 1. Get all groups the user belongs to
        // 2. Find all passwords shared with those groups
        
        // For now, we'll return an empty array
        return [];
    }
}