<html>
<head>
    <title>page title</title>
    <meta charset="UTF-8" />
</head>
<body>
<?php

class GenerateSQLliteFile
{

    public $data = "";

    const DB_SERVER = "localhost";
    const DB_USER = "root";
    const DB_PASSWORD = "";
    const DB = "hadithbd";

    private $db = NULL;
    private $SqlLiteDB=NULL;

    public function __construct()
    {
        error_reporting(-1);
        ini_set('display_errors', 'On');
        ini_set('max_execution_time', 500000000);
        $this->dbConnect();
    }

    private function dbConnect(){
        $this->db = mysql_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD) or die(mysql_error());
        if($this->db)
            mysql_select_db(self::DB,$this->db);

        mysql_query("SET NAMES 'utf8'", $this->db);

        // Create SQLLite File
        $this->SqlLiteDB=new SqlLiteDB();
    }

    public function MySQLQuery($query)
    {
        return mysql_query($query,$this->db);
    }

    public function SQLLiteQuery($query)
    {
        $this->SqlLiteDB->exec($query);
    }

}

class SqlLiteDB extends SQLite3
{
    function __construct()
    {
        $this->open('hb');
    }
}



// Remove previous SqlLite Database
if (file_exists("hb")) {
    unlink('hb');
}

// Connect MySQL and Create SqlliteFIle
$Generation = new GenerateSQLliteFile;

// Setup SQLLite Database Tables.
$Generation->SQLLiteQuery('CREATE TABLE "android_metadata" ("locale" TEXT);INSERT INTO android_metadata VALUES("en_US");');

$Generation->SQLLiteQuery('CREATE TABLE "hadithbook" ("id" INTEGER,"nameEnglish" VARCHAR,"nameBengali" VARCHAR,"lastUpdate" VARCHAR,"isActive" INTEGER,"priority" INTEGER,"publisherId" INTEGER, "section_number" INTEGER, "hadith_number" INTEGER, PRIMARY KEY("id"));');

$Generation->SQLLiteQuery('CREATE TABLE "hadithchapter" ("id" INTEGER, "nameEnglish" VARCHAR,"nameBengali" VARCHAR, "nameArabic" VARCHAR, "lastUpdate" VARCHAR,"isActive" INTEGER,"bookId" INTEGER,"sectionId" INTEGER,"hadith_number" INTEGER, PRIMARY KEY( "id"));');

$Generation->SQLLiteQuery('CREATE TABLE "hadithsection" ("nameEnglish" VARCHAR,"nameBengali" VARCHAR,"lastUpdate" VARCHAR,"isActive" INTEGER,"id" INTEGER,"serial" INTEGER,"bookId" INTEGER, "hadith_number" INTEGER, "range_start" INTEGER, "range_end" INTEGER, PRIMARY KEY("id"));');

$Generation->SQLLiteQuery('CREATE TABLE "hadithexplanation" ("explanation" VARCHAR,"lastUpdate" VARCHAR,"id" INTEGER,"isActive" INTEGER,"hadithId" INTEGER, PRIMARY KEY("id"));');

$Generation->SQLLiteQuery('CREATE TABLE "hadithpublisher" ("nameEnglish" VARCHAR, "nameBengali" VARCHAR, "lastUpdate" VARCHAR, "isActive" INTEGER,"id" INTEGER, PRIMARY KEY("id"));');

$Generation->SQLLiteQuery('CREATE TABLE "hadithstatus" ("colCode" VARCHAR,"statusEnglish" VARCHAR,"statusBengali" VARCHAR,"lastUpdate" VARCHAR,"isActive" INTEGER,"id" INTEGER, PRIMARY KEY("id"));');

$Generation->SQLLiteQuery('CREATE TABLE "rabihadith" ("sortBy" VARCHAR, "rabiEnglish" VARCHAR, "lastUpdate" VARCHAR, "rabiBengali" VARCHAR,"isActive" INTEGER,"id" INTEGER, PRIMARY KEY("id"));');

$Generation->SQLLiteQuery('CREATE TABLE "hadithmain" ("note" VARCHAR, "lastUpdate" VARCHAR, "hadithEnglish" VARCHAR, "hadithArabic" VARCHAR, "hadithBengali" VARCHAR, "checkStatus" INTEGER, "hadithNo" INTEGER, "id" INTEGER, "isActive" INTEGER, "chapterId" INTEGER,"bookId" INTEGER, "publisherId" INTEGER, "rabiId" INTEGER,"sectionId" INTEGER,"statusId" INTEGER, PRIMARY KEY("id"));');


