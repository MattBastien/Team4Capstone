# Team4Capstone

This is a small IoT project made for the University of Windsor as an Electrical Engineering
Capstone project.  The goal of this project is to provide a sensor network for monitoring 
and controlling the University of Windsor's Capstone Lab in the CEI building.

At the moment the project has three major components:

* Front End webapp written in JavaScript and PHP
* Back End PYthon code to interface with sensors
* Physical sensor hardware (RFduino code available in this repository)

## Installation

All of the following code can be installed on a linux machine. Remember to set appropriate permissions with chmod
Additionally, a BLED112 dongle is required for interfacing with the BLE sensors.  At the moment all of the code
relies on this dongle even if the machine in question supports BLE natively.

### FRONT END

1. Webcode
   * Apt-get install nginx or Apache
   * If nginx, dump contents in /usr/share/nginx/html -> $WEBROOT
   * Main file -> index.php
      * Contains code for the website landing page
      * Associated CSS files and JS found in $WEBROOT/css and $WEBROOT/js respectively
         * Toggle buttons
      * Code stored in $WEBROOT/toggles
         * Highcharts scripts located in $WEBROOT/stats/
      * Todo: use Ajax to append in realtime
         * Handled by update.php
         * Main framework in chart.new.php
         * Check out (we couldn't get it working)
      * menu.php
         * Allows client to choose parameters of interest (sensor, week, year, etc.)
         * Feeds this info to chart.php
      * chart.php
         * Builds a query based on menu.php
         * Encodes (timestamp,value) into acceptable HighCharts input
         * Prepares js that instantiates the line chart client-side

2. MySQL 
   * Apt-get mysql-server phpmyadmin
   * After config, create a symbolic link
      * sudo ln -s /usr/share/phpmyadmin /usr/share/nginx/html
   * Navigate to http://localhost/phpmyadmin
      * Create a database called status
         * Create tables "toggles" and "sensorDump" with the following headings:
            * toggles
               * name 		(string)
               * value 	(bool)
               * add rows to this table for Dev1, Dev2, ..., DevN, doorOpen, Current
            * sensorDump
               * id 		(tinyint)
               * type 		(string)
               * timestamp 	(bigint)
               * value 	(float)
               * Can be left blank (used as a template for dynamic table creation in pygatt)


3. PHP
   * Apt-get php-fpm5 and mysqldbi(?)
   * Navigate to /etc/nginx/sites-available/default
      * Uncomment fastcgi config
      * Add index.php to available sites
   * Resart the service

4. IP camera 
   * Navigate to $WEBROOT/js/misc.js
      * Update the function which spawn the video pane with camera's IP

### BACK END
1. PyGatt
   * Apt-get install screen
   * Use [Python PIP][1] to install the following libraries:
      * [Bottle][2]
      * [PubSub][3]

2. RFduino Hardware
   * Follow the instructions on the [RFduino GitHub Page][4] to setup the Arduino IDE for development
   * Compile and upload the code located in /RFduino/ from this repository to the RFduino
                
Copy Sensor_Data_Acquisition anywhere
Run main.py in a separate screen

## Acknowledgements

Our group would like to thank both our supervisor Dr. Kemal Tepe and lab technican
Mr. Frank Cicchello for their support and guidance in this project.

[1]: https://docs.python.org/2/installing/
[2]: http://bottlepy.org/docs/dev/index.html
[3]: http://pubsub.sourceforge.net/
[4]: https://github.com/RFduino/RFduino/blob/master/README.md