# Password Depot for Nextcloud

Password Depot is a Nextcloud application that allows you to securely store and manage passwords for users and groups.

## Features

- Secure password storage with encryption
- Share passwords with users or groups
- Organize passwords in categories
- Search functionality
- Password generator
- Copy username/password to clipboard
- Import/export functionality

## Installation

1. Navigate to your Nextcloud apps directory:
   ```
   cd /path/to/nextcloud/apps/
   ```

2. Clone the repository:
   ```
   git clone https://github.com/yourusername/passworddepot.git
   ```

3. Enable the app in Nextcloud:
   - Go to Settings > Apps
   - Click on "Not enabled" category
   - Find "Password Depot" and click "Enable"

Alternatively, you can use the Nextcloud App Store to install the app directly from the Nextcloud admin interface.

## Usage

### Adding a new password

1. Click on the "Password Depot" icon in the Nextcloud navigation menu
2. Click the "New password" button
3. Fill in the password details:
   - Title: A name for the password
   - Username: The username for the account
   - Password: The password for the account
   - URL: The website or service URL (optional)
   - Notes: Additional notes (optional)
   - Category: A category for organizing passwords (optional)
4. Click "Save"

### Viewing and editing passwords

1. Click on a password in the list to view its details
2. Click the edit icon to modify the password
3. Click the delete icon to remove the password

### Sharing passwords

1. Click on a password in the list to view its details
2. Click the share icon
3. Enter the name of the user or group you want to share with
4. Select whether it's a user or group
5. Click "Share"

### Generating secure passwords

1. When creating or editing a password, click the "Generate" button next to the password field
2. A secure password will be generated based on your settings

## Security

Password Depot uses Nextcloud's encryption capabilities to securely store your passwords. All passwords are encrypted before being stored in the database, and are only decrypted when accessed by an authorized user.

## Development

### Building the app

```
make
```

### Running tests

```
make test
```

## License

This app is licensed under the GNU Affero General Public License v3.0. See the [LICENSE](LICENSE) file for details.

## Support

If you encounter any issues or have questions, please file an issue on the [GitHub repository](https://github.com/yourusername/passworddepot/issues).