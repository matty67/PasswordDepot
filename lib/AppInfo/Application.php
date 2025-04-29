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
use OCA\PasswordDepot\Migration\InstallStep;
use OCA\PasswordDepot\Migration\UninstallStep;

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

        // Register migration steps
        $context->registerMigrationStep(InstallStep::class);
        $context->registerMigrationStep(UninstallStep::class);
    }

    public function boot(IBootContext $context) {
        // This method is called when the app is loaded
        $serverContainer = $context->getServerContainer();

        // Register any app-specific services or event listeners here
        // For example, you could register event listeners for user or group events

        // Log that the app has been loaded successfully
        $logger = $serverContainer->get('OCP\ILogger');
        $logger->info('Password Depot app loaded successfully', ['app' => self::APP_ID]);
    }
}
