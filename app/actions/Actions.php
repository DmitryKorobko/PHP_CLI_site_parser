<?php

namespace liw\app\actions;

/**
 * Created by PhpStorm.
 * User: steinmann
 * Date: 05.09.17
 * Time: 14:17
 */

/** class Actions */
class Actions {

    // Parsing function
    public function getParse(){
        echo "Please enter URL for parsing: \n";
        $url = readline();
        $this->getImagesLinksFromAllLinksOfDomainByUrl($url);
    }

    // Report result of domain analyse function
    public function getReport() {
        echo "Please enter domain for analyse: \n";
        $url = readline();
        $this->getImagesLinksFromAllLinksOfDomainByUrl($url, false);
    }

    // Help view function
    public function getHelp() {
        echo "==============================================\n";
        echo "Help. List of commands:\n";
        echo "==============================================\n";
        echo "1. parse (URL)     - parsing URL and get link to file with result\n";
        echo "2. report (domain) - get result of domain analyse\n";
        echo "3. help            - get help with list of commands\n";
    }

    //Search all images links from all links of domain in the pages by URL function
    public function getImagesLinksFromAllLinksOfDomainByUrl($url, $forWrite = true, $urls = null) {
        $from = (substr($url, 0, 4) == 'http') ? $url : 'http://' . $url;
        $domain = $this->getDomain($from);
        $allLinkIsChecked = true;
        $urls[$from] = true;

        if (@fopen($from, "r") && @file_get_contents($from)) {
            $html = file_get_contents($from);
            preg_match_all("/<[Aa][\s]{1}[^>]*[Hh][Rr][Ee][Ff][^=]*=[ '\"\s]*([^ \"'>\s#]+)[^>]*>/", $html, $matches);

            foreach ($matches[1] as $match) {
                $matchVal = ((substr($url, 0, 4) == 'http') && (stristr($match, $domain))) ? $match : 'http://' . $domain . $match;

                if ($matchVal !== $from && empty($urls[$matchVal])) {
                    $urls[$matchVal] = false;
                }
            }

            foreach ($urls as $key => $value) {

                if (!$value) {
                    $allLinkIsChecked = false;
                }
            }

            if (!$allLinkIsChecked) {
                $urlsArray = [];

                foreach ($urls as $key => $value) {

                    if (!$value) {
                        $urls[$key] = true;

                        if ($forWrite) {
                            $urlsArray += ($this->getImagesLinksFromAllLinksOfDomainByUrl($key, true, $urls));
                        } else {
                            $urlsArray += ($this->getImagesLinksFromAllLinksOfDomainByUrl($key,false, $urls));
                        }
                    }
                }

                $urls += $urlsArray;
                $allLinkIsChecked = true;

                foreach ($urls as $key => $value) {

                    if (!$value) {
                        $allLinkIsChecked = false;
                    }
                }

                if ($allLinkIsChecked) {

                    if ($forWrite) {
                        $this->readWriteReport($domain, true, $this->getImagesLinks($urls));
                    } else {
                        $this->readWriteReport($domain, false, $this->getImagesLinks($urls));
                    }

                    exit();
                }

                return $urls;
            } else {

                if ($forWrite) {
                    $this->readWriteReport($domain, true, $this->getImagesLinks($urls));
                } else {
                    $this->readWriteReport($domain, false, $this->getImagesLinks($urls));
                }

                exit();
            }
        }

        return $urls;
    }

    //Search all images in the pages by URL function
    public function getImagesLinks($urlsArray)
    {
        $imageLinksWithSourcePages = [];

        foreach ($urlsArray as $urlKey => $urlValue) {

            if (@fopen($urlKey, "r") && @file_get_contents($urlKey)) {
                $html = file_get_contents($urlKey);
                preg_match_all("/<[Ii][Mm][Gg][\s]{1}[^>]*[Ss][Rr][Cc][^=]*=[ '\"\s]*([^ \"'>\s#]+)[^>]*>/", $html, $matches);
                $imageLinks = [];

                foreach ($matches[1] as $key => $value) {

                    if (substr($value, 0, 4) == 'http') {

                        if (@fopen($value, "r")) {
                            $imageLinks[$key] = $value;
                        }
                    } else {

                        if (@fopen($urlKey . $value, "r")) {
                            $imageLinks[$key] = $urlKey . $value;
                        }
                    }
                }

                if (!empty($imageLinks)) {
                    $imageLinksWithSourcePages['Source page: ' . $urlKey] = $imageLinks;
                }
            }
        }

        return (!empty($imageLinksWithSourcePages)) ? $imageLinksWithSourcePages : 'Do not have any images by this URL';
    }

    //Get domain by URL function
    public function getDomain($url) {
        $from = (substr($url, 0, 4) == 'http') ? $url : 'http://' . $url;
        $domain = parse_url($from)['host'];

        return $domain;
    }

    //Save report as .CSV file function
    public function readWriteReport($domain, $forWrite = true, $dataArray = null) {

        if ($forWrite) {

            if ($dataArray !== null) {
                $report = fopen(__DIR__ . '/../reports/' . $domain . '.csv', 'w');

                foreach ($dataArray as $item) {
                    fputcsv($report, $item , ';', '"');
                }

                fclose($report);

                echo 'Link to results of analyse: ' . "\n" . realpath(__DIR__ . "/../reports/" . $domain . '.csv') . "\n";
            } else {
                echo 'Not images by this URL';
            }
        } else {
              // If we want read results of domain analyse from already existing file
//            if (@fopen(__DIR__ . '/../reports/' . $domain . '.csv', 'r')) {
//                if (($report = fopen(__DIR__ . '/../reports/' . $domain . '.csv', 'r')) !== false) {
//                    $reportArray = [];
//
//                    while (($data = fgetcsv($report, 1000, ";")) !== false) {
//                        $num = count($data);
//
//                        for ($i = 0; $i < $num; $i++) {
//                            $reportArray[] = $data[$i];
//                        }
//                    }
//
//                    fclose($report);
//
//                    return $reportArray;
//
//                }
//            } else {
//
//                echo 'Error of open file';
//            }

            //If we want see results of domain analyse without creating file
            echo 'Results of analyse for domain: ' . $domain . "\n";
            foreach ($dataArray as $dataItem => $dataValue) {
                echo "\n" . $dataItem . ": \n Links: \n";
                foreach ($dataArray[$dataItem] as $value) {
                    echo  $value . "\n";
                }
            }
        }
    }
}