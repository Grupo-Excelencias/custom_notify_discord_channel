CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Features
 * Requirements
 * Installation
 * Configuration
 * Known Issues
 * Maintainers


INTRODUCTION
------------

The "Notify for a Discord channel" module allows you to send notifications to
a channel on the Discord platform of the content that is created on your
website.

This module has an administration interface to configure its functionality.


FEATURES
--------

 * Send notifications for each content created.
 * Configure the URL of the Discord channel to notify.
 * Create a preview with custom fields of the content to share.


REQUIREMENTS
------------

This module not requires other modules outside of Drupal core.


INSTALLATION
------------

 * Install the Notify for a Discord channel module as you would normally install a contributed
 Drupal module. Visit https://www.drupal.org/node/1897420 for further
 information.


CONFIGURATION
-------------

    1. Navigate to Administration > Extend and enable the module.
    2. Navigate to Administration > Configuration > Web services > Form Settings
    Notify for a Discord channel.
    3. Activate the module by checking the "Activate" field.
    4. Specify the URL of the Discord channel to notify in the "Discord Webhook URL" field.
    5. All content types appear in the Settings section. Activate this functionality by
    checking the "Event create" field of the desired content types. Ready !!!.
    With this option the content will be notified with its default preview.
    6. If you want to customize the preview you have to check the "Create preview" field
    and configure the fields that appear below. These fields that appear below are of the
    content type, you can also go to the "User settings" section and configure other fields
    for the preview that come from the users.


KNOWN ISSUES
------------


MAINTAINERS
-----------
* Nolberto Rojas - nolbertinho90@gmail.com
