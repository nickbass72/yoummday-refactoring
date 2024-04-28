# Change log

All notable changes to this project will be documented in this file.

## 2024-04-28
**Author**: Nikolay Haralambiev

This refactoring is based on my vision, knowledge, and experience of how the project could look.
I tried to make it cover the well-known practices, structures, and methodology.
In general, my plan was:
1. to move all command line-related files into a dedicated `bin` directory, and to simplify the code inside them.
2. to move all configuration files into a dedicated `config` directory.
3. to create a new `Application` class to handle container building and application running.
4. add the missing stuff like the `Token` entity, and related normalizers and denormalizers.
5. to create a new enum type for the available permission options.
6. to create a new `TokenProviderInterface` to declare the token-providing methods for future providers, for example, a database provider.
7. to create a simple implementation of `TokenProviderInterface` presented by `InMemoryTokenProvider` which reads tokens from the configuration file.
8. to handle properly HTTP requests returning the right status codes and data.
9. to move each part of the application logic to the proper place.
10. to rename `*Handler` classes to `*Controller` because of their role and purpose.
11. to create all needed `unit` and `functional` tests.

I am sure, there are even more stuff that could be refactored, for example:
- handling normalization of the `Token` array
- use constants for the HTTP response status codes

Unfortunately, my weekend passed too fast including some required things besides my work.
Anyway, I think I did a good job! I hope you will like it! :)

### How to test
To test the application behavior in Browser, first start it by issuing `bin/serve` in the terminal inside the project directory.
You can try the following links:
- http://localhost:1337/tokens for listing all available tokens
- http://localhost:1337/tokens/token1234 for showing token details
- http://localhost:1337/tokens/token1234/has-read-permission for showing if a token has a read permission
- http://localhost:1337/tokens/token1234/has-write-permission for showing if a token has a write permission

To run the tests, open the terminal, switch to the project directory, and run `bin/test`.

### Added
- `.gitignore` - added to handle the redundant project stuff
- `phpunit.xml.dist` - configuration for `phpunit`.
- `bin` - new subdirectory to hold command line tools.
- `config` - new subdirectory to hold configuration files.
- `var` - new directory for holding runtime application data.
- `bin/serv` - new script for starting the application. This file is a modified version of the old `src/main.php`.
- `bin/test` - new script (shortcut) to run application tests.
- `config/app.json` - new configuration file based on the old `src/config.json`
- `config/tokens.json` - new configuration for defining available tokens for the new `InMemoryTokenProvider` service.
- `src/Application.php` - new class responsible for initializing and running the application.
- `src/Controller/HasPermissionController.php` - new class for handling token permission requests.
- `src/Controller/ListTokensController.php` - new class for handling the request for listing tokens.
- `src/Controller/ShowTokenController.php` - new class for handling the request for showing token info.
- `src/Entity/Token.php` - new entity representing `Token` data structure.
- `src/Enum/Permission.php` - new `enum` type for the token permission options.
- `src/Exception/TokenProviderException.php` - new exception class specific to `TokenProviderInterface` implementations.
- `src/Provider/TokenProviderInterface.php` - new base interface for different token provider implementations.
- `src/Provider/InMemoryTokenProvider.php` - implementation of `TokenProviderInterface` for in-memory stored tokens. Tokens are loaded in memory reading the `config/tokens.json` configuration file.
- `src/Serializer/TokenSerizlizer.php` - new service for normalizing/denormalizing the `Token` entity.
- `Test/Base/FunctionalTestCase.php` - base class for the `functional` tests.
- `Test/Functional/HasPermissionRequestTest.php` - a functional test for the "has-permission" request.
- `Test/Functional/ListTokensRequestTest.php` - a functional test for the "list tokens" request.
- `Test/Functional/ShowTokenRequestTest.php` - a functional test for the "show token" request.
- `Test/Unit/Controller/HasPermissionControllerTest.php` - a unit test for the `HasPermissionController`.
- `Test/Unit/Controller/ListTokensControllerTest.php` - a unit test for the `ListTokensController`.
- `Test/Unit/Controller/ShowTokenControllerTest.php` - a unit test for the `ShowTokenController`.
### Changed
- `composer.json` - updated with some new dependencies and configurations.
- `composer.lock` - updated according to the changes in `composer.json`.
- `src/config.json` - renamed to `app.json` and moved to the `config` directory
- `src/main.php` - refactored, renamed to `serv`, and moved to the `bin` directory
- `src/route.cache` - this file was removed from the repository. The generation of new ones is directed to the `var` directory.
- `src/Handler` - this directory was renamed to `src/Controller`
- `src/Handler/HasPermissionHandler.php` - this class is moved and refactored as `src/Controller/HasPermissionController.php`