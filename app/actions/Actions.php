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
        $domain = $this->getDomain($url);
        $linksArray = $this->getUrlLinksOfDomain($url); var_dump($linksArray); exit;
        $imagesArray = $this->getImageLinks($linksArray);
        $this->readWriteReport($domain, true, $imagesArray);
    }

    // Report result of domain analyse function
    public function getReport() {
        echo "Please enter domain for analyse: \n";
        $url = readline();
        $domain = $this->getDomain($url);
        $report = $this->readWriteReport($domain, false);
        print_r($report);
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

    //Search all links of domain in the pages by URL function
    public function getUrlLinksOfDomain($url, $urls = null) {
        $from = (substr($url, 0, 4) == 'http') ? $url : 'http://' . $url;
        $domain = $this->getDomain($from);
        $allLinkIsChecked = true;
        $urls[$from] = true;

        if (@fopen($from, "r")) {
            $html = file_get_contents($from);
            preg_match_all("/<[Aa][\s]{1}[^>]*[Hh][Rr][Ee][Ff][^=]*=[ '\"\s]*([^ \"'>\s#]+)[^>]*>/", $html, $matches);

            foreach ($matches[1] as $match) {
                $matchVal = ((substr($url, 0, 4) == 'http') && (stristr($match, $domain))) ? $match : 'http://' . $domain . $match;

                if ($matchVal !== $from && empty($urls[$matchVal])) {
                    $urls[$matchVal] = false;
                }
            }

            foreach ($urls as $key => $value) {
                if ($value === false) {
                    $allLinkIsChecked = false;
                }
            }

            if ($allLinkIsChecked === false) {
                $urlsArray = [];

                foreach ($urls as $key => $value) {
                    if ($value === false) {
                        $urlsArray = $urlsArray + ($this->getUrlLinksOfDomain($key, $urls));
                    }
                }

                $urls = $urls + $urlsArray;

                return $urls;
            }
        }

        return $urls;
    }

    //Search all images in the pages by URL function
    public function getImageLinks($urlsArray)
    {
        $imageLinks = [];

        foreach ($urlsArray as $urlKey => $urlValue) {
            $html = file_get_contents($urlKey);
            preg_match_all("/<[Ii][Mm][Gg][\s]{1}[^>]*[Ss][Rr][Cc][^=]*=[ '\"\s]*([^ \"'>\s#]+)[^>]*>/", $html, $matches);

            foreach ($matches[1] as $key => $value){
                if (substr($value, 0, 4) == 'http') {
                    if(@fopen($value, "r")) {
                        $imageLinks[$key] = 'link: ' . $value . ' source page: ' . $key;
                    }
                } else {
                    if(@fopen($urlKey . $value, "r")) {
                        $imageLinks[$key] = 'link: ' . $urlKey . $value . ' source page: ' . $key;
                    }
                }
            }
        }

        return array_unique($imageLinks);
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
                    fputcsv($report, $item);
                }

                fclose($report);
            } else {

            } return 'Not images by this URL';
        } else {

            if (($report = fopen(__DIR__ . '/../reports/' . $domain . '.csv', 'r')) !== false) {
                $reportArray = [];

                while (($data = fgetcsv($report, 1000, ",")) !== false) {
                    $num = count($data);

                    for ($i=0; $i < $num; $i++) {
                        $reportArray[] =  $data[$i];
                    }
                }

                fclose($report);

                return $reportArray;

            } else {

                return 'Error of open file';
            }
        }
    }
}