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
    const DB_PASSWORD = "123456";
    const DB = "banglaHadith";

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
        $this->open('bengaliHadith.sqlite');
    }
}



// Remove previous SqlLite Database
if (file_exists("bengaliHadith.sqlite")) {
    unlink('bengaliHadith.sqlite');
}

// Connect MySQL and Create SqlliteFIle
$Generation = new GenerateSQLliteFile;

// Setup SQLLite Database Tables.
$Generation->SQLLiteQuery('CREATE TABLE "android_metadata" ("locale" TEXT);INSERT INTO android_metadata VALUES("en_US");CREATE TABLE "bookcontent" ("answer" VARCHAR,"question" VARCHAR, "note" VARCHAR, "lastUpdate" VARCHAR,"isActive" INTEGER,"id" INTEGER,"bookId" INTEGER,"sectionId" INTEGER, PRIMARY KEY("id"));CREATE TABLE "bookname" ("id" INTEGER, "nameEnglish" VARCHAR, "nameBengali" VARCHAR, "lastUpdate" VARCHAR, "isActive" INTEGER, "typeId" INTEGER, "writerId" INTEGER, "section_number" INTEGER, "content_number" INTEGER, PRIMARY KEY("id"));CREATE TABLE "booksection" ("id" INTEGER, "name" VARCHAR, "lastUpdate" VARCHAR, "isActive" INTEGER, "bookId" INTEGER, "content_number" INTEGER, PRIMARY KEY("id"));CREATE TABLE "booktype" ( "categoryName" VARCHAR, "lastUpdate" VARCHAR, "isActive" INTEGER, "id" INTEGER, PRIMARY KEY("id"));CREATE TABLE "bookwriter" ("nameEnglish" VARCHAR, "nameBengali" VARCHAR, "lastUpdate" VARCHAR, "isActive" INTEGER, "id" INTEGER,PRIMARY KEY( "id"));CREATE TABLE "hadithbook" ("id" INTEGER,"nameEnglish" VARCHAR,"nameBengali" VARCHAR,"lastUpdate" VARCHAR,"isActive" INTEGER,"priority" INTEGER,"publisherId" INTEGER, "section_number" INTEGER, "hadith_number" INTEGER, PRIMARY KEY("id"));CREATE TABLE "hadithchapter" ("id" INTEGER, "nameEnglish" VARCHAR,"nameBengali" VARCHAR, "nameArabic" VARCHAR, "lastUpdate" VARCHAR,"isActive" INTEGER,"bookId" INTEGER,"sectionId" INTEGER,"hadith_number" INTEGER, PRIMARY KEY( "id"));CREATE TABLE "hadithexplanation" ("explanation" VARCHAR,"lastUpdate" VARCHAR,"id" INTEGER,"isActive" INTEGER,"hadithId" INTEGER, PRIMARY KEY("id"));CREATE TABLE "hadithmain" ("note" VARCHAR, "lastUpdate" VARCHAR, "hadithEnglish" VARCHAR, "hadithArabic" VARCHAR, "hadithBengali" VARCHAR, "checkStatus" INTEGER, "hadithNo" INTEGER, "id" INTEGER, "isActive" INTEGER, "chapterId" INTEGER,"bookId" INTEGER, "publisherId" INTEGER, "rabiId" INTEGER,"sectionId" INTEGER,"statusId" INTEGER, PRIMARY KEY("id"));CREATE TABLE "hadithpublisher" ("nameEnglish" VARCHAR, "nameBengali" VARCHAR, "lastUpdate" VARCHAR, "isActive" INTEGER,"id" INTEGER, PRIMARY KEY("id"));CREATE TABLE "hadithsection" ("nameEnglish" VARCHAR,"nameBengali" VARCHAR,"lastUpdate" VARCHAR,"isActive" INTEGER,"id" INTEGER,"serial" INTEGER,"bookId" INTEGER, "hadith_number" INTEGER, PRIMARY KEY("id"));CREATE TABLE "hadithstatus" ("colCode" VARCHAR,"statusEnglish" VARCHAR,"statusBengali" VARCHAR,"lastUpdate" VARCHAR,"isActive" INTEGER,"id" INTEGER, PRIMARY KEY("id"));CREATE TABLE "rabihadith" ("sortBy" VARCHAR, "rabiEnglish" VARCHAR, "lastUpdate" VARCHAR, "rabiBengali" VARCHAR,"isActive" INTEGER,"id" INTEGER, PRIMARY KEY("id"));');


