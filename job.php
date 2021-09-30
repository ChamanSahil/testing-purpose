<?php
require _DIR_ . '/utilities/common.php';

// function to get the response of URL
function curlCalls($address)
{
    $ch = curl_init();

    curl_seto\pt($ch, CURLOPT_URL, $address);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    //Execute the request.
    $data = curl_exec($ch);

    // the if statement will only run if the URL wll be: https://c.xkcd.com/random/comic/
    if (strpos($address, "random") == true) {
        // converting the response as the HTML document (datatype -> string)
        $content =  htmlentities($data);

        // find the specific string in the main string as it contains the re-drected comic no.
        $inspect = strpos($content, "https://xkcd.com");
        // this will take a sub-string of 30 cahracters after the ocuurence of "https://xkcd.com"
        $string =  substr($content, $inspect, 30);

        // In some cases, apart from the comic no there are some other html entities in the string,
        // so to eliminate those I have sanitized the numbers
        $comic_no = (int) filter_var($string, FILTER_SANITIZE_NUMBER_INT);

        // forming the final url which will provide all the json ddetails about the comic
        $target_url = "https://xkcd.com/" . $comic_no . "/info.0.json";

        // as now I have to again use curl to fetch the details of the target_url, I called tit withing this iteration
        curlCalls($target_url);
    }
    // this else statement will be executed for the target_url only ie (for eg:) https://xkcd.com/547/info.0.json
    else {
        $decoded = json_decode($data, true);

        // Parameters extracted from the json file
        $image_Url =  $decoded['img'];
        $title = $decoded['title'];

        // Month and year for the comic: sums up the date for the comic
        $month = $decoded['month'];
        $year = ' ' . $decoded['year'];

        $comic_no = $decoded['num'];

        // Converting the month number to month's name
        $dateObj   = DateTime::createFromFormat('!m', $month);
        $month = $dateObj->format('F');
    }

    curl_close($ch);
}

curlCalls("https://c.xkcd.com/random/comic/");
?>