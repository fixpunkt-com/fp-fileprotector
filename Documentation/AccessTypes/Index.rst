..  include:: /Includes.rst.txt

..  _access-types:

============
Access Types
============

An **access type** is a single, self-contained rule that can grant access to a
protected folder. Every access type answers one simple question for a given
protection record: *"Should the current visitor be allowed in?"*

When a folder is requested, fp-fileprotector asks every registered access type
in turn. Access is granted as soon as **one** access type grants it — the
access types are combined with an **OR** conjunction. If no access type grants
access, the request is denied.

fp-fileprotector ships with two access types out of the box:

..  card-grid::
    :columns: 1
    :columns-md: 2
    :gap: 4
    :card-height: 100

    ..  card:: Frontend Login

        Grant access to logged-in frontend users, optionally limited to
        specific users or user groups.

        ..  card-footer:: :ref:`How the frontend access type works <access-type-fe>`
            :button-style: btn btn-secondary stretched-link

    ..  card:: Backend Login

        Grant access to backend users based on their own file storage
        permissions.

        ..  card-footer:: :ref:`How the backend access type works <access-type-be>`
            :button-style: btn btn-secondary stretched-link

..  note::
    The two shipped access types can also be combined on a single folder. A
    visitor is then granted access if they satisfy the frontend rule **or** the
    backend rule.

You are not limited to these two access types. See
:ref:`For Developers <for-developers>` to learn how to add your own.

..  toctree::
    :hidden:

    Fe/Index
    Be/Index