/*

// add Favourite Hadith Table
$Generation->SQLLiteQuery('CREATE TABLE "hadithmainfavourite" ("id" INTEGER, "hadithmain_table_id" INTEGER NOT NULL UNIQUE, PRIMARY KEY("id"))');

// add Favourite Book Content Table
$Generation->SQLLiteQuery('CREATE TABLE "bookcontentfavourite" ("id" INTEGER, "bookcontent_table_id" INTEGER NOT NULL UNIQUE, PRIMARY KEY("id"))');

// Database Settings
$Generation->SQLLiteQuery('CREATE TABLE "settings" ( "id"  INTEGER NOT NULL, "option_name"  TEXT NOT NULL, "option_value"  TEXT NOT NULL, PRIMARY KEY ("id"))');

// Tag List
$Generation->SQLLiteQuery('CREATE TABLE "taglist" ( "id" INTEGER, "tag_name" TEXT, "type" TEXT, PRIMARY KEY ("id") )');

// Tagged Contents
$Generation->SQLLiteQuery('CREATE TABLE "taggedcontents" ( "id" INTEGER, "tag_list_id" INTEGER, "content_id" INTEGER, "content_type" TEXT, PRIMARY KEY ("id"))');

// Tagged List Content Meta
$Generation->SQLLiteQuery('CREATE TABLE "taggedlistmeta" ( "id" INTEGER, "tag_list_id" INTEGER, "meta_key" TEXT, "meta_value" TEXT, PRIMARY KEY ("id"))');

// BookMarked Contents
$Generation->SQLLiteQuery('CREATE TABLE "bookmark" ( "id" INTEGER, "content_id" INTEGER, "book_id" INTEGER, "section_id" INTEGER, "position_in_menu" INTEGER, "position_in_section_list" INTEGER, "content_type" TEXT, PRIMARY KEY ("id"))');


$Generation->SQLLiteQuery("INSERT INTO settings(id,option_name,option_value) VALUES (1,'database_generated','".date("Y-m-d")."')");
$Generation->SQLLiteQuery("INSERT INTO settings(id,option_name,option_value) VALUES (2,'database_updated_last','".date("Y-m-d")."')");
$Generation->SQLLiteQuery("INSERT INTO settings(id,option_name,option_value) VALUES (3,'application_version','5')");
//$Generation->SQLLiteQuery("INSERT INTO settings(id,option_name,option_value) VALUES (3,'bookmarked_hadith',1)");
//$Generation->SQLLiteQuery("INSERT INTO settings(id,option_name,option_value) VALUES (4,'bookmarked_book_content',1)");

$Generation->SQLLiteQuery("INSERT INTO taglist(id,tag_name, type) VALUES (1,'সাধারণ','hadith')");
$Generation->SQLLiteQuery("INSERT INTO taglist(id,tag_name, type) VALUES (2,'সাধারণ','book_content')");

$Generation->SQLLiteQuery("INSERT INTO taggedlistmeta(id,tag_list_id,meta_key,meta_value) VALUES (1,1,'times_tagged',0)");
$Generation->SQLLiteQuery("INSERT INTO taggedlistmeta(id,tag_list_id,meta_key,meta_value) VALUES (2,2,'times_tagged',0)");

*/

// Query Table Table `bookcontent` and Insert Accordingly to SQLLite.

$SqlQuery=$Generation->MySQLQuery("SELECT * FROM `books_content`");
$Generation->SQLLiteQuery("BEGIN TRANSACTION");
while ($row = mysql_fetch_array($SqlQuery)) {
    $Generation->SQLLiteQuery("INSERT INTO bookcontent(id,bookId,sectionId,question,answer,note,isActive,lastUpdate) VALUES (".$row['contentID'].",".$row['bookID'].",".$row['sectionID'].",'".strip_tags(htmlentities($row['MainQ'], ENT_QUOTES))."','".strip_tags(htmlentities($row['MainA'], ENT_QUOTES))."','".strip_tags(htmlentities($row['Mnote'], ENT_QUOTES))."','".$row['active']."','".$row['lastUpdate']."')");
}
$Generation->SQLLiteQuery("END TRANSACTION");
echo 'Insertion to SQL Lite Table: bookcontent completed.<br>';


// Query Table Table `bookname` and Insert Accordingly to SQLLite.

$SqlQuery=$Generation->MySQLQuery("SELECT * FROM `books_name`");
$Generation->SQLLiteQuery("BEGIN TRANSACTION");
while ($row = mysql_fetch_array($SqlQuery)) {

    $Q = $Generation->MySQLQuery("SELECT * FROM `book_section` WHERE bookID=".$row['bookID']);
    $SelctionNumber=mysql_num_rows($Q);
    $Q = $Generation->MySQLQuery("SELECT * FROM `books_content` WHERE bookID=".$row['bookID']);
    $ContentNumber=mysql_num_rows($Q);

    $Generation->SQLLiteQuery("INSERT INTO bookname(id,writerId,typeId,nameBengali,nameEnglish,isActive,lastUpdate,section_number,content_number) VALUES (".$row['bookID'].",".$row['writterID'].",".$row['booktype'].",'".strip_tags(htmlentities($row['Book_nameBD'], ENT_QUOTES))."','".strip_tags(htmlentities($row['Book_nameEN'], ENT_QUOTES))."','".strip_tags(htmlentities($row['Active'], ENT_QUOTES))."','".$row['lastUpdate']."','".$SelctionNumber."','".$ContentNumber."')");

}
$Generation->SQLLiteQuery("END TRANSACTION");
echo 'Insertion to SQL Lite Table: bookname completed.<br>';