$SqlQuery=$Generation->MySQLQuery("SELECT * FROM `hadithbook`");
$Generation->SQLLiteQuery("BEGIN TRANSACTION");
while ($row = mysql_fetch_array($SqlQuery)) {

    $Q = $Generation->MySQLQuery("SELECT * FROM `hadithsection` WHERE BookID=".$row['BookID']." AND hadithsection.SecActive = 1");
    $SectionNumber=mysql_num_rows($Q);
    $Q = $Generation->MySQLQuery("SELECT SectionID FROM `hadithmain` WHERE BookID=".$row['BookID']);
    $HadithNumber=mysql_num_rows($Q);

    $Generation->SQLLiteQuery("INSERT INTO hadithbook(id,publisherId,nameBengali,nameEnglish,priority,isActive,lastUpdate,section_number,hadith_number) VALUES (".$row['BookID'].",'".$row['PubID']."','".strip_tags(htmlentities($row['BookNameBD'], ENT_QUOTES))."','".strip_tags(htmlentities($row['BookNameEN'], ENT_QUOTES))."',".$row['priority'].",".$row['Active'].",'".$row['lastUpdate']."','".$SectionNumber."','".$HadithNumber."')");

}
$Generation->SQLLiteQuery("END TRANSACTION");
echo 'Insertion to SQL Lite Table: hadithbook completed.<br>';


// Query Table Table `hadithchapter` and Insert Accordingly to SQLLite.

$SqlQuery=$Generation->MySQLQuery("SELECT * FROM `hadithchapter`");
$Generation->SQLLiteQuery("BEGIN TRANSACTION");
while ($row = mysql_fetch_array($SqlQuery)) {

    $Q = $Generation->MySQLQuery("SELECT SectionID FROM `hadithmain` WHERE chapterID=".$row['chapID']);
    $HadithNumber=mysql_num_rows($Q);

    $Generation->SQLLiteQuery("INSERT INTO hadithchapter(id,bookId,sectionId,nameBengali,nameArabic,nameEnglish,isActive,lastUpdate,hadith_number) VALUES (".$row['chapID'].",".$row['BookID'].",".$row['SectionID'].",'".strip_tags(htmlentities($row['ChapterBG'], ENT_QUOTES))."','".strip_tags(htmlentities($row['ChapterAR'], ENT_QUOTES))."','".strip_tags(htmlentities($row['ChapterEN'], ENT_QUOTES))."',".$row['StatusActive'].",'".$row['lastUpdate']."','".$HadithNumber."')");

}
$Generation->SQLLiteQuery("END TRANSACTION");
echo 'Insertion to SQL Lite Table: hadithchapter completed.<br>';


// Query Table Table `hadithsection` and Insert Accordingly to SQLLite.

$SqlQuery=$Generation->MySQLQuery("SELECT *, ( SELECT min(hadithmain.HadithNo) FROM hadithmain WHERE hadithmain.SectionID = hadithsection.SectionID AND hadithmain.BookID = `hadithsection`.`BookID` ) AS range_start, ( SELECT max(hadithmain.HadithNo) FROM hadithmain WHERE hadithmain.SectionID = hadithsection.SectionID AND hadithmain.BookID = `hadithsection`.`BookID` ) AS range_end FROM `hadithsection`");
$Generation->SQLLiteQuery("BEGIN TRANSACTION");
while ($row = mysql_fetch_array($SqlQuery)) {

    $Q = $Generation->MySQLQuery("SELECT SectionID FROM `hadithmain` WHERE SectionID=".$row['SectionID']);
    $HadithNumber=mysql_num_rows($Q);
	
    $Generation->SQLLiteQuery("INSERT INTO hadithsection (id,bookId,nameBengali,nameEnglish,isActive,lastUpdate,hadith_number,serial,range_start,range_end) VALUES (".$row['SectionID'].",".$row['BookID'].",'".strip_tags(htmlentities($row['SectionBD'], ENT_QUOTES))."','".strip_tags(htmlentities($row['SectionEN'], ENT_QUOTES))."',".$row['SecActive'].",'".$row['lastUpdate']."','".$HadithNumber."','".$row['serial']."','".$row['range_start']."','".$row['range_end']."')");
}
$Generation->SQLLiteQuery("END TRANSACTION");
echo 'Insertion to SQL Lite Table: hadithsection completed.<br>';




// Query Table Table `hadithexplanation` and Insert Accordingly to SQLLite.

