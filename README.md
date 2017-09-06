# PHP_CLI_site_parser

Functionality:
--------------
CLI application-parser of the site according to the specified url with the following functionality:
1. The parse command - starts the parser, accepts the mandatory parameter url (both with the protocol, and without).
1.1. When navigating through the transmitted url, the application finds all the pictures located on the page, and saves their full paths and the source page.
1.2. On the page analyzed, it finds all the links leading to other pages of the given domain, for each of these pages, items 1.1 are executed. and 1.2.
1.3. At the end of the command, the command returns a reference to the CSV file with the analysis results.
2. The report command - displays the analysis results for the domain to the console, accepts the required domain parameter (both with the protocol and without).
3. The help command displays a list of commands with explanations.

Instructions:
-------------
First you must clone repository and install composer. For this, in the command line or terminal run the command:

<div>git clone https://github.com/DmitryKorobko/PHP_CLI_site_parser.git</div>

If composer installed global, in the application directory run command:

<div>composer install</div>

For run application, go to app/parser directory and run command:

<div>php Parser.php</div>

And now you can use commands of application(parse, report, help). For get list of all commands
run this command:

<div>help</div>