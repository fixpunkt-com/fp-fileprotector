..  include:: /Includes.rst.txt

..  _access-type-be:

==========================
Backend Login Access Type
==========================

The backend access type grants access based on the login status **and the file
permissions** of a **backend user**. It is implemented in
:php:`Fixpunkt\FpFileprotector\AccessType\BeLoginAccessType`.

Unlike the frontend access type, the backend access type does not rely on a
list of users or groups stored on the protection rule, and it has **no
configuration of its own**. It is always evaluated for every protected folder
and reads the access rights **directly from the logged-in backend user**,
reusing the file mount and file storage permissions that TYPO3 already manages
for that user.

How it works
============

The access type evaluates the request in the following order:

#.  If no backend user is currently logged in, access is denied.

#.  If the backend user is an **administrator**, access is always granted.

#.  Otherwise, the access type looks up the file storage referenced by the
    protection rule among the user's own file storages. Access is granted if
    the protected folder lies **within the file mount boundaries** of that
    storage for the current user.

This means a backend user can access exactly those protected folders they are
already allowed to see and manage in the TYPO3 file list — no additional
configuration per user or group is required.

..  note::
    There is no toggle to enable or disable this access type per folder. Since
    it only ever grants access to backend users who already have the necessary
    file permissions, it is safe to evaluate it on every protected folder.
