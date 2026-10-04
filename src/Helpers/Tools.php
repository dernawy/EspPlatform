<?php

namespace App\Helpers;

use App\Entity\SiteSettings;
use App\Entity\Templates;
use App\Entity\Users;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use FilesystemIterator;
use phpDocumentor\Reflection\Types\Integer;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Response;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

define("NULL_STRING",     "NULL");
define("EMPTY_STRING",    "EMPTY");
define("VISITORS_STRING", "Visitors");
define("USERS_STRING",    "users");
define("DEFAULT_STRING_CAP",    "DEFAULT");

define("DEFAULT_SITE_NAME",          "New Site Name");
define("DEFAULT_SITE_SLOGAN",        "New Site Slogan");
define("DEFAULT_SITE_TEMPLATE_NAME", "default");
define("DEFAULT_USR_STYLE_NAME",     "default");

define("SUCCESS_FORM", "SUCCESS_FORM");
define("FAILED_FORM", "FAILED_FORM");
define("INVALID_FORM", "INVALID_FORM");

define("DATABASE_CREAT_FAILED", "DATABASE_CREAT_FAILED");
define("DATABASE_CREATED", "DATABASE_CREATED");
define("DATABASE_EXIST", "DATABASE_EXIST");
define("DATABASE_ABSENT", "DATABASE_ABSENT");

define("NO_SQL_PROCESS", "NO_SQL_PROCESS");
define("SUCCESS_SQL", "SUCCESS_SQL");
define("INVALID_SQL", "INVALID_SQL");

define("NO_TABLE_PROCESS", "NO_TABLE_PROCESS");
define("TABLE_EXIST", "TABLE_EXIST");
define("TABLE_NOT_EXIST", "TABLE_NOT_EXIST");
define("TABLE_CREATED", "TABLE_CREATED");
define("TABLE_CREAT_FAILED", "TABLE_CREAT_FAILED");
define("TABLE_NOT_EMPTY", "TABLE_NOT_EMPTY");
define("TABLE_DELETE_SUCCESS", "TABLE_DELETE_SUCCESS");
define("TABLE_DELETE_FAILED", "TABLE_DELETE_FAILED");

define("ADMIN_DELETE_SUCCESS", "ADMIN_DELETE_SUCCESS");
define("ADMIN_DELETE_FAILED", "ADMIN_DELETE_FAILED");
define("ADMIN_EXIST", "ADMIN_EXIST");
define("ADMIN_NOT_EXIST", "ADMIN_NOT_EXIST");



class Tools extends AbstractController
{

    private string $site_name = 'My New Site';
    private int $count = 0;
    public int $uploaded_img_index = 0;

    public function getSiteName(): string{

        return $this->site_name;
    }

    /**
     * @throws \Exception
     */
    public function getFilenameDate($date = 'now', $format = 'd-m-Y'): string {
        $actual_date = new \DateTime($date);

        return $actual_date->format($format);
    }

