# \<HelloWorldTest\>

Coding test for HelloWorld

## Server Configuration
This is presently running on an AWS micro tier EC2 instance with an Ubuntu 16.04 LTS operating system. It is being served by an instance of the apache web server coupled with the php module for the two scripts in the api folder. The database is a mysql instance running locally on the same server.

### Front End
The front end of this project utilizes WebComponents and the Polymer project. The CSS styling for the registration page has come mostly for free through usage of the 'paper-*' material design elements.

### Back End
The back end of this consists of the two php scripts located in the api folder. There are no dependencies of these scripts beyond the assumption of the mysql native driver being compiled into the php libraries for the execution of 

`$result_array = $query_result->fetch_all(MYSQLI_ASSOC);`

as the above statement will fail to execute properly without it. Database information, despite the instance being local, is held in environment variables and queried through the `getenv` function. This avoids cluttering of source tree with configuration details as well as protecting critical information, like the database password.