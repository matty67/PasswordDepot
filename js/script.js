/**
 * Password Depot - Nextcloud app for managing passwords
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 */

(function (OC, window, $, undefined) {
    'use strict';

    $(document).ready(function () {
        // Initialize the app
        var PasswordDepot = function (baseUrl) {
            this._baseUrl = baseUrl;
            this._passwords = [];
            this._currentPassword = null;
            this._currentView = 'list'; // list, detail, edit, create, share
            this._templates = {
                content: $('#content-tpl').html(),
                detail: $('#detail-tpl').html(),
                form: $('#form-tpl').html(),
                share: $('#share-tpl').html(),
                shareItem: $('#share-item-tpl').html()
            };

            this._init();
        };

        PasswordDepot.prototype = {
            _init: function () {
                // Initialize event handlers
                this._initEventHandlers();

                // Load passwords
                this.loadPasswords();
            },

            _initEventHandlers: function () {
                // Password list item click
                $('#app-content').on('click', '.password-item', this._onPasswordClick.bind(this));

                // New password button
                $('#app-content').on('click', '.new-password', this._onNewPasswordClick.bind(this));

                // Edit password button
                $('#app-content').on('click', '.icon-edit', this._onEditPasswordClick.bind(this));

                // Delete password button
                $('#app-content').on('click', '.icon-delete', this._onDeletePasswordClick.bind(this));

                // Share password button
                $('#app-content').on('click', '.icon-share', this._onSharePasswordClick.bind(this));

                // Form submit
                $('#app-content').on('submit', 'form', this._onFormSubmit.bind(this));

                // Form cancel
                $('#app-content').on('click', '.cancel', this._onFormCancel.bind(this));

                // Show/hide password
                $('#app-content').on('click', '.icon-toggle, .password-toggle', this._onTogglePasswordClick.bind(this));

                // Generate password
                $('#app-content').on('click', '.password-generate', this._onGeneratePasswordClick.bind(this));

                // Copy to clipboard
                $('#app-content').on('click', '.icon-clippy', this._onCopyToClipboardClick.bind(this));

                // Share button
                $('#app-content').on('click', '.share-button', this._onShareButtonClick.bind(this));

                // Unshare button
                $('#app-content').on('click', '.share-delete', this._onUnshareButtonClick.bind(this));

                // Navigation
                $('#app-navigation').on('click', '.nav-item a', this._onNavigationClick.bind(this));
            },

            loadPasswords: function () {
                var self = this;
                $.ajax({
                    url: this._baseUrl + '/passwords',
                    type: 'GET',
                    contentType: 'application/json',
                    success: function (response) {
                        self._passwords = response;
                        self._renderList();
                    },
                    error: function (xhr, status, error) {
                        OC.Notification.showTemporary(t('passworddepot', 'Error loading passwords'));
                    }
                });
            },

            _renderList: function () {
                var self = this;
                var $list = $('.app-content-list');
                $list.empty();

                if (this._passwords.length === 0) {
                    $list.append('<div class="empty-content">' +
                        '<div class="icon-password"></div>' +
                        '<h2>' + t('passworddepot', 'No passwords found') + '</h2>' +
                        '<p>' + t('passworddepot', 'Create your first password by clicking the + button') + '</p>' +
                        '</div>');
                    return;
                }

                this._passwords.forEach(function (password) {
                    var html = self._formatTemplate(self._templates.content, password);
                    $list.append(html);
                });
            },

            _renderDetail: function (password) {
                var html = this._formatTemplate(this._templates.detail, password);
                $('.password-detail-container').html(html);
                this._currentView = 'detail';
            },

            _renderForm: function (password, isNew) {
                var data = password || {
                    id: null,
                    title: '',
                    username: '',
                    password: '',
                    url: '',
                    notes: '',
                    category: ''
                };

                data.formTitle = isNew ? t('passworddepot', 'New password') : t('passworddepot', 'Edit password');

                var html = this._formatTemplate(this._templates.form, data);
                $('.password-detail-container').html(html);
                this._currentView = isNew ? 'create' : 'edit';
            },

            _renderShare: function (password) {
                var html = this._formatTemplate(this._templates.share, password);
                $('.password-detail-container').html(html);
                this._currentView = 'share';

                // Load shares
                this._loadShares(password.id);
            },

            _loadShares: function (passwordId) {
                var self = this;
                $.ajax({
                    url: this._baseUrl + '/passwords/' + passwordId + '/shares',
                    type: 'GET',
                    contentType: 'application/json',
                    success: function (response) {
                        self._renderShares(response);
                    },
                    error: function (xhr, status, error) {
                        OC.Notification.showTemporary(t('passworddepot', 'Error loading shares'));
                    }
                });
            },

            _renderShares: function (shares) {
                var self = this;
                var $list = $('.shares-list');
                $list.empty();

                if (shares.length === 0) {
                    $list.append('<li class="empty">' + t('passworddepot', 'Not shared with anyone') + '</li>');
                    return;
                }

                shares.forEach(function (share) {
                    share.shareTypeIcon = share.shareType === 0 ? 'icon-user' : 'icon-group';
                    var html = self._formatTemplate(self._templates.shareItem, share);
                    $list.append(html);
                });
            },

            _onPasswordClick: function (e) {
                e.preventDefault();
                var id = $(e.currentTarget).data('id');
                var password = this._findPassword(id);
                if (password) {
                    this._currentPassword = password;
                    this._renderDetail(password);
                }
            },

            _onNewPasswordClick: function (e) {
                e.preventDefault();
                this._renderForm(null, true);
            },

            _onEditPasswordClick: function (e) {
                e.preventDefault();
                e.stopPropagation();
                this._renderForm(this._currentPassword, false);
            },

            _onDeletePasswordClick: function (e) {
                e.preventDefault();
                e.stopPropagation();
                var self = this;
                var id = this._currentPassword.id;

                OC.dialogs.confirm(
                    t('passworddepot', 'Are you sure you want to delete this password?'),
                    t('passworddepot', 'Delete password'),
                    function (confirmed) {
                        if (confirmed) {
                            self._deletePassword(id);
                        }
                    },
                    true
                );
            },

            _onSharePasswordClick: function (e) {
                e.preventDefault();
                e.stopPropagation();
                this._renderShare(this._currentPassword);
            },

            _onFormSubmit: function (e) {
                e.preventDefault();
                var self = this;
                var $form = $(e.currentTarget);
                var data = {
                    title: $form.find('#title').val(),
                    username: $form.find('#username').val(),
                    password: $form.find('#password').val(),
                    url: $form.find('#url').val(),
                    notes: $form.find('#notes').val(),
                    category: $form.find('#category').val()
                };

                if (this._currentView === 'create') {
                    this._createPassword(data);
                } else {
                    data.id = this._currentPassword.id;
                    this._updatePassword(data);
                }
            },

            _onFormCancel: function (e) {
                e.preventDefault();
                if (this._currentPassword) {
                    this._renderDetail(this._currentPassword);
                } else {
                    $('.password-detail-container').html('<div class="password-detail-empty">' +
                        '<div class="icon-password"></div>' +
                        '<h2>' + t('passworddepot', 'No password selected') + '</h2>' +
                        '<p>' + t('passworddepot', 'Select a password on the left or create a new one:') + '</p>' +
                        '<button class="button primary new-password">' +
                        '<span class="icon-add"></span>' +
                        t('passworddepot', 'New password') +
                        '</button>' +
                        '</div>');
                    this._currentView = 'list';
                }
            },

            _onTogglePasswordClick: function (e) {
                e.preventDefault();
                e.stopPropagation();
                var $button = $(e.currentTarget);
                var $passwordField = $button.closest('.password-field-value, .password-input-container').find('input[type="password"], .password-hidden');

                if ($passwordField.is('input')) {
                    if ($passwordField.attr('type') === 'password') {
                        $passwordField.attr('type', 'text');
                        $button.attr('title', t('passworddepot', 'Hide password'));
                    } else {
                        $passwordField.attr('type', 'password');
                        $button.attr('title', t('passworddepot', 'Show password'));
                    }
                } else {
                    // For the detail view
                    var passwordText = this._currentPassword.password;
                    if ($passwordField.hasClass('password-hidden')) {
                        $passwordField.removeClass('password-hidden').text(passwordText);
                        $button.attr('title', t('passworddepot', 'Hide password'));
                    } else {
                        $passwordField.addClass('password-hidden').text('••••••••');
                        $button.attr('title', t('passworddepot', 'Show password'));
                    }
                }
            },

            _onGeneratePasswordClick: function (e) {
                e.preventDefault();
                e.stopPropagation();
                var length = $('#password-generator-length').val() || 16;
                var useUppercase = $('#password-generator-uppercase').is(':checked');
                var useNumbers = $('#password-generator-numbers').is(':checked');
                var useSpecial = $('#password-generator-special').is(':checked');

                var charset = 'abcdefghijklmnopqrstuvwxyz';
                if (useUppercase) charset += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                if (useNumbers) charset += '0123456789';
                if (useSpecial) charset += '!@#$%^&*()_+~`|}{[]:;?><,./-=';

                var password = '';
                for (var i = 0; i < length; i++) {
                    password += charset.charAt(Math.floor(Math.random() * charset.length));
                }

                $('#password').val(password);
            },

            _onCopyToClipboardClick: function (e) {
                e.preventDefault();
                e.stopPropagation();
                var $button = $(e.currentTarget);
                var $field = $button.closest('.password-field-value').find('span, a');
                var text = $field.text();

                // If it's a password field and it's hidden, get the actual password
                if ($field.hasClass('password-hidden')) {
                    text = this._currentPassword.password;
                }

                this._copyToClipboard(text);
                OC.Notification.showTemporary(t('passworddepot', 'Copied to clipboard'));
            },

            _onShareButtonClick: function (e) {
                e.preventDefault();
                var self = this;
                var shareType = $('#share-type').val();
                var shareWith = $('#share-with').val();

                if (!shareWith) {
                    OC.Notification.showTemporary(t('passworddepot', 'Please enter a user or group name'));
                    return;
                }

                $.ajax({
                    url: this._baseUrl + '/passwords/' + this._currentPassword.id + '/share',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        shareType: shareType,
                        shareWith: shareWith
                    }),
                    success: function (response) {
                        $('#share-with').val('');
                        self._loadShares(self._currentPassword.id);
                        OC.Notification.showTemporary(t('passworddepot', 'Password shared successfully'));
                    },
                    error: function (xhr, status, error) {
                        OC.Notification.showTemporary(t('passworddepot', 'Error sharing password'));
                    }
                });
            },

            _onUnshareButtonClick: function (e) {
                e.preventDefault();
                e.stopPropagation();
                var self = this;
                var $button = $(e.currentTarget);
                var shareId = $button.closest('.share-item').data('id');

                $.ajax({
                    url: this._baseUrl + '/passwords/' + this._currentPassword.id + '/share/' + shareId,
                    type: 'DELETE',
                    success: function (response) {
                        self._loadShares(self._currentPassword.id);
                        OC.Notification.showTemporary(t('passworddepot', 'Share removed successfully'));
                    },
                    error: function (xhr, status, error) {
                        OC.Notification.showTemporary(t('passworddepot', 'Error removing share'));
                    }
                });
            },

            _onNavigationClick: function (e) {
                e.preventDefault();
                var $target = $(e.currentTarget);
                var href = $target.attr('href');

                // Update active state
                $('#app-navigation .nav-item a').removeClass('active');
                $target.addClass('active');

                // Handle different views
                if (href === '#') {
                    this.loadPasswords();
                } else if (href === '#shared') {
                    this._loadSharedPasswords();
                } else if (href === '#favorites') {
                    this._loadFavoritePasswords();
                }
            },

            _createPassword: function (data) {
                var self = this;
                $.ajax({
                    url: this._baseUrl + '/passwords',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    success: function (response) {
                        self._passwords.push(response);
                        self._currentPassword = response;
                        self._renderList();
                        self._renderDetail(response);
                        OC.Notification.showTemporary(t('passworddepot', 'Password created successfully'));
                    },
                    error: function (xhr, status, error) {
                        OC.Notification.showTemporary(t('passworddepot', 'Error creating password'));
                    }
                });
            },

            _updatePassword: function (data) {
                var self = this;
                $.ajax({
                    url: this._baseUrl + '/passwords/' + data.id,
                    type: 'PUT',
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    success: function (response) {
                        // Update the password in the list
                        for (var i = 0; i < self._passwords.length; i++) {
                            if (self._passwords[i].id === response.id) {
                                self._passwords[i] = response;
                                break;
                            }
                        }
                        self._currentPassword = response;
                        self._renderList();
                        self._renderDetail(response);
                        OC.Notification.showTemporary(t('passworddepot', 'Password updated successfully'));
                    },
                    error: function (xhr, status, error) {
                        OC.Notification.showTemporary(t('passworddepot', 'Error updating password'));
                    }
                });
            },

            _deletePassword: function (id) {
                var self = this;
                $.ajax({
                    url: this._baseUrl + '/passwords/' + id,
                    type: 'DELETE',
                    success: function (response) {
                        // Remove the password from the list
                        self._passwords = self._passwords.filter(function (password) {
                            return password.id !== id;
                        });
                        self._currentPassword = null;
                        self._renderList();
                        $('.password-detail-container').html('<div class="password-detail-empty">' +
                            '<div class="icon-password"></div>' +
                            '<h2>' + t('passworddepot', 'No password selected') + '</h2>' +
                            '<p>' + t('passworddepot', 'Select a password on the left or create a new one:') + '</p>' +
                            '<button class="button primary new-password">' +
                            '<span class="icon-add"></span>' +
                            t('passworddepot', 'New password') +
                            '</button>' +
                            '</div>');
                        OC.Notification.showTemporary(t('passworddepot', 'Password deleted successfully'));
                    },
                    error: function (xhr, status, error) {
                        OC.Notification.showTemporary(t('passworddepot', 'Error deleting password'));
                    }
                });
            },

            _loadSharedPasswords: function () {
                // This is a placeholder. In a real implementation, you would load
                // passwords shared with the current user.
                var self = this;
                OC.Notification.showTemporary(t('passworddepot', 'Loading shared passwords...'));
                // For now, just show all passwords
                this._renderList();
            },

            _loadFavoritePasswords: function () {
                // This is a placeholder. In a real implementation, you would load
                // the user's favorite passwords.
                var self = this;
                OC.Notification.showTemporary(t('passworddepot', 'Loading favorite passwords...'));
                // For now, just show all passwords
                this._renderList();
            },

            _findPassword: function (id) {
                for (var i = 0; i < this._passwords.length; i++) {
                    if (this._passwords[i].id === id) {
                        return this._passwords[i];
                    }
                }
                return null;
            },

            _formatTemplate: function (template, data) {
                return template.replace(/\{\{([^}]+)\}\}/g, function (match, key) {
                    return data[key] !== undefined ? data[key] : '';
                });
            },

            _copyToClipboard: function (text) {
                var $temp = $('<input>');
                $('body').append($temp);
                $temp.val(text).select();
                document.execCommand('copy');
                $temp.remove();
            }
        };

        // Initialize the app
        var app = new PasswordDepot(OC.generateUrl('/apps/passworddepot'));
    });

})(OC, window, jQuery);