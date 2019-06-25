<?php
//composer require guzzlehttp/guzzle
// composer require symfony/dom-crawler
// composer require symfony/css-selector

//http://docs.guzzlephp.org/en/stable/
//https://symfony.com/doc/current/components/dom_crawler.html
require 'vendor/autoload.php';

use Symfony\Component\DomCrawler\Crawler;

function getMenuCategoriesUrl($html)
{
    $crawler = new Crawler($html);
    return $crawler->filter('.sub-menu > li')->each(function (Crawler $node, $i) {
        $categoryUrl = $node->filter('a')->attr('href');
        if (strpos($categoryUrl, '#') !== false) {

        } else {
            return $categoryUrl;
        }
    });
}


$today = new \DateTime();

$loginArray = [
    'email' => 'petarivanov2012@gmail.com',
    'password' => '123456789',
];
$domain = 'https://skyphone.bg';
$loginUrl = $domain . '/login';
$client = new \GuzzleHttp\Client(['cookies' => true]);
$response = $client->request('POST', $loginUrl, [
    'form_params' => $loginArray
]);

$productUrls = [];

// go get category data from url
if ($response->getStatusCode() == 200) {
    echo "<h2>Logged in!!!</h2>";

    foreach (getMenuCategoriesUrl('' . $response->getBody()) as $key => $url) {
        if ($url) {
            //Go through category paginator and stop if there are no more than 99 results on current page
            for ($i = 1; $i < 10; $i++) {
                //get category content
                $response = $client->request('GET', $domain . $url . $i);
                $crawler = new Crawler('' . $response->getBody());

                $urls = $crawler->filter('.row-stripedd')->each(function (Crawler $node, $i) {
                    $urlArray = explode('/', $node->filter('a')->attr('href'));
                    return '/' . $urlArray[1] . '/' . $urlArray[2] . '/' . $urlArray[3] . '/';
                });

                $productUrls = array_merge($productUrls, array_flip($urls));

                //if there are no more pages
                if ($crawler->filter('.row-stripedd')->count() < 99) {
                    break;
                }
            }
        }

//        if ($key == 1) {
//            break;
//        }
    }

    print_R($productUrls);
    exit;


    $file = fopen('scraped'.$today->format('Ymd').'.csv', 'w');

    // go get product data from url
    foreach ($productUrls as $url => $someValue) {
        $urlArray = explode('/',$url);
        $response = $client->request('GET', $domain . $url);
        $crawler = new Crawler('' . $response->getBody());

        $id = $urlArray[2];
        $name = trim($crawler->filter('h3')->text());
        $price = trim($crawler->filter('.dprice')->count() == 1 ? $crawler->filter('.dprice')->text() : $crawler->filter('.price-box')->text());
        $description = trim($crawler->filter('.product-description')->text());
        $image = $crawler->filter('.product-full-image')->attr('src');

        if($crawler->filter('.sold')->count() == 1){
            $inStock = $crawler->filter('.sold')->text();
        }elseif($crawler->filter('.sold-out')->count() == 1){
            $inStock = $crawler->filter('.sold-out')->text();
        }else{
            $inStock = "";
        }

        $categories = $crawler->filter('.breadcrumb > li')->each(function (Crawler $node, $i) {
            return trim($node->filter('a')->text());
        });

        fputcsv($file,[$id,$name,$price,$description,$image,$inStock,implode(' | ',$categories)]);
    }

    fclose($file);

    echo '<h2>CSV created!!!</h2>';
    $end = new \DateTime();
    echo "Execution time: ";
    echo $end->diff($today)->format("%i minutes, %s seconds");

} else {
    echo 'Status Code: ' . $response->getStatusCode();
}


