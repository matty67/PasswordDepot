<?php
/** @var $l \OCP\IL10N */
?>

<ul>
    <li class="nav-item">
        <a href="#" class="active">
            <img src="<?php print_unescaped(image_path('passworddepot', 'app.svg')); ?>" alt="">
            <?php p($l->t('All passwords')); ?>
        </a>
    </li>
    <li class="nav-item">
        <a href="#shared">
            <img src="<?php print_unescaped(image_path('core', 'actions/share.svg')); ?>" alt="">
            <?php p($l->t('Shared with me')); ?>
        </a>
    </li>
    <li class="nav-item">
        <a href="#favorites">
            <img src="<?php print_unescaped(image_path('core', 'actions/star.svg')); ?>" alt="">
            <?php p($l->t('Favorites')); ?>
        </a>
    </li>
</ul>

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