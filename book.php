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
        $this->open('ob');
    }
}



// Remove previous SqlLite Database
if (file_exists("ob")) {
    unlink('ob');
}

// Connect MySQL and Create SqlliteFIle
$Generation = new GenerateSQLliteFile;

// Setup SQLLite Database Tables.
$Generation->SQLLiteQuery('CREATE TABLE "android_metadata" ("locale" TEXT);INSERT INTO android_metadata VALUES("en_US");');

$Generation->SQLLiteQuery('CREATE TABLE "bookcontent" ("answer" VARCHAR,"question" VARCHAR, "note" VARCHAR, "lastUpdate" VARCHAR,"isActive" INTEGER,"id" INTEGER,"bookId" INTEGER,"sectionId" INTEGER, PRIMARY KEY("id"));');

$Generation->SQLLiteQuery('CREATE TABLE "bookname" ("id" INTEGER, "nameEnglish" VARCHAR, "nameBengali" VARCHAR, "lastUpdate" VARCHAR, "isActive" INTEGER, "typeId" INTEGER, "writerId" INTEGER, "section_number" INTEGER, "content_number" INTEGER, PRIMARY KEY("id"));');

$Generation->SQLLiteQuery('CREATE TABLE "booksection" ("id" INTEGER, "name" VARCHAR, "lastUpdate" VARCHAR, "isActive" INTEGER, "bookId" INTEGER, "content_number" INTEGER, PRIMARY KEY("id"));');

$Generation->SQLLiteQuery('CREATE TABLE "booktype" ( "categoryName" VARCHAR, "lastUpdate" VARCHAR, "isActive" INTEGER, "id" INTEGER, PRIMARY KEY("id"));');

$Generation->SQLLiteQuery('CREATE TABLE "bookwriter" ("nameEnglish" VARCHAR, "nameBengali" VARCHAR, "lastUpdate" VARCHAR, "isActive" INTEGER, "id" INTEGER,PRIMARY KEY( "id"));');


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


?>
</body>
<html>