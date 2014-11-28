#!/usr/bin/env bash

# =============================================================================
#
# Author:       Artur Tolstenco
# Date:		    15/05/2013
# Description:	This script creates (or deletes) a new mysql user and its database. 
#               It also populates the database with suitable data
#
# modif. 19/05/2014 Giuseppe Ciaccio
#
# =============================================================================

CURRENT_DIR=$(dirname "$0")
CLEAN_EXPECTED_ARGS=1
CREATE_EXPECTED_ARGS=2

PAR_CREATE='create'
PAR_CLEAN='clean'

PAR_ALL='all'
PAR_CREATE_USER='create_user'
PAR_POPULATION='populate'


DB_ROOT='root'
DB_ROOT_PWD='root'
DB_HOST='localhost'

DB_USER='authserver'
DB_USER_PWD='random85'
DB_NAME='authserver_zf'
DB_SCHEMA=$CURRENT_DIR'/schema.mysql.sql'
DB_DATA=$CURRENT_DIR'/data.mysql.sql'


# PRIVATE FUNCTIONS ############################################################

# checks if the database population files exists
function check_files() {
    if [ ! -f $DB_SCHEMA ] || [ ! -f $DB_DATA ]; then
        echo 'The population files for "'$DB_NAME'" database does not exist'
        exit 1
    fi
}

# checks if there was an error during execution of previous comand 
# and prints the $1 message
function check_error() {
    if [ $? -gt 0 ]; then
		echo $1
		exit 1
	fi
}

# creates a new mysql database user
function create_user() {
    bash $CURRENT_DIR/db_user.sh create $DB_ROOT $DB_ROOT_PWD $DB_HOST \
	                                     $DB_USER $DB_USER_PWD \
	                                     $DB_NAME
    check_error 'Error creating the new user or its database...'
	echo 'The new user and its databases were created successfully'
}

# populates the DB
function populate() {
    check_files
    php $CURRENT_DIR/load.mysql.php -s $DB_SCHEMA -d $DB_DATA -w
    check_error 'Error during the database population...'
    echo 'Databases populated successfully'
    
    echo
    echo '================================================================================'
    echo 'The database connection params must be handcoded in application/configs/application.ini:'
    echo 'username = '$DB_USER
    echo 'password = '$DB_USER_PWD
    echo 'host = '$DB_HOST
    echo 'dbname = '$DB_NAME
    echo '================================================================================'
    echo
}

function all() {
    create_user
    populate
}

function create() {
    case $1 in
        $PAR_CREATE_USER)
            create_user
            ;;
        $PAR_POPULATION)
            populate
            ;;
        $PAR_ALL)
            all
            ;;
        *)
            echo 'Unrecognized parameter. Allowed parameters: '$PAR_ALL', '$PAR_CREATE_USER', '$PAR_POPULATION
            exit 1
    esac
}

function clean() {    
    bash $CURRENT_DIR/db_user.sh delete $DB_ROOT $DB_ROOT_PWD $DB_HOST \
	                                     $DB_USER $DB_USER_PWD \
	                                     $DB_NAME
    check_error 'Error deleting user and its databases...'
    echo 'The mysql db user and its databases were deleted successfully'
}

################################################################################


# MAIN #########################################################################

if [ $# -lt $CLEAN_EXPECTED_ARGS ]; then
    echo 'Error: some arguments are missing'
    echo 'Usage: '$(basename $0)' '$PAR_CREATE'|'$PAR_CLEAN\
                                   $PAR_ALL'|'$PAR_CREATE_USER'|'$PAR_POPULATION
        exit 1
fi

case $1 in
    $PAR_CLEAN)
        clean
        ;;

    $PAR_CREATE)
        if [ $# -ne $CREATE_EXPECTED_ARGS ]; then
	        echo 'Error: some arguments are missing'
	        echo 'Usage: '$(basename $0)' '$PAR_CREATE'|'$PAR_CLEAN\
	                                       $PAR_ALL'|'$PAR_CREATE_USER'|'$PAR_POPULATION
	        exit 1
        fi
        
        create $2
        ;;
    *)
        echo 'Unrecognized parameter. Allowed parameters: '$PAR_CREATE', '$PAR_CLEAN
esac

exit 0
