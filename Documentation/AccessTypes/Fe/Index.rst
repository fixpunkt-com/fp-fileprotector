..  include:: /Includes.rst.txt

..  _access-type-fe:

===========================
Frontend Login Access Type
===========================

The frontend access type grants access based on the login status of a
**frontend user**. It is implemented in
:php:`Fixpunkt\FpFileprotector\AccessType\FeLoginAccessType`.

How it works
============

The access type evaluates the protection record in the following order:

#.  If the *Must be logged in to the frontend* option is **disabled** for the
    folder, the frontend access type never grants access.

#.  If no frontend user is currently logged in, access is denied.

#.  If neither individual users nor user groups are configured on the
    protection rule, **every** logged-in frontend user is granted access.

#.  Otherwise, access is granted if the current frontend user is one of the
    selected **users**, or is a member of one of the selected **user groups**.

..  note::
    Users and user groups are combined with an **OR** conjunction: it is enough
    to match either a selected user or a selected group.

Configuration
=============

The relevant fields are configured directly on the access rule in the
**File Protection** backend module:

..  confval:: Must be logged in to the frontend
    :name: access-type-fe-fe-login

    Master switch for this access type. If disabled, none of the fields below
    have any effect and the frontend access type will not grant access.

..  confval:: Frontend Users
    :name: access-type-fe-users

    Optional list of individual frontend users who may access the folder.

..  confval:: Frontend User Groups
    :name: access-type-fe-user-groups

    Optional list of frontend user groups whose members may access the folder.

If both the users and the user groups list are left empty, any logged-in
frontend user is granted access.
