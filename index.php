<?php
//composer require guzzlehttp/guzzle
//composer require guzzle/plugin-cookie ????
// composer require symfony/dom-crawler
// composer require symfony/css-selector ???

//http://docs.guzzlephp.org/en/stable/
//https://symfony.com/doc/current/components/dom_crawler.html
require 'vendor/autoload.php';

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelectorConverter;

$today = new \DateTime();

$loginArray = [
    'app_login_type[username]' => 'admin',
    'app_login_type[password]' => '1234',
];
$url = 'https://erp.411reports.com/security/login';
$client = new \GuzzleHttp\Client(['cookies' => true]);
$response = $client->request('POST', $url, [
    'form_params' =>$loginArray
]);

$html = ''.$response->getBody();
// go get data from url
if($response->getStatusCode() == 200){
    echo "Logged in!!!";

    $response = $client->request('GET', 'https://erp.411reports.com/level');
    $html = ''.$response->getBody();

    echo $html;

}else{
    echo 'Status Code: '.$response->getStatusCode();
}

exit;
//loop through the data
//$crawler = new Crawler($html);
//$nodeValues = $crawler->filter('.navbar-nav > li')->each(function (Crawler $node, $i) {
//    $url = $node->filter('a')->attr('href');
//    return [$url,$node->text()];
//});
//
//$file = fopen('links'.$today->format('Ymd').'.csv', 'w');
//
//foreach($nodeValues as $node){
//    fputcsv($file,$node);
//}
//
//fclose($file);
//
//echo 'CSV created!';