    public function saveCkeditorImage($parameters = ['method' => 'POST', 'f_var' => 'file']): Response
    {

        $image_url = '';

        if ($_SERVER['REQUEST_METHOD'] === $parameters['method']) {

            if(isset($_FILES[$parameters['f_var']])){


                $fileName    = $_FILES[$parameters['f_var']]['name'];
                $fileFulPath = $_FILES[$parameters['f_var']]['full_path'];
                $fileTmpPath = $_FILES[$parameters['f_var']]['tmp_name'];
                $fileSize    = $_FILES[$parameters['f_var']]['size'];
                $fileType    = $_FILES[$parameters['f_var']]['type'];
                $fileError   = $_FILES[$parameters['f_var']]['error'];

                $f_size = $this->filesize($fileSize);

                if($f_size > 2){

                    $response = new Response(

                        json_encode(
                            [
                                'dir'        => $parameters['upload_dir_url'],
                                'error'      => $parameters['errors']
                            ]
                        ),
                        403
                    );

                    $response->headers->set('Content-Type', 'application/json');
                    return $response;

                }

                $absolut_url      = $parameters['absolut_url'];

                $filesystem = new Filesystem();

                if(!$filesystem->exists($absolut_url)){
                    $filesystem->mkdir(Path::normalize($absolut_url));
                    //mkdir($uploadFileDir, 0777, true);
                }

                $dest_path = $absolut_url . $fileName;

                if($filesystem->exists($dest_path)) { // if the uploaded file exist with the same name

                    $response = new Response(

                        json_encode(
                            [
                                'dir'        => $parameters['upload_dir_url'],
                                'error'      => $parameters['errors']
                            ]
                        ),
                        403
                    );

                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
                else
                {

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {

                        $image_url = $parameters['image_url'];

                    }
                }
            }
        }

        $response = new Response(

            json_encode(
                [

                    $parameters['success_array']

                ]
            ),
            200
        );

        $response->headers->set('Content-Type', 'application/json');
        return $response;


    }

    public function filesize(mixed $bytes): float|int
    {
        $factor = floor((strlen($bytes) - 1) / 3);
        return $bytes / pow(1024, $factor);
    }

    function human_filesize($bytes, $decimals = 2): string
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

    /* used in generateClientOfferReference function */
    function salt(): ?string {
        return substr(sha1(mt_rand()), 0, 22);
    }

    /* In [ClientsOffersController] when create a new offer this function will create the offer's
       reference that used to create the path for uploaded images in ckeditor also will be the reference of this
       offer in DB
     */
    public function generateCode(Int $code_length, Int $split_length): ?string{
        $text = 'customer@domain.com';
        $hash = md5($text .$this->salt());

        for ($i = 0; $i < 1000; $i++) {
            $hash = md5($hash);
        }

        return implode('-', str_split(substr(strtoupper($hash), 0, $code_length), $split_length));
    }

    public function random_str(int $length = 64, string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): string {

        if ($length < 1) {
            throw new \RangeException("Length must be a positive integer");
        }

        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;

        try {

            for ($i = 0; $i < $length; ++$i) {
                $pieces [] = $keyspace[random_int(0, $max)];
            }
        }
        catch (\Exception $e) {

        }

        return implode('', $pieces);
    }

    public function getCustomPosterFileName($poster_temp_path): string {

        $all_files = scandir($poster_temp_path);
        $keyword = "frame_custom_pos_0"; // your keyword
        $stack = '';

        foreach ($all_files as $file) {
            if (preg_match('/'.$keyword.'/i', $file)) {
                $stack = $file;
            }
        }

        return $stack;
    }

    /* To creat m3u8 for uploaded file [mp4, mpeg, avi] */
    public function creatHlsVideo($ffmpeg){

    }

    /* In [generateReference] when create a new live this function will create the live
       reference that used to create the path for live also will be the reference of this
       live in DB
     */
    public function generateReference(): ?string{
        $text = 'palestine';
        $hash = md5($text .$this->salt());

        for ($i = 0; $i < 1000; $i++) {
            $hash = md5($hash);
        }

        return implode('-', str_split(substr(strtoupper($hash), 0, 8), 4));
    }

    public function generateUniqRef($destination) : string {

        $filesystem  = new Filesystem();

        $reference  = $this->generateReference();
        $check_dest = $destination . '/' . $reference . '/';

        if (!$filesystem->exists($check_dest)) {
            return $reference;
        }
        else
        {
            while ($filesystem->exists($check_dest)) {

                if (!$filesystem->exists($check_dest)) {
                    return $reference;
                }
            }
        }

        return 'ERROR';

    }

    function userAgent(): ?string {
        return $_SERVER["HTTP_USER_AGENT"];
    }

    function isMobile(): bool|int
    {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|iPad|Macintosh|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }

    public function getIpv4(): ?string {

        $file = file_get_contents('http://ip4.me/');

        // Trim IP based on HTML formatting
        $pos = strpos( $file, '+3' ) + 28;
        $end = strpos( $file, '\n' ) + 12;
        $ip = substr( $file, $pos, $end );

        return $ip;
    }

    public function getNetworkInterfaces() :string {
        return json_encode(net_get_interfaces(), JSON_PRETTY_PRINT);
    }

    public function getIpv6(): ?string {

        $file = file_get_contents('http://ip6.me/api/');



        // Trim IP based on HTML formatting
        $pos = strpos( $file, '6,' ) + 2;
        $end = strpos( $file, ',v' ) - 5;
        //dd(substr( $file, $pos, $end ));
        return substr( $file, $pos, $end );
    }

    public function makeSiteDefaultsSettings(EntityManagerInterface $em, Users $user, string $siteName): void
    {

        $encoders    = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer  = new Serializer($normalizers, $encoders);

        $settings    = new SiteSettings();

        $site_general_settings  = [

            "general" =>  [
                "site_name"        => $this->site_name,
                "slogan"           => "automation",
                "show_navbar"      => 'YES',
                "show_brand_div"   => 'YES',
                "show_slogan_div"  => 'YES',
                "brand_text"       => 'YES',
                "brand_image"      => 'YES',
                "brand_image_path" => "/images/templates/logo.png",
            ],
        ];

        $site_general_content  = $serializer->serialize($site_general_settings, 'json');

        $site_general_data  = json_decode($site_general_content, true);

        $settings->setUser($user);
        $settings->setSiteName($siteName);
        $settings->setSiteSetting($site_general_data);

        $em->persist($settings);
        $em->flush();

    }

    public function makeSiteDefaultTemplate(EntityManagerInterface $em, Users $user, string $siteName, string $siteSlogan, string $templateNam): void
    {

        $templates = new Templates();

        $templates->setUser($user);
        $templates->setTemplateName($templateNam);
        $templates->setTemplateActive(true);
        $templates->setSiteName($siteName);
        $templates->setSiteSlogan($siteSlogan);

        $em->persist($templates);
        $em->flush();
    }

    public function createDb(){

    }

    /**
     * @throws Exception
     */
    public function dbExist(EntityManagerInterface $em, string $dbName): ?bool
    {
        $sql = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME ="."'$dbName';";

        $statement = $em->getConnection()->prepare($sql);

        $result = $statement->executeQuery();

        if($result->rowCount() > 0){
            return true;
        }

        return false;

    }

    /**
     * @throws Exception
     */
    public function dbTableExist(EntityManagerInterface $em, string $dbName, string $tableName): ?array
    {

        $sql = "SELECT IF(EXISTS(SELECT * FROM information_schema.tables WHERE table_schema ="."'$dbName'"." AND table_name ='$tableName'), 'YES', 'NO') as EXIST;";

        $db = $em->getConnection();

        $statement = $db->prepare($sql);

        $result = $statement->executeQuery();

        return $result->fetchAssociative();

    }

    /**
     * @throws Exception
     */
    public function importSqlFile(mixed $form, EntityManagerInterface $em, string $file, string $dbName, string $tableName): bool
    {

        $db = $em->getConnection();

        if(file_exists($file)){

            $sql = trim(file_get_contents($file));

            if($sql){

                $querySet = $db->prepare($sql);

                $querySet->executeQuery();

                $db->close();

                if($this->dbTableExist($em, $dbName, $tableName)){
                    return true;
                }
            }
            else
            {
                throw new Exception("File {$file} appears to be empty.");
            }
        }
        else
        {
            throw new Exception("File '{$file}' not found.");
        }

        return false;
    }

    /**
     * @throws Exception
     */
    public function deleteDbEmptyTable(EntityManagerInterface $em, ?string $db, ?string $table): array
    {

        $sql       = "DROP TABLE `$table`";
        $statement = $em->getConnection()->prepare($sql);
         $statement->executeQuery();

        return $this->dbTableExist($em, $db, $table);
    }
}

