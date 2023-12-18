# Revision History

This page lists changes for this application.

## Rev. 0.6.0

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

2023-12-18

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
