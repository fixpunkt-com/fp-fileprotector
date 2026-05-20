# TYPO3 Extension: fp-fileprotector

The extension **fp-fileprotector** allows you to restrict access to file storages in TYPO3 and define granular access rules for individual files and folders.

## Features

- Protect TYPO3 file storages from public access
- Define access rules per folder with inheritance to subfolders
- Restrict access based on frontend login, user groups, or individual users
- Whitelist and blacklist modes

## Requirements

- TYPO3 CMS
- Web server with `.htaccess` support (`AllowOverride All`)

## Installation

1. Install the extension via Composer or the TYPO3 Extension Manager.
2. Activate a storage in the backend module **File Protection** (under "Files").
3. Place the `.htaccess` file (template at `Resources/Private/htacces.txt`) in the root directory of the storage.