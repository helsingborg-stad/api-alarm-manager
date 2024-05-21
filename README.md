# Api Alarm Manager
Creates WordPress Rest API endpoints for contal alarms and fire danger levels. 

## Alarms
Alarms are imported from a remote FTP/SFTP server. The alarms are then stored in the WordPress database and can be fetched from the WordPress Rest API.
After the alarms have been imported, they are moved to an archive folder on the remote server. For the alarms to be archived on the remote server, the const `API_ALARM_MANAGER_ARCHIVE_ALARMS_ON_REMOTE` must be set to `true`. Otherwise the alarm files on the remote server will note be altered.

## Fire danger levels
Fire danger levels are posts that are created in WordPress. The posts are then fetched from the WordPress Rest API.

## Constants
define('API_ALARM_MANAGER_ARCHIVE_ALARMS_ON_REMOTE', true); - REQUIRED to archive alarms on remote server.

## Devcontainer
This project uses a devcontainer for development. This means that you can use VS Code to develop the project. To use the devcontainer, you need to install the [Remote - Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers) extension for VS Code. When you have installed the extension, you can open the project in a container by clicking the green button in the bottom left corner of VS Code and select "Remote-Containers: Reopen in Container".

### Devcontainer ftp/sftp services
To make it easier to test this plugins functionality on your local machine you can use the ftp/sftp services that are installed in the devcontainer. The services and their credentials are listed in the `./devcontainer/docker-compose.yml` file.

Use the following details to set up a connection to either ftp or sftp from the WordPress admin panel:

#### SFTP
* server: sftp
* username: demo
* password: demo
* FTP/SFTP folder: /files
* FTP/SFTP archive folder: /archive

#### FTP
* server: ftp
* username: demo
* password: demo
* FTP/SFTP folder: /files
* FTP/SFTP archive folder: /archive

### Devcontainer ftp/sftp test files
A number of files for testing locally are available in the `./devcontainer/remoteFiles`. To prepare or reset test files run the shell script `./.vscode/tasks/reset-dummy-files` from the terminal: 
```bash
sh ./vscode/tasks/reset-dummy-files.sh
```

## PHP Tests
Municipio uses [PHPUnit](https://phpunit.de/) for unit testing. For mocking and stubbing we use [WP_Mock](https://wp-mock.gitbook.io/). This means that you can use WP_Mock, [Mockery](https://github.com/mockery/mockery)(since this is a wrapper for WP_Mock) and PHPUnit_MockObject for mocking and stubbing.

### PHPUnit Tests file structure
All tests are stored in the `tests/phpunit/tests` folder. The file structure should mirror the file structure of the theme. The file name should be the same as the file you want to test. For example, if you want to test the file `src/Controller/Base.php` you should create the file `tests/phpunit/tests/Controller/Base.php`. To avoid having too large test files, you can instead create a folder with the same name as the file you want to test and put the test files inside. Please note that for separating files by which class function you are testing, you should name the file e.g. `Base.functionName.php`.

### Running PHPUnit tests
Run `composer test` in the terminal.

### Running PHPUnit tests with code coverage
Run `composer test:coverage` in the terminal. This will generate a code coverage report in the `tests/phpunit/.coverage` folder.
