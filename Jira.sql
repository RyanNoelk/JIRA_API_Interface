
/*
 * Holds the core data for the queries to JIRA.
 */
CREATE TABLE JIRA_MINER(
# Primary Key of the Table
    JIRA_MINER_ID INT(11) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (JIRA_MINER_ID),

    # Domain that is being queried
    DOMAIN VARCHAR(30) DEFAULT 'JIRA',

    # Project that is being queried
    PROJECT VARCHAR(100) NOT NULL,

    # Table that the data is being saved to
    SAVE_TABLE VARCHAR(40) NOT NULL,

    # Primary key of the table that the data is being saved to
    SAVE_TABLE_PK VARCHAR(40) NOT NULL,

    # username and password stored like -> username:password
    CREDENTIALS VARCHAR(100) NOT NULL,

    # The domain name of the server that will be hit
    BASE_URL VARCHAR(100) NOT NULL,

    # Max number of resulted to be returned per query
    MAX int(11) NOT NULL,

    # Number of days to go back and fetch
    DAYS int(11) DEFAULT 2,

    # Sets whether the entry will be executed
    ACTIVE BOOL DEFAULT TRUE,

    # Last Update
    LAST_UPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);

/*
 * Contains the Filters that will be used in every query to JIRA to limit the data set.
 *
 * Example:
 *   project	   10825
 */
CREATE TABLE JIRA_FILTERS(
    # Primary Key of the Table
    JIRA_SEARCH_ID INT(11) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (JIRA_SEARCH_ID),

    # Foreign Key to JIRA_MINER
    JIRA_MINER_ID INT(11),
    FOREIGN KEY (JIRA_MINER_ID) REFERENCES JIRA_MINER(JIRA_MINER_ID),

    # JIRA column to be filtered
    JIRA_COLUMN VARCHAR(50),
    # Filter value
    FILTER VARCHAR(50),

    # Last Update
    LAST_UPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);


/*
 * Maps the JIRA columns to the columns of the database that is being written to.
 *
 * Example:
 *   project->name	    QC_ACTUAL_PROJECT
 *   priority->name	    QC_PRIORITY
 *   duedate	          QC_RELEASE_TO_TEST_DATE
 */
CREATE TABLE JIRA_SEARCH(
    # Primary Key of the Table
    JIRA_SEARCH_ID INT(11) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (JIRA_SEARCH_ID),

    # Foreign Key to JIRA_MINER
    JIRA_MINER_ID INT(11),
    FOREIGN KEY (JIRA_MINER_ID) REFERENCES JIRA_MINER(JIRA_MINER_ID),

    # JIRA column to be captured
    JIRA_COLUMN VARCHAR(50),
    # Internal column name of the database to save to
    DB_COLUMN VARCHAR(50),

    # Last Update
    LAST_UPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);