// Query Table Table `booksection` and Insert Accordingly to SQLLite.

$SqlQuery=$Generation->MySQLQuery("SELECT * FROM `book_section`");
$Generation->SQLLiteQuery("BEGIN TRANSACTION");
while ($row = mysql_fetch_array($SqlQuery)) {

    $Q = $Generation->MySQLQuery("SELECT * FROM `books_content` WHERE sectionId=".$row['secID']);
    $ContentNumber=mysql_num_rows($Q);

    $Generation->SQLLiteQuery("INSERT INTO booksection(id,bookId,isActive,lastUpdate,name,content_number) VALUES (".$row['secID'].",".$row['BookID'].",".$row['sectionActive'].",'".$row['lastUpdate']."','".strip_tags(htmlentities($row['SectionName'], ENT_QUOTES))."','".$ContentNumber."')");
}
$Generation->SQLLiteQuery("END TRANSACTION");
echo 'Insertion to SQL Lite Table: booksection completed.<br>';


// Query Table Table `booktype` and Insert Accordingly to SQLLite.

$SqlQuery=$Generation->MySQLQuery("SELECT * FROM `books_type`");
$Generation->SQLLiteQuery("BEGIN TRANSACTION");
while ($row = mysql_fetch_array($SqlQuery)) {
    $Generation->SQLLiteQuery("INSERT INTO booktype(id,categoryName,isActive,lastUpdate) VALUES (".$row['btypeID'].",'".strip_tags(htmlentities($row['bookCat'], ENT_QUOTES))."',".$row['activeStatus'].",'".$row['lastUpdate']."')");
}
$Generation->SQLLiteQuery("END TRANSACTION");
echo 'Insertion to SQL Lite Table: booktype completed.<br>';


// Query Table Table `bookwriter` and Insert Accordingly to SQLLite.

$SqlQuery=$Generation->MySQLQuery("SELECT * FROM `book_writter`");
$Generation->SQLLiteQuery("BEGIN TRANSACTION");
while ($row = mysql_fetch_array($SqlQuery)) {
    $Generation->SQLLiteQuery("INSERT INTO bookwriter(id,nameBengali,nameEnglish,isActive,lastUpdate) VALUES (".$row['wrID'].",'".strip_tags(htmlentities($row['writter_nameBN'], ENT_QUOTES))."','".strip_tags(htmlentities($row['writter_nameEN'], ENT_QUOTES))."',".$row['writter_active'].",'".$row['lastUpdate']."');");
}
$Generation->SQLLiteQuery("END TRANSACTION");
echo 'Insertion to SQL Lite Table: bookwriter completed.<br>';


// Query Table Table `hadithbook` and Insert Accordingly to SQLLite.

$SqlQuery=$Generation->MySQLQuery("SELECT * FROM `hadithbook`");
$Generation->SQLLiteQuery("BEGIN TRANSACTION");
while ($row = mysql_fetch_array($SqlQuery)) {

    $Q = $Generation->MySQLQuery("SELECT * FROM `hadithsection` WHERE BookID=".$row['BookID']);
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



// Query Table Table `hadithsection` and Insert Accordingly to SQLLite.

$SqlQuery=$Generation->MySQLQuery("SELECT * FROM `hadithsection`");
$Generation->SQLLiteQuery("BEGIN TRANSACTION");
while ($row = mysql_fetch_array($SqlQuery)) {

    $Q = $Generation->MySQLQuery("SELECT SectionID FROM `hadithmain` WHERE SectionID=".$row['SectionID']);
    $HadithNumber=mysql_num_rows($Q);

    $Generation->SQLLiteQuery("INSERT INTO hadithsection (id,bookId,nameBengali,nameEnglish,isActive,lastUpdate,hadith_number,serial) VALUES (".$row['SectionID'].",".$row['BookID'].",'".strip_tags(htmlentities($row['SectionBD'], ENT_QUOTES))."','".strip_tags(htmlentities($row['SectionEN'], ENT_QUOTES))."',".$row['SecActive'].",'".$row['lastUpdate']."','".$HadithNumber."','".$row['serial']."')");
}
$Generation->SQLLiteQuery("END TRANSACTION");
echo 'Insertion to SQL Lite Table: hadithsection completed.<br>';



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