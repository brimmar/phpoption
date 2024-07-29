# Contributing to PHP Option Type

First off, thank you for considering contributing to PHP Option Type! It's people like you that make PHP Option Type such a great tool.

## Where do I go from here?

If you've noticed a bug or have a feature request, make sure to check our [Issues](https://github.com/brimmar/phpoption/issues) page to see if someone else in the community has already created a ticket. If not, go ahead and make one!

## Fork & create a branch

If this is something you think you can fix, then fork PHP Option Type and create a branch with a descriptive name.

A good branch name would be (where issue #325 is the ticket you're working on):

```sh
git checkout -b 325-add-japanese-translations
```

## Get the test suite running

Make sure you're using PHP 8.0 or higher. Install dependencies using Composer:

```sh
composer install
```

Now you should be able to run the entire test suite using:

```sh
vendor/bin/pest
```

## Implement your fix or feature

At this point, you're ready to make your changes! Feel free to ask for help; everyone is a beginner at first.

## Make a Pull Request

At this point, you should switch back to your master branch and make sure it's up to date with PHP Option Type's master branch:

```sh
git remote add upstream git@github.com:brimmar/phpoption.git
git checkout master
git pull upstream master
```

Then update your feature branch from your local copy of master, and push it!

```sh
git checkout 325-add-japanese-translations
git rebase master
git push --set-upstream origin 325-add-japanese-translations
```

Go to the PHP Option repo and press the "Compare & pull request" button.

## Keeping your Pull Request updated

If a maintainer asks you to "rebase" your PR, they're saying that a lot of code has changed, and that you need to update your branch so it's easier to merge.

To learn more about rebasing in Git, there are a lot of [good](https://git-scm.com/book/en/v2/Git-Branching-Rebasing) [resources](https://www.atlassian.com/git/tutorials/rewriting-history/git-rebase) but here's the suggested workflow:

```sh
git checkout 325-add-japanese-translations
git pull --rebase upstream master
git push --force-with-lease 325-add-japanese-translations
```

## Code review

A team member will review your pull request and provide feedback. Please be patient as pull requests are often reviewed in batches.

## And finally...

Thank you for contributing!
