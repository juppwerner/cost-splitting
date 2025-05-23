# Revision History

This page lists changes for this application.

## 1.1.0, 1.1.1

2025-03-31

* Added exporting and [importing](../costproject/import) a cost project

## 1.0.0

2025-03-31

* Lots of fixes

## 0.9.4

2024-08-09

* Cost Project View: Added button Add Expense] on top of view
* Improved mobile views

## 0.9.3

2024-06-17

* Expense: Fixed sorting participants

## 0.9.2

2024-06-17

* [Expenses](../expense/index) Search: added title
* [Cost Project](../costproject) mobile view: reduced some fields
* Added automatic detection of mobile/tablet device in order to select appropriate device view (mobile/desktop)

## 0.9.1

2024-06-14

* [Home Page](../site/index): Added welcome message after login, and eventually a link to confugre the user's profile
* [Home Page](../site/index): Fixed site title and breadcrumbs title
* Improved styling of grid view pagination buttons

## 0.9.0

2024-06-13

* [Cost Breakdown](../costproject): Added export as PDF. Open the PDF from the _breakdown_ view

## 0.8.7

2024-06-06

* [Cost Project Form](../costproject/create): Added link to sort participants
* User Module: Updated German translations
 
## 0.8.6

2024-06-04

* New Expense added: improved success message

## 0.8.5

2024-06-04

* Improved email templates

## 0.8.3, 0.8.4

2024-06-04

* Splitted Expense: allow comma as decimal separator

## Rev. 0.8.1, 0.8.2

2024-06-03

* Cost Breakdown: Changed layout of participant info cards to _column_
* Bugfix - Removed unnecessary output

## Rev. 0.8.0

2024-01-29

* New module: [Credits](../order)

## Rev. 0.7.2

2024-01-23

* Bugfix orders

## Rev. 0.7.0

2024-01-23

* Added PayPal payment to see cost breakdown in projects

## Rev. 0.6.0

2023-12-18

* Cost Projects table: added showing and searching for sum of expense amounts
* Cost Project Form : Rearranged fields order
* Create New Project: return to previous URL if user exceeded max. nbr. of projects
* Cost Breakdown: Rearranged expense table columns
* Expense Form: Added showing / changing participants weightings table on change of participants select field
* Expenses: Added showing weightings for custom splitting expenses
* Expenses: Added splitting expense using distribution
* Expense Form: Added default values for titles  auto-complete
* Expense Form: Added default titles for auto-complete
* Expense Form: Changed calculation of currency to use reverse rate
* Expense Create: Set currency from project
* User Password Recovery: Changed fields to passwordInput
* Home Page: Added messages regarding Login/Register
* General: Improved mobile views

## Rev. 0.5.6

2023-11-27

* [Cost Project](../costproject): Improved error validations, success messages when managing project users

## Rev. 0.5.5

2023-11-06

* Cost Breakdown: Fixed rounding deviations

## Rev. 0.5.4

2023-10-24

* Changed to codemix/yii2-configloader

## Rev. 0.5.3

2023-10-11

* [Expenses](../expense): Reduced currencies dropdown list to only EWF currencies

## Rev. 0.5.2

2023-10-09

* Removed package nemmo/attachments

## Rev. 0.5.0

2023-10-08

* [Expenses](../expense): Added documents/attachments

## Rev 0.4.0

2023-09-29

* Changed app versioning system to a semantic versioning

## Rev. 24

2023-09-28

* [Expenses](../expense): Renamed participant to recipient
* [Expense](../expense) Form: Added preventing mousewheel scroll in amount number field and exchangerate number fields

## Rev. 23

2023-09-28

* User Registration: Registration and confirmation now redirect to home page

## Rev. 22

2023-09-28

* Added merging email parameters

## Rev. 21

2023-09-28

* New User Registration: Assign default role _author_

## Rev. 20

2023-09-28

* [Cost Projects](../costproject): Added access checks to only allow owner user to add other users
* User Preferences: Fixed setting user profile timezone
* Improved email layouts

## Rev. 19

2023-09-26

* Rearranged menue
* Added translations
* Added ChartJS to show breakdown balances as bar chart

## Rev. 18

2023-09-23

* Changed exchange rates service to own service via EZB data

## Rev. 17

2023-09-22

* [Register](../user/register), [Recover Password](../user/forgot): Added _Password (confirm)_ field
* Home Page: Added help message for first project if no project exists yet
* [Expense](../expense/index): Added typeahead for title field

## Rev. 16

2023-09-21

* [Home Page](../site/index): Added showing login/register buttons for guest users
* [Cost Projects](../costproject/index): Added parameter max. number of user cost projects

## Rev. 15

2023-09-21

* Homepage cards: increased padding
* Translated [contact form](../site/contact)

## Rev. 14

2023-09-21

* News Item View: Fixed internal error

## Rev. 13

2023-09-21

* [Homepage](../site/index): Count of expenses now only shows user expenses
* Languages Menue: Added country flags
* Expenses Views: Added button icons
* Expenses Grid: Only show project filter for user projects
* Adding a new expense: Show message when there is no cost project yet
* Forms: hint-block now red
* New Cost Project: Added intro message

## Rev. 12

2023-09-21

* Changed internal mailer library
* Harmonized table CSS

## Rev. 11

2023-09-20

* Harmonized Gridview table styles
* Added $ sign as brand icon
* Changed jomepage card buttons to button group
* Create new expense: added default expense type
* Added User Costproject n:m
* Expenses: Expense Title: Add badge if type is 'money transfer' Added showing participants
* Expense: Added expense/expenseType
* Added feather icons
* New Cost Project created: added flash message with link to add new expense
* Create next new expense: pass last ID to copy some values from previous expense

## Rev. 10

2023-09-14

* [Cost Project](../costproject): Complete rewrite of cost breakdown
* [Cost Project](../costproject): Cost Breakdown: Added messages about compensation payments between participants
* [Cost Project](../costproject): Added initially showing/hiding currency dropdown depending on _Use currency_

## Rev. 9

2023-06-27

* New expense: set default date to today

## Rev.6

2022-10-16

* Tidied up the README file

## Rev.5

2022-10-16

* Added currency to cost items
* Added Docker [installation howto](../INSTALL-docker.md) 
* Many small bug fixes

## Rev.4

2022-09-10

* [Ausgabe](../expense): Field _Payed By_ is now required
* Tidied up html

## Rev.3

2022-09-09

* Added more information to [README](../../README.md) and [INSTALL](../../INSTALL.mkd) files

## Rev.1

2022-09-04

* Initial version


[startpage]: ../site/index

<!-- vim:set ai sw=4 sts=4 et fdc=4: -->
