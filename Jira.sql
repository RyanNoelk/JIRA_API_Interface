CREATE TABLE QC_DEFECT_DATA(
    ERA_QC_DATA_ID INT(11) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (ERA_QC_DATA_ID),
    QC_DEFECT_ID INT (11),
    QC_TEST_CASE_ID VARCHAR(200),
    QC_RELEASE VARCHAR(100),
    QC_SYSTEM VARCHAR (100),
    QC_USERNAME VARCHAR(200),
    QC_STATUS VARCHAR(100),
    QC_LOB VARCHAR(200),
    QC_PRIORITY VARCHAR(200),
    QC_DOMAIN VARCHAR(200),
    QC_PROJECT VARCHAR(200),
    QC_TEST_EXE VARCHAR(200),
    QC_POD VARCHAR(200),
    QC_CAPABILITY VARCHAR(200),
    QC_ENVIRONMENT VARCHAR(200),
    QC_ACTUAL_PROJECT VARCHAR(200),
    QC_PROGRAM VARCHAR(200),
    QC_MODULE VARCHAR(200),
    QC_DETECTED_BY VARCHAR(200),
    QC_IMPACTED_TC VARCHAR(200),

    QC_SUMMARY VARCHAR(1000),
    QC_CLOSING_DATE DATETIME,
    QC_CREATED_DATE DATETIME,
    QC_LAST_UPDATE DATETIME,
    QC_RELEASE_TO_TEST_DATE DATETIME,
    ERA_CREATED_DATE TIMESTAMP);

CREATE INDEX ERA_CREATED_DATEidx on QC_DEFECT_DATA(ERA_CREATED_DATE);
CREATE INDEX QC_DEFECT_IDidx on QC_DEFECT_DATA(QC_DEFECT_ID);
CREATE INDEX QC_RELEASEidx on QC_DEFECT_DATA(QC_RELEASE);
CREATE INDEX QC_LOBidx on QC_DEFECT_DATA(QC_LOB);
CREATE INDEX QC_PROGRAMidx on QC_DEFECT_DATA(QC_PROGRAM);
CREATE INDEX QC_PROJECTidx on QC_DEFECT_DATA(QC_PROJECT);
CREATE INDEX QC_ACTUAL_PROJECTidx on QC_DEFECT_DATA(QC_ACTUAL_PROJECT);
CREATE INDEX QC_PRIORITYidx on QC_DEFECT_DATA(QC_PRIORITY);
CREATE INDEX QC_STATUSidx on QC_DEFECT_DATA(QC_STATUS);
CREATE INDEX QC_SYSTEMidx on QC_DEFECT_DATA(QC_SYSTEM);
CREATE INDEX QC_MODULEidx on QC_DEFECT_DATA(QC_MODULE);
