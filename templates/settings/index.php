<?php
/** @var $l \OCP\IL10N */
?>

<div id="app-settings">
    <div id="app-settings-header">
        <button class="settings-button" data-apps-slide-toggle="#app-settings-content">
            <?php p($l->t('Settings')); ?>
        </button>
    </div>
    <div id="app-settings-content">
        <ul>
            <li>
                <input type="checkbox" id="show-passwords" class="checkbox">
                <label for="show-passwords"><?php p($l->t('Show passwords by default')); ?></label>
            </li>
            <li>
                <input type="checkbox" id="auto-logout" class="checkbox">
                <label for="auto-logout"><?php p($l->t('Auto-logout after inactivity')); ?></label>
            </li>
            <li>
                <label for="password-generator-length"><?php p($l->t('Password generator length')); ?></label>
                <input type="number" id="password-generator-length" min="8" max="64" value="16">
            </li>
            <li>
                <input type="checkbox" id="password-generator-uppercase" class="checkbox" checked>
                <label for="password-generator-uppercase"><?php p($l->t('Include uppercase letters')); ?></label>
            </li>
            <li>
                <input type="checkbox" id="password-generator-numbers" class="checkbox" checked>
                <label for="password-generator-numbers"><?php p($l->t('Include numbers')); ?></label>
            </li>
            <li>
                <input type="checkbox" id="password-generator-special" class="checkbox" checked>
                <label for="password-generator-special"><?php p($l->t('Include special characters')); ?></label>
            </li>
            <li>
                <button id="export-passwords" class="button">
                    <?php p($l->t('Export passwords')); ?>
                </button>
            </li>
            <li>
                <button id="import-passwords" class="button">
                    <?php p($l->t('Import passwords')); ?>
                </button>
            </li>
        </ul>
    </div>
</div>