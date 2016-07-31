# Continuum [![Gitter](https://img.shields.io/badge/chat-on_gitter-3F51B5.svg?style=flat-square)](https://gitter.im/flibiowebcontinuum/discussion)

Continuum is a continuous integration artifact storage system built using PHP. Continuum does not do any project testing, but instead stores build artifacts.
This is perfect for CI systems such as [Travis CI](https://travis-ci.org) that only test projects and do not store the artifacts.
Continuum can run on nearly any webserver that can run PHP code, making it accessible to all users. Continuum requires PHP 5.5 or greater.

## Installation

Please follow the steps below to ensure your Continuum install is successful. Continuum must be installed on a PHP server.

1. Download the latest release of Continuum from the [releases page](https://github.com/FlibioWeb/Continuum/releases/latest).
2. Extract the files into your intended directory.
3. Navigate to your Continuum instance in your web browser.
4. Create a new account, used to manage your Continuum instance.
5. After creating an account, login to Continuum.
6. Navigate to the admin page. If you are logged in, there is a button titled "admin" in the upper right corner.
7. Click on the "Update Now" button found near the top of the admin page. This will complete your Continuum installation.

## Project Setup

After installing Continuum, the next step is to setup a project. Continuum requires open-source repositories on GitHub to function.

1. Navigate to the administrator page on Continuum.
2. Add a new project by filling out the respective fields.

After setting up the project on Continuum, you must then setup the project on GitHub and Travis CI.

1. Navigate to [Travis CI](https://travis-ci.org/), and add your repository.
2. Enter the settings page of the repository on Travis, and add an environment variable called `CONTINUUM_TOKEN`.
 - Set the value to the API Token found in the settings page of your Continuum installation. 
 - Make sure you set `Display value in build log` to off, otherwise your Continuum install will be insecure.
3. In the GitHub repository, add a `.travis.yml` file.
4. Modify the `after_success` build step to run a file called `continuum.sh`.
 - An example can be found [here](https://gist.github.com/Flibio/1a34a9e0260fb982b65f30fb0921e3f1#file-travis-yml-L3-L5).
5. Add a file to the GitHub repository called `continuum.sh`. Fill it with the content found [here](https://gist.github.com/Flibio/1a34a9e0260fb982b65f30fb0921e3f1#file-continuum-sh).
6. Configure the `continuum.sh` file, using the comments as guidelines.
