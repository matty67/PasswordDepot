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
use OC\AppFramework\Utility\SimpleContainer;
use OCP\IAppManager;

// Get the app manager
$appManager = \OC::$server->get(IAppManager::class);

// Check if the app is already registered
if (!$appManager->isInstalled('passworddepot')) {
    // Register the app manually
    $appManager->enableApp('passworddepot');
}

// Initialize the app
$app = new Application();
