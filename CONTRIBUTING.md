# ToDo & CO - Contributing

---

## Introduction

Here you will find the guide lines to contribute to the project.

### Code of conduct

- Respect everyone!
- Reporting bugs with issues.
- Suggest Enhancements wit Pull Requests from your fork.

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