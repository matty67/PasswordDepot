<?php
namespace OCA\PasswordDepot\Service;

use Exception;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCA\PasswordDepot\Db\Password;
use OCA\PasswordDepot\Db\PasswordMapper;
use OCA\PasswordDepot\Db\Share;
use OCA\PasswordDepot\Db\ShareMapper;
use OCP\IUserManager;
use OCP\IGroupManager;
use OCP\Security\ICrypto;

class PasswordService {

    private $passwordMapper;
    private $shareMapper;
    private $userManager;
    private $groupManager;
    private $crypto;

    public function __construct(
        PasswordMapper $passwordMapper,
        ShareMapper $shareMapper,
        IUserManager $userManager,
        IGroupManager $groupManager,
        ICrypto $crypto
    ) {
        $this->passwordMapper = $passwordMapper;
        $this->shareMapper = $shareMapper;
        $this->userManager = $userManager;
        $this->groupManager = $groupManager;
        $this->crypto = $crypto;
    }

    /**
     * Find all passwords for a user
     * 
     * @param string $userId
     * @return array
     */
    public function findAll($userId) {
        // Get passwords owned by the user
        $ownedPasswords = $this->passwordMapper->findAllByUser($userId);
        
        // Get passwords shared with the user
        $sharedPasswords = $this->passwordMapper->findAllSharedWithUser($userId);
        
        // Get passwords shared with groups the user belongs to
        $groupPasswords = $this->passwordMapper->findAllSharedWithUserGroups($userId);
        
        // Merge and return all passwords
        $allPasswords = array_merge($ownedPasswords, $sharedPasswords, $groupPasswords);
        
        // Decrypt sensitive data
        foreach ($allPasswords as &$password) {
            $this->decryptPassword($password);
        }
        
        return $allPasswords;
    }

    /**
     * Find a specific password
     * 
     * @param int $id
     * @param string $userId
     * @return Password
     * @throws DoesNotExistException
     * @throws MultipleObjectsReturnedException
     * @throws Exception
     */
    public function find($id, $userId) {
        try {
            // Try to find a password owned by the user
            $password = $this->passwordMapper->find($id, $userId);
        } catch (DoesNotExistException $e) {
            // If not found, check if it's shared with the user
            try {
                $password = $this->passwordMapper->findShared($id, $userId);
            } catch (DoesNotExistException $e) {
                throw new DoesNotExistException('Password not found or not accessible');
            }
        }
        
        // Decrypt sensitive data
        $this->decryptPassword($password);
        
        return $password;
    }

    /**
     * Create a new password
     * 
     * @param string $title
     * @param string $username
     * @param string $password
     * @param string $url
     * @param string $notes
     * @param string $category
     * @param string $userId
     * @return Password
     */
    public function create(
        $title,
        $username,
        $password,
        $url,
        $notes,
        $category,
        $userId
    ) {
        $passwordEntity = new Password();
        $passwordEntity->setTitle($title);
        $passwordEntity->setUsername($username);
        $passwordEntity->setUrl($url);
        $passwordEntity->setNotes($notes);
        $passwordEntity->setCategory($category);
        $passwordEntity->setUserId($userId);
        
        // Encrypt sensitive data
        $passwordEntity->setPassword($this->crypto->encrypt($password));
        
        return $this->passwordMapper->insert($passwordEntity);
    }

    /**
     * Update an existing password
     * 
     * @param int $id
     * @param string $title
     * @param string $username
     * @param string $password
     * @param string $url
     * @param string $notes
     * @param string $category
     * @param string $userId
     * @return Password
     * @throws DoesNotExistException
     * @throws MultipleObjectsReturnedException
     * @throws Exception
     */
    public function update(
        $id,
        $title,
        $username,
        $password,
        $url,
        $notes,
        $category,
        $userId
    ) {
        try {
            $passwordEntity = $this->passwordMapper->find($id, $userId);
            
            $passwordEntity->setTitle($title);
            $passwordEntity->setUsername($username);
            $passwordEntity->setUrl($url);
            $passwordEntity->setNotes($notes);
            $passwordEntity->setCategory($category);
            
            // Encrypt sensitive data
            $passwordEntity->setPassword($this->crypto->encrypt($password));
            
            return $this->passwordMapper->update($passwordEntity);
        } catch (Exception $e) {
            throw new Exception('Could not update password: ' . $e->getMessage());
        }
    }

    /**
     * Delete a password
     * 
     * @param int $id
     * @param string $userId
     * @return void
     * @throws DoesNotExistException
     * @throws MultipleObjectsReturnedException
     */
    public function delete($id, $userId) {
        try {
            $password = $this->passwordMapper->find($id, $userId);
            $this->passwordMapper->delete($password);
            
            // Delete all shares for this password
            $this->shareMapper->deleteAllForPassword($id);
        } catch (Exception $e) {
            throw new Exception('Could not delete password: ' . $e->getMessage());
        }
    }

    /**
     * Share a password with a user or group
     * 
     * @param int $id
     * @param int $shareType (0 = user, 1 = group)
     * @param string $shareWith
     * @param string $userId
     * @return Share
     * @throws DoesNotExistException
     * @throws MultipleObjectsReturnedException
     * @throws Exception
     */
    public function share($id, $shareType, $shareWith, $userId) {
        try {
            // Verify the password exists and is owned by the user
            $password = $this->passwordMapper->find($id, $userId);
            
            // Verify the share target exists
            if ($shareType === 0) { // User
                if (!$this->userManager->userExists($shareWith)) {
                    throw new Exception('User does not exist');
                }
            } else if ($shareType === 1) { // Group
                if (!$this->groupManager->groupExists($shareWith)) {
                    throw new Exception('Group does not exist');
                }
            } else {
                throw new Exception('Invalid share type');
            }
            
            // Create the share
            $share = new Share();
            $share->setPasswordId($id);
            $share->setShareType($shareType);
            $share->setShareWith($shareWith);
            $share->setSharedBy($userId);
            
            return $this->shareMapper->insert($share);
        } catch (Exception $e) {
            throw new Exception('Could not share password: ' . $e->getMessage());
        }
    }

    /**
     * Remove a share
     * 
     * @param int $id
     * @param int $shareId
     * @param string $userId
     * @return void
     * @throws DoesNotExistException
     * @throws MultipleObjectsReturnedException
     * @throws Exception
     */
    public function unshare($id, $shareId, $userId) {
        try {
            // Verify the password exists and is owned by the user
            $password = $this->passwordMapper->find($id, $userId);
            
            // Verify the share exists and belongs to this password
            $share = $this->shareMapper->find($shareId);
            if ($share->getPasswordId() !== $id) {
                throw new Exception('Share does not belong to this password');
            }
            
            // Delete the share
            $this->shareMapper->delete($share);
        } catch (Exception $e) {
            throw new Exception('Could not unshare password: ' . $e->getMessage());
        }
    }

    /**
     * Decrypt password data
     * 
     * @param Password $password
     * @return void
     */
    private function decryptPassword($password) {
        if ($password->getPassword()) {
            $decryptedPassword = $this->crypto->decrypt($password->getPassword());
            $password->setPassword($decryptedPassword);
        }
    }
}