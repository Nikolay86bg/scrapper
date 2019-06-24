<?php
//composer require guzzlehttp/guzzle
//composer require guzzle/plugin-cookie ????
// composer require symfony/dom-crawler
// composer require symfony/css-selector ???

//http://docs.guzzlephp.org/en/stable/
//https://symfony.com/doc/current/components/dom_crawler.html
//composer require symfony/css-selector
require 'vendor/autoload.php';

use Symfony\Component\DomCrawler\Crawler;

$today = new \DateTime();

$loginArray = [
    'email' => 'petarivanov2012@gmail.com',
    'password' => '123456789',
];
$url = 'https://skyphone.bg/login';
$client = new \GuzzleHttp\Client(['cookies' => true]);
$response = $client->request('POST', $url, [
    'form_params' =>$loginArray
]);

$html = ''.$response->getBody();
// go get data from url
if($response->getStatusCode() == 200){
    echo "Logged in!!!";

//    $response = $client->request('GET', 'https://test.com/level');
//    $html = ''.$response->getBody();
//    echo $html;

    $crawler = new Crawler($html);
    $nodeValues = $crawler->filter('.sub-menu > li')->each(function (Crawler $node, $i) {
        $categoryUrl = $node->filter('a')->attr('href');
        if (strpos($categoryUrl, '#') !== false) {

        }else{
            return $categoryUrl;
        }
    });
echo '<pre>';
    print_R($nodeValues);
//    $file = fopen('links'.$today->format('Ymd').'.csv', 'w');
//
//    foreach($nodeValues as $node){
//        fputcsv($file,$node);
//    }
//
//    fclose($file);
//
//    echo 'CSV created!';



}else{
    echo 'Status Code: '.$response->getStatusCode();
}

exit;
//loop through the data

