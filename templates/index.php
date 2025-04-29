<?php
script('passworddepot', 'script');
style('passworddepot', 'style');
/** @var $l \OCP\IL10N */
?>

<div id="app">
    <div id="app-navigation">
        <?php print_unescaped($this->inc('navigation/index')); ?>
        <?php print_unescaped($this->inc('settings/index')); ?>
    </div>

    <div id="app-content">
        <div id="app-content-wrapper">
            <div class="app-content-list">
                <div class="app-content-list-item-loading"></div>
            </div>
            <div class="app-content-detail">
                <div class="password-detail-container">
                    <div class="password-detail-empty">
                        <div class="icon-password"></div>
                        <h2><?php p($l->t('No password selected')); ?></h2>
                        <p><?php p($l->t('Select a password on the left or create a new one:')); ?></p>
                        <button class="button primary new-password">
                            <span class="icon-add"></span>
                            <?php p($l->t('New password')); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Templates -->
<script id="content-tpl" type="text/template">
    <div class="password-item" data-id="{{id}}">
        <div class="password-item-title">{{title}}</div>
        <div class="password-item-username">{{username}}</div>
        <div class="password-item-category">{{category}}</div>
    </div>
</script>

<script id="detail-tpl" type="text/template">
    <div class="password-detail">
        <div class="password-detail-header">
            <h2>{{title}}</h2>
            <div class="password-actions">
                <button class="icon-share" title="<?php p($l->t('Share')); ?>"></button>
                <button class="icon-delete" title="<?php p($l->t('Delete')); ?>"></button>
                <button class="icon-edit" title="<?php p($l->t('Edit')); ?>"></button>
            </div>
        </div>
        <div class="password-detail-fields">
            <div class="password-field">
                <label><?php p($l->t('Username')); ?></label>
                <div class="password-field-value">
                    <span>{{username}}</span>
                    <button class="icon-clippy" title="<?php p($l->t('Copy to clipboard')); ?>"></button>
                </div>
            </div>
            <div class="password-field">
                <label><?php p($l->t('Password')); ?></label>
                <div class="password-field-value">
                    <span class="password-hidden">••••••••</span>
                    <button class="icon-toggle" title="<?php p($l->t('Show password')); ?>"></button>
                    <button class="icon-clippy" title="<?php p($l->t('Copy to clipboard')); ?>"></button>
                </div>
            </div>
            <div class="password-field">
                <label><?php p($l->t('URL')); ?></label>
                <div class="password-field-value">
                    <a href="{{url}}" target="_blank">{{url}}</a>
                    <button class="icon-clippy" title="<?php p($l->t('Copy to clipboard')); ?>"></button>
                </div>
            </div>
            <div class="password-field">
                <label><?php p($l->t('Notes')); ?></label>
                <div class="password-field-value">
                    <div class="password-notes">{{notes}}</div>
                </div>
            </div>
            <div class="password-field">
                <label><?php p($l->t('Category')); ?></label>
                <div class="password-field-value">
                    <span>{{category}}</span>
                </div>
            </div>
        </div>
    </div>
</script>

<script id="form-tpl" type="text/template">
    <div class="password-form">
        <h2>{{formTitle}}</h2>
        <form>
            <div class="form-group">
                <label for="title"><?php p($l->t('Title')); ?></label>
                <input type="text" id="title" name="title" value="{{title}}" required>
            </div>
            <div class="form-group">
                <label for="username"><?php p($l->t('Username')); ?></label>
                <input type="text" id="username" name="username" value="{{username}}" required>
            </div>
            <div class="form-group">
                <label for="password"><?php p($l->t('Password')); ?></label>
                <div class="password-input-container">
                    <input type="password" id="password" name="password" value="{{password}}" required>
                    <button type="button" class="icon-toggle password-toggle" title="<?php p($l->t('Show password')); ?>"></button>
                    <button type="button" class="icon-history password-generate" title="<?php p($l->t('Generate password')); ?>"></button>
                </div>
            </div>
            <div class="form-group">
                <label for="url"><?php p($l->t('URL')); ?></label>
                <input type="url" id="url" name="url" value="{{url}}">
            </div>
            <div class="form-group">
                <label for="notes"><?php p($l->t('Notes')); ?></label>
                <textarea id="notes" name="notes">{{notes}}</textarea>
            </div>
            <div class="form-group">
                <label for="category"><?php p($l->t('Category')); ?></label>
                <input type="text" id="category" name="category" value="{{category}}">
            </div>
            <div class="form-actions">
                <button type="button" class="button cancel"><?php p($l->t('Cancel')); ?></button>
                <button type="submit" class="button primary"><?php p($l->t('Save')); ?></button>
            </div>
        </form>
    </div>
</script>

<script id="share-tpl" type="text/template">
    <div class="password-share">
        <h2><?php p($l->t('Share password')); ?></h2>
        <div class="share-with-container">
            <label for="share-with"><?php p($l->t('Share with')); ?></label>
            <div class="share-with-field">
                <select id="share-type">
                    <option value="0"><?php p($l->t('User')); ?></option>
                    <option value="1"><?php p($l->t('Group')); ?></option>
                </select>
                <input type="text" id="share-with" placeholder="<?php p($l->t('Name')); ?>">
                <button class="button primary share-button"><?php p($l->t('Share')); ?></button>
            </div>
        </div>
        <div class="shared-with-list">
            <h3><?php p($l->t('Shared with')); ?></h3>
            <ul class="shares-list">
                <!-- Shares will be listed here -->
            </ul>
        </div>
    </div>
</script>

<script id="share-item-tpl" type="text/template">
    <li class="share-item" data-id="{{id}}">
        <span class="share-item-icon {{shareTypeIcon}}"></span>
        <span class="share-item-name">{{shareWithDisplayName}}</span>
        <button class="icon-delete share-delete" title="<?php p($l->t('Unshare')); ?>"></button>
    </li>
</script>
