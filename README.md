# Test task for Z company


### What the module does

#### Frontend:

1) in customer account add a new link to menu
2) when opening the link, show a form with one field "Status" and button "Save"
3) when a customer fills in the status and presses the save button, the status gets saved
4) the status must be displayed in the top right corner. Right after the welcome message.
5) it must work correctly with all the caches enabled

#### Backend:

1) The saved status must be displayed in admin in customer edit page.
2) Admin can change the status


### System requirements

- Magento 2.4.2+
- Customer module is required


### Installation

Use [Composer](https://getcomposer.org/) to install the module:


    composer require victorkon/ztest
