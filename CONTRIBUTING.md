# P8 ToDo & Co - Contributing

---

## Introduction

Here you will find the guide lines to contribute to the project.

In first, you can read the README.md an AUTHENTICATION.md to know how the project works.

### Code of conduct

- Respect everyone!
- Reporting bugs with issues.
- Suggest Enhancements wit Pull Requests from your fork.

### Quality processus

Quality is controlled by the various tools put in place in continuous integration. As well as by the code coverage of PHPUNIT.

Github branches have been configured to only accept pull requests that have passed the continuous integration test (explained in the next section). And a review by at least one of the project managers is mandatory.

Regarding the code coverage of PHPUnit, the goal is to aim for 100% for the pieces of code that are relevant.


### Testing:

You will find a gitlab-ci.yaml file allowing you to set up continuous integration via Gitlab.

For this, you can create a clone of your Github project in Gitlab which will be synchronized with each push or pull request 
and which will play the tests. The result appears in Github.

Tools used :
- SECURITY-CHECKER
- PHPSTAN
- PHPCS
- PHPMD
- PHPMETRICS
- TWIGCS
- PHPUNIT
- GOOGLE LIGHTHOUSE
- BLACKFIRE

### Suggestions

As indicated in the README file, it is necessary to use a redis cache, an Apache server and a MySQL database. 
You can use the docker configuration to mount the project in an Apache and Redis server. The management of the database is entrusted to you.