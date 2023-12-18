# Api Alarm Manager
Creates WordPress Rest API endpoint for contal alarms

## Devcontainer
* Copy `.devcontainer/env.example` to `.devcontainer/env` and fill out the variable values inside. Then restart/rebuild container.

## PHP Tests
Municipio uses [PHPUnit](https://phpunit.de/) for unit testing. For mocking and stubbing we use [WP_Mock](https://wp-mock.gitbook.io/). This means that you can use WP_Mock, [Mockery](https://github.com/mockery/mockery)(since this is a wrapper for WP_Mock) and PHPUnit_MockObject for mocking and stubbing.

### PHPUnit Tests file structure
All tests are stored in the `tests/phpunit/tests` folder. The file structure should mirror the file structure of the theme. The file name should be the same as the file you want to test. For example, if you want to test the file `src/Controller/Base.php` you should create the file `tests/phpunit/tests/Controller/Base.php`. To avoid having too large test files, you can instead create a folder with the same name as the file you want to test and put the test files inside. Please note that for separating files by which class function you are testing, you should name the file e.g. `Base.functionName.php`.

### Running PHPUnit tests
Run `composer test` in the terminal.

### Running PHPUnit tests with code coverage
Run `composer test:coverage` in the terminal. This will generate a code coverage report in the `tests/phpunit/.coverage` folder.
