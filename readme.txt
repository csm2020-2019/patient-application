'Diabetes Assistant' Full-Stack Web Component
***********************
Oliver Earl
ole4@aber.ac.uk

A live version of this application is running on my web space, for your convenience:
http://users.aber.ac.uk/ole4/index.html
Username: wibble
Password: something

License: MIT
Libraries:
Backend:
- PHP-JWT: BSD-3
Frontend:
- Bootstrap 4: MIT
- jQuery: MIT
- JSCookie: MIT
- Sammy, Sammy Templating Engine: MIT
Tools:
- ESLint: MIT
- JMS/Serializer: MIT
- PHPDocumentor: MIT
- PHPMD: BSD-3
- PHPUnit: BSD-3
- JSDoc: Apache

***********************
Deployment Instructions
***********************
If you are marking this work and have received this archive from TurnItIn/Blackboard, all files necessary for running
the application are in the directory, including 3rd-party libraries and compiled phpDocumentor documentation.

If you've downloaded this from Blackboard, the necessary configuration files will be included for your convenience. If
you are downloading this from GitHub/GitLab, you will need to use the *settings-sample.ini* file to construct your own
settings.ini file. This is incredibly important, as without it, the backend application will not run.

The frontend of the application is pointed to the api.php file, the primary entry-point for the backend application,
sitting in the same public_html directory as it by default, but you can go into the main.js file and change this should
you wish to host the backend and frontend on separate servers as they do not communicate directly outside of HTTP in any 
way.

If you are installing this after pulling the Git repository, you will need to run 'composer install' in order to
fetch and install all necessary dependencies and to generate an autoload file that the program depends upon. If
you do not have Composer, please follow the instructions on https://getcomposer.org/ Please note that I have not
had any success in getting Composer to run on Central.

Regardless of whether you're downloading files from Blackboard, or pulling from a repo, you must run Composer in order to 
install PHP dependencies. You can install this on your system, following the instructions on https://getcomposer.org, or you 
can use the provided composer.phar file. The choice is yours. Without getting dependencies or using Composer's autoload
functionalities however, the program simply won't work.

** IN ORDER TO COMMUNICATE WITH A DATABASE STORED ON DB.DCS.ABER.AC.UK, IT MUST BE RAN ON THE UNIVERSITY NETWORK **

When uploading it to a PHP server, please ensure that you're using at least PHP 7.

Unsure of what to do? Here's a rundown:
- Copy all of the web application files into your home directory, one level above your public_html folder. All of the files in
the public_html folder should go into the root of your public_html folder, i.e., this should contain index.html, api.php, etc.
- Run 'php composer.phar update' at your terminal to install PHP dependencies
- Ensure file permissions are okay, either using fixwebperms, or by doing it yourself.
- If you're really not sure, please reach out for help!

***********************
File Directories
***********************
doc - Generated documentation by JSDoc and PHPDocumentor
node_modules - 3rd-party Node.js tools provided by npm
public_html - The web facing portion of the program
src - Source code
tests - PHPUnit unit tests
vendor - 3rd-party PHP libraries and tools provided by Composer

***********************
Running PHPUnit Tests
***********************
You will need Composer - run the command 'composer test' or 'composer.phar run test' to run all PHPUnit tests.

***********************
Running PHP Mess Detector
***********************
You will need Composer - run the command 'composer lint' or 'composer.phar run lint' to run PHPMD.
It will output to stdout so output can be piped.

***********************
Running ESLint
***********************
ESLint is a tool used for checking the overall quality of JavaScript code written in the program. You will need Node.js and npm to install it.
Run 'npm install' to install Node dependencies and will allow you to make use of ESLint in your code editor, or at the command line.

***********************
Viewing JSDoc Documentation
***********************
Navigate to the docs/phpdoc folder, and open index.html in your web browser.
You can generate new documentation by running 'npm run jsdoc' to generate new JS documentation.

***********************
Viewing phpDocumentor Documentation
***********************
Navigate to the docs/jsdoc folder, and open index.html in your web browser.
You can generate new documentation by running 'composer doc' or 'composer.phar run doc' to generate new PHP documentation.

***********************
Troubleshooting
***********************
If you are having further trouble getting the program working, please get in touch and I will try my best to help.