# How to contribute

Everybody should be able to help. Here's how you can make this project more
awesome:

1. [Fork it](https://github.com/jolicode/JoliNotif/fork_select)
2. improve it
3. submit a [pull request](https://help.github.com/articles/creating-a-pull-request)

Your work will then be reviewed as soon as possible (suggestions about some
changes, improvements or alternatives may be given).

Here's some tips to make you the best contributor ever:

* [Green tests](#green-tests)
* [Standard code](#standard-code)
* [Keeping your fork up-to-date](#keeping-your-fork-up-to-date)

## Green tests

Run the tests using the following script:

    vendor/bin/phpunit

## Standard code

Use [PHP CS fixer](http://cs.sensiolabs.org/) to make your code compliant with
JoliNotif's coding standards:

    vendor/bin/php-cs-fixer fix .

## Keeping your fork up-to-date

To keep your fork up-to-date, you should track the upstream (original) one
using the following command:

    git remote add upstream https://github.com/jolicode/JoliNotif.git

Then get the upstream changes:

    git checkout master
    git pull --rebase origin master
    git pull --rebase upstream master
    git checkout <your-branch>
    git rebase master

Finally, publish your changes:

    git push -f origin <your-branch>

Your pull request will be automatically updated.
