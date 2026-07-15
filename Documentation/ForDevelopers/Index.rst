..  include:: /Includes.rst.txt

..  _for-developers:

==============
For Developers
==============

fp-fileprotector can be extended with your own :ref:`access types <access-types>`.
An access type is a small PHP class that decides whether the current visitor may
access a protected folder, plus the Fluid partials used to display and edit its
settings in the backend module.

At runtime, all registered access types are collected and combined with an
**OR** conjunction: access is granted as soon as one of them grants it. Adding
your own access type therefore never restricts existing rules — it only adds a
new way to *grant* access.

A custom access type consists of the following parts.

..  contents::
    :local:

The access type class
======================

The heart of every access type is a class that implements
:php:`Fixpunkt\FpFileprotector\AccessType\AccessTypeInterface`. Place it under
:file:`Classes/AccessType/<Name>AccessType.php` in your own extension.

..  code-block:: php
    :caption: Classes/AccessType/AccessTypeInterface.php

    interface AccessTypeInterface
    {
        /** @param array<string, mixed> $protection Raw protection database record */
        public function isGranted(array $protection): bool;
        public function getPartials(): string;
    }

:php:`isGranted()`
    Receives the raw protection database record as an associative array and
    returns whether the current visitor should be granted access. Return
    :php:`true` to grant access, :php:`false` to abstain. Because access types
    are combined with **OR**, returning :php:`false` never blocks another
    access type from granting access.

:php:`getPartials()`
    Returns the partial path used to render this access type in the backend
    module, e.g. :php:`'Access/MyType'`. The module renders
    :file:`<path>/Show.html` to display the rule and :file:`<path>/Edit.html`
    to edit it (see :ref:`the partials section <for-developers-partials>`).

..  code-block:: php
    :caption: Classes/AccessType/MyTypeAccessType.php

    <?php

    declare(strict_types=1);

    namespace Vendor\MyExtension\AccessType;

    use Fixpunkt\FpFileprotector\AccessType\AccessTypeInterface;

    class MyTypeAccessType implements AccessTypeInterface
    {
        /** @param array<string, mixed> $protection Raw protection database record */
        public function isGranted(array $protection): bool
        {
            // Your access decision, based on the protection record.
            return (bool)($protection['my_field'] ?? false);
        }

        public function getPartials(): string
        {
            return 'Access/MyType';
        }
    }

Registering the class
----------------------

fp-fileprotector discovers access types through the service container. Your
class must be registered as a service and tagged with
``fp_fileprotector.access``. Add the tag in your extension's
:file:`Configuration/Services.yaml`:

..  code-block:: yaml
    :caption: Configuration/Services.yaml

    services:
      _defaults:
        autowire: true
        autoconfigure: true

      Vendor\MyExtension\:
        resource: '../Classes/*'

      Vendor\MyExtension\AccessType\MyTypeAccessType:
        tags:
          - { name: 'fp_fileprotector.access' }

..  _for-developers-partials:

The partials
============

Each access type provides two Fluid partials, ideally located under
:file:`Access/<Name>/` so they match the path returned by
:php:`getPartials()`:

:file:`Show.html`
    Renders the current settings of the rule read-only. It receives the
    protection record as the :html:`{protection}` variable.

:file:`Edit.html`
    Renders the form fields used to create or edit the rule. It receives all
    available variables (:html:`{_all}`), including the current record.

..  code-block:: html
    :caption: Resources/Private/Partials/Access/MyType/Show.html

    <div class="col-xs-12 col-md-4">
      <div class="form-group">
        <label>My setting:</label><br>
        <f:if condition="{protection.my_field}">
          <f:then><span class="text-success">Yes</span></f:then>
          <f:else><span class="text-danger">No</span></f:else>
        </f:if>
      </div>
    </div>

..  code-block:: html
    :caption: Resources/Private/Partials/Access/MyType/Edit.html

    <div class="checkbox">
      <label>
        <f:form.checkbox name="protection[my_field]" value="1" checked="{record.my_field}" />
        My setting
      </label>
    </div>

Making the partials available to the backend module
----------------------------------------------------

Because the partials live in your own extension, you have to tell the backend
module where to find them. Add your partial folder to the module's
``partialRootPaths`` in TypoScript. Use an index that is not already taken (for
example ``100``) so you do not overwrite the paths shipped with
fp-fileprotector:

..  code-block:: typoscript
    :caption: Configuration/TypoScript/setup.typoscript

    module.tx_fpfileprotector {
      view {
        partialRootPaths {
          100 = EXT:my_extension/Resources/Private/Partials/
        }
      }
    }

The partial path returned by :php:`getPartials()` (e.g. ``Access/MyType``) is
resolved relative to these ``partialRootPaths``.

The TCA override (optional)
===========================

If your access type needs additional fields on the protection record — for
example a checkbox or a relation — add them via a TCA override for the table
:sql:`tx_fpfileprotector_domain_model_protection`.

This step is **optional**: if your access decision does not require any extra
data stored on the rule, you can skip it entirely.

..  code-block:: php
    :caption: Configuration/TCA/Overrides/tx_fpfileprotector_domain_model_protection.php

    <?php

    declare(strict_types=1);

    defined('TYPO3') or die();

    $table = 'tx_fpfileprotector_domain_model_protection';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns($table, [
        'my_field' => [
            'label' => 'My setting',
            'exclude' => 1,
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
    ]);

..  note::
    The values written by your :file:`Edit.html` partial (the ``protection[...]``
    form fields) are what end up in the protection record and are handed to
    :php:`isGranted()`. Make sure the field names in the partial, the TCA
    override and the class match.
