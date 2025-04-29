<?php
/**
 * Password Depot - Nextcloud app for managing passwords
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 */

namespace OCA\PasswordDepot\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCA\PasswordDepot\Db\PasswordMapper;
use OCA\PasswordDepot\Db\ShareMapper;
use OCA\PasswordDepot\Service\PasswordService;

class Application extends App implements IBootstrap {
    const APP_ID = 'passworddepot';

    public function __construct(array $urlParams = []) {
        parent::__construct(self::APP_ID, $urlParams);
    }

    public function register(IRegistrationContext $context) {
        // Register services
        $context->registerService(PasswordMapper::class, function($c) {
            return new PasswordMapper(
                $c->get('OCP\IDBConnection')
            );
        });

        $context->registerService(ShareMapper::class, function($c) {
            return new ShareMapper(
                $c->get('OCP\IDBConnection')
            );
        });

        $context->registerService(PasswordService::class, function($c) {
            return new PasswordService(
                $c->get(PasswordMapper::class),
                $c->get(ShareMapper::class),
                $c->get('OCP\IUserManager'),
                $c->get('OCP\IGroupManager'),
                $c->get('OCP\Security\ICrypto')
            );
        });
    }

    public function boot(IBootContext $context) {
        // This method is called when the app is loaded
    }
}
