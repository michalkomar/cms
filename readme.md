[![Join the chat at https://gitter.im/wundermanpraha/cms](https://badges.gitter.im/wundermanpraha/cms.svg)](https://gitter.im/wundermanpraha/cms?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

[Dokumentation] (/docs/cs/docs.md) (CZ)


How to run project
==================

1. CLI: `composer create-project wundermanpraha/cms`
1. CLI: `make setup`
1. Change properties in PROJECT_ROOT/app/config/config.local.neon to your servers
1. Go to root of project and run in CLI: `composer install -o --no-dev`

Dependencies
============

+ PHP >= 5.4
+ MySQL
+ Composer
+ Nginx | Apache 2

Application dependencies
========================
- @Gedmo in entities
	+ https://github.com/Atlantic18/DoctrineExtensions
