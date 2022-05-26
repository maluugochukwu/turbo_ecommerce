<?php
require_once('vendor/autoload.php');
use Mailgun\Mailgun;
use Aws\S3\S3Client;  
use Aws\Exception\AwsException;
define('ACCESS_KEY', "AKIAJIQIBSRLDHN5ZPCQ");
define('SECRET_KEY', "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");

// $mgClient = Mailgun::create('3db02b318065c74819b5e302abe38e94-ba042922-5fdecadf');
//         $domain = "sandboxea9fe5ca9ffb41a6814b1cbcc292ed80.mailgun.org";
//         $params = array(
// //          'from'    => 'STORE 200 <no-reply@store200.com>',
// //          'to'      => "maluugochukwu@gmail.com",
// //          'subject' => "Happy Birthday",
// //          'text'    => "Ok this is the second test"
// //            
//             'from'    => 'STORE 200 <no-reply@store200.com>',
//             'to'      => 'maluugochukwu@gmail.com',
//             'cc'      => 'maluugochukwu@gmail.com',
// //            'bcc'     => 'bar@example.com',
//             'subject' => 'Welcome User',
//             'text'    => 'Testing some Mailgun awesomness!',
//             'html'    => '<html><body><h2></h2>HTML version of the </body><b>body</b></html>'
//         );

// //         Make the call to the client.
// try{
//     $mgClient->messages()->send($domain, $params);
// }
// catch(Exception $e){
//     echo 'Message: ' .$e->getMessage();
// }





// $key    = "AKIAJIQIBSRLDHN5ZPCQ";
// $secret = "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";
// $bucket = "store200.com";
// $s3Client = new S3Client([
//    'region' => 'us-east-2',
//    'version' => '2006-03-01',
//    'credentials' => [
//        'key'    => $key,
//        'secret' => $secret
//    ]
// ]);



// try {
   
//    $result = $s3Client->putObject([
//        'Bucket' => $bucket,
//        'Key' => "testlabel33",
//        'ContentLength' => filesize("img/banner.jpg"),
//        'SourceFile' => "img/banner.jpg",
//     //    'ACL'   => 'public-read'
//    ]);
//    var_dump($result);
//    echo $result['ObjectURL'];
// } catch (AwsException $e) {
//    echo $e->getMessage() . "\n";
// }


//@@@@@@@@@@@@ LIST OBJECT IN BUCKET
// Use the high-level iterators (returns ALL of your objects).
// try {
//     $results = $s3Client->getPaginator('ListObjects', [
//         'Bucket' => $bucket
//     ]);

//     foreach ($results as $result) {
//         foreach ($result['Contents'] as $object) {
//             echo $object['Key'] . PHP_EOL;
//         }
//     }
// } catch (S3Exception $e) {
//     echo $e->getMessage() . PHP_EOL;
// }




//@@@@@@@@@@@@@@@@@@@@@@@@@@

//Listing all S3 Bucket
//$buckets = $s3Client->listBuckets();
//foreach ($buckets['Buckets'] as $bucket) {
//    echo $bucket['Name'] . "<br/>";
//}


function generateOrderID()
    {
        $chars = "0123456789";
        $yr    = date("Y");
        $res = "";
        for ($i = 0; $i < 8; $i++) 
        {
            $res .= $chars[mt_rand(0, strlen($chars)-1)];
        }
        $yr_chunck = str_split($yr);
        $res_chunck = str_split($res);
        // var_dump($yr_chunck); 
        $order_id = "";
        foreach($res_chunck as $key=> $val)
        {
            if($key == "2")
            {
                $order_id .= $yr_chunck[0].$val;
            }
            elseif($key == "4")
            {
                $order_id .= $yr_chunck[1].$val;
            }
            elseif($key == "6")
            {
                $order_id .= $yr_chunck[2].$val;
            }
            elseif($key == "1")
            {
                $order_id .= $yr_chunck[3].$val;
            }else{
                $order_id .= $val;
            }
            
        }
        
        return $order_id;
    }
    echo generateOrderID();