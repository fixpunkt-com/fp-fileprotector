# TYPO3 Extension: fp-fileprotector

The extension **fp-fileprotector** allows you to restrict access to file storages in TYPO3 and define granular access rules for individual files and folders.

## Features

- **Secure file protection through `.htaccess` and middleware** — protected files are shielded at the web server level and served through a TYPO3 middleware that enforces access on every request.
- **File access based on backend user permissions** — backend users reach exactly those protected files they are already allowed to see and manage in the TYPO3 file list, reusing their existing file mount and storage permissions.
- **File access based on frontend users and groups** — grant access to logged-in frontend users, optionally limited to specific users or user groups.
- **Above all: easy extensibility with your own access types** — add custom access rules with just a small class and a partial, without touching the extension's core. See the [developer documentation](Documentation/ForDevelopers/Index.rst).

## Requirements

- TYPO3 CMS
- Web server with `.htaccess` support (`AllowOverride All`)

## Installation

1. Install the extension via Composer or the TYPO3 Extension Manager.
2. Activate a storage in the backend module **File Protection** (under "Files").
3. Place the `.htaccess` file (template at `Resources/Private/htacces.txt`) in the root directory of the storage.