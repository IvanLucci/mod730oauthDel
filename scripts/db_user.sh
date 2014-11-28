#!/usr/bin/env bash

# =============================================================================
#
# Author:       Artur Tolstenco
# Date:		    15/05/2013
# Description:	This script creates (or deletes) a new mysql user $DB_USER with 
#               $DB_USER_PWD password on $DB_HOST using the root user $DB_ROOT with
#		        $DB_ROOT_PWD password
#		        After creating the user this script also creates one database 
#		        using credentials of the user just created
# Usage:	    bash db_user.sh create|delete mysql_root_user mysql_root_password db_host user_to_create user_password db_to_create
#
# =============================================================================


EXPECTED_ARGS=7
PAR_CREATE='create'
PAR_DELETE='delete'



# PRIVATE FUNCTIONS ############################################################

function check_error() {
    if [ $? -gt 0 ]; then
		echo $1
		exit 1
	fi
}

# creates a new mysql user and a new database
function create() {
    # creating the mysql user
    mysql --host=$DB_HOST --user=$DB_ROOT --password=$DB_ROOT_PWD <<QUERY
CREATE USER '$DB_USER'@'$DB_HOST' IDENTIFIED BY '$DB_USER_PWD';
GRANT ALL ON \`$DB_USER\_%\` . * TO '$DB_USER'@'$DB_HOST';
QUERY
    check_error 'Error creating the new mysql user "'$DB_USER'" with "'$DB_USER_PWD'" password'

    # creating the new database
    mysql --host=$DB_HOST --user=$DB_USER --password=$DB_USER_PWD <<QUERY
CREATE DATABASE  \`$DB_TO_CREATE\` ;
QUERY
    check_error 'Error creating database "'$DB_TO_CREATE'" for mysql user "'$DB_USER'" with "'$DB_USER_PWD'" password'

    echo 'Success creating the new mysql user and the its database'
}

function delete() {
    # delete the database
    mysql --host=$DB_HOST --user=$DB_USER --password=$DB_USER_PWD <<QUERY
DROP DATABASE  \`$DB_TO_CREATE\` ;
QUERY
    check_error 'Error deleting database "'$DB_TO_CREATE'" for mysql user "'$DB_USER'" with "'$DB_USER_PWD'" password'
    
    # deleting the mysql user
    mysql --host=$DB_HOST --user=$DB_ROOT --password=$DB_ROOT_PWD <<QUERY
DROP USER '$DB_USER'@'$DB_HOST';
QUERY
    check_error 'Error deleting the mysql user "'$DB_USER
    
    echo 'Success deleting the mysql user and the its database'
}

################################################################################

# MAIN #########################################################################

if [ $# -ne $EXPECTED_ARGS ]; then
	echo 'Error: some arguments are missing'
	echo 'Usage: '$(basename $0)' '$PAR_CREATE'|'$PAR_DELETE' mysql_root_user mysql_root_password db_host user_to_create user_password db_to_create'
	exit 1
fi

DB_ROOT=$2;
DB_ROOT_PWD=$3
DB_HOST=$4

DB_USER=$5
DB_USER_PWD=$6
DB_TO_CREATE=$7

case $1 in
    $PAR_CREATE)
        create
        ;;
    $PAR_DELETE)
        delete
        ;;
    *)
        echo 'Unrecognized parameter. Allowed parameters: '$PAR_CREATE', '$PAR_DELETE
        exit 1
esac

exit 0