$SqlQuery=$Generation->MySQLQuery("SELECT * FROM `hadithexplanation`");
$Generation->SQLLiteQuery("BEGIN TRANSACTION");
while ($row = mysql_fetch_array($SqlQuery)) {
    $Generation->SQLLiteQuery("INSERT INTO hadithexplanation(id,hadithId,explanation,isActive,lastUpdate) VALUES (".$row['expID'].",".$row['hadithID'].",'".strip_tags(htmlentities($row['explanation'], ENT_QUOTES))."',".$row['active'].",'".$row['lastUpdate']."')");
}
$Generation->SQLLiteQuery("END TRANSACTION");
echo 'Insertion to SQL Lite Table: hadithexplanation completed.<br>';


// Query Table Table `hadithpublisher` and Insert Accordingly to SQLLite.

$SqlQuery=$Generation->MySQLQuery("SELECT * FROM `hadithsource`");
$Generation->SQLLiteQuery("BEGIN TRANSACTION");
while ($row = mysql_fetch_array($SqlQuery)) {
    $Generation->SQLLiteQuery("INSERT INTO hadithpublisher(id,nameBengali,nameEnglish,isActive,lastUpdate) VALUES (".$row['SourceID'].",'".strip_tags(htmlentities($row['SourceNameBD'], ENT_QUOTES))."','".strip_tags(htmlentities($row['SourceNameEN'], ENT_QUOTES))."','".$row['SourceActive']."','".$row['lastUpdate']."')");
}
$Generation->SQLLiteQuery("END TRANSACTION");
echo 'Insertion to SQL Lite Table: hadithpublisher completed.<br>';


// Query Table Table `hadithstatus` and Insert Accordingly to SQLLite.

$SqlQuery=$Generation->MySQLQuery("SELECT * FROM `hadithstatus`");
$Generation->SQLLiteQuery("BEGIN TRANSACTION");
while ($row = mysql_fetch_array($SqlQuery)) {
    $Generation->SQLLiteQuery("INSERT INTO hadithstatus(id,statusBengali,statusEnglish,colCode,isActive,lastUpdate) VALUES (".$row['StatusID'].",'".strip_tags(htmlentities($row['StatusBG'], ENT_QUOTES))."','".strip_tags(htmlentities($row['StatusEN'], ENT_QUOTES))."','".$row['ColCode']."',".$row['Active'].",'".$row['lastUpdate']."')");
}
$Generation->SQLLiteQuery("END TRANSACTION");
echo 'Insertion to SQL Lite Table: hadithstatus completed.<br>';


// Query Table Table `rabihadith` and Insert Accordingly to SQLLite.

$SqlQuery=$Generation->MySQLQuery("SELECT * FROM `rabihadith`");
$Generation->SQLLiteQuery("BEGIN TRANSACTION");
while ($row = mysql_fetch_array($SqlQuery)) {
    $Generation->SQLLiteQuery("INSERT INTO rabihadith(id,rabiBengali,rabiEnglish,sortBy,isActive,lastUpdate) VALUES (".$row['rabiID'].",'".strip_tags(htmlentities($row['rabiBangla'], ENT_QUOTES))."','".strip_tags(htmlentities($row['rabiEnglish'], ENT_QUOTES))."','".$row['sortBy']."',".$row['active'].",'".$row['lastUpdate']."');");
}
$Generation->SQLLiteQuery("END TRANSACTION");
echo 'Insertion to SQL Lite Table: rabihadith completed.<br>';



// Query Table Table `hadithmain` and Insert Accordingly to SQLLite.

$SqlQuery=$Generation->MySQLQuery("SELECT * FROM `hadithmain`");
$Generation->SQLLiteQuery("BEGIN TRANSACTION");
while ($row = mysql_fetch_array($SqlQuery)) {
    $Generation->SQLLiteQuery("INSERT INTO hadithmain(id,note,lastUpdate,isActive,statusId,checkStatus,rabiId,bookId,publisherId,chapterId,hadithEnglish,sectionId,hadithNo,hadithArabic,hadithBengali) VALUES (".$row['HadithID'].",'".strip_tags(htmlentities($row['HadithNote'], ENT_QUOTES))."','".$row['DateUpdate']."',".$row['HadithActive'].",'".$row['HadithStatus']."','".$row['CheckStatus']."','".$row['RabiID']."','".$row['BookID']."','".$row['SourceID']."','".$row['chapterID']."','".strip_tags(htmlentities($row['EnglishHadith'], ENT_QUOTES))."','".$row['SectionID']."','".$row['HadithNo']."','".strip_tags(htmlentities($row['ArabicHadith'], ENT_QUOTES))."','".strip_tags(htmlentities($row['BanglaHadith'], ENT_QUOTES))."')");
}
$Generation->SQLLiteQuery("END TRANSACTION");
echo 'Insertion to SQL Lite Table: hadithmain completed.<br>';

?>
</body>
<html>