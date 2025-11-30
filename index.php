<?php
if(isset($_GET['url'])) {
    $link = $_GET['url'];
    $link = removeAfter($link, '?');
    $link = str_replace('?', '', $link);
    $realFileName = getFileNameFromURL($link);
    $realFileName = str_replace('.'.getExtension($realFileName), '', $realFileName);
    if(isFind($link, 'shutterstock.com')) {
        $id = getNumbersEx($realFileName);
        if(isFind($link, '-')) {
            if(isFind($link, '/video/') && isFind($link, 'clip-')) {
                $id = removeBefore($link, 'clip-');
                $id = removeAfter($id, '-');
                $id = str_replace('-', '', $id);
                die();
            }
            elseif(isFind($link, '/music/') && isFind($link, 'track-')) {
                $id = removeBefore($link, 'track-');
                $id = removeAfter($id, '-');
                $id = str_replace('-', '', $id);
                die();
            }
            else {
                $id = explode('-', $link);
                $id = $id[(sizeof($id) - 1)];
            }
        }
        if(!empty($id)) {
            $cookie = "cookies.txt";
            if(!file_exists($cookie)) {
                die();
            }
            $country = "US";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.shutterstock.com/sstk-oauth/access-token");
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
            curl_setopt($ch, CURLOPT_HEADER, 0); 
            $headers = array('Referer: https://www.shutterstock.com/home', 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36', 'accept: application/json', 'content-type: application/json');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);                                                                                            
            $result = curl_exec($ch);
            echo $result;
            curl_close($ch);
            $json = json_decode($result);
            if($json->access_token) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://www.shutterstock.com/studioapi/user/subscriptions");
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
                curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, 0); 
                $headers = array('Referer: https://www.shutterstock.com/home', 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36', 'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7');
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $rs = curl_exec($ch);
                curl_close($ch);
                $jso = json_decode($rs);
                $credits_remaining = 0;
                foreach($jso->data as $key) {
                    if($key->attributes->status == "active" && $key->attributes->current_licenses[0]->redeemable_for == "standard") {
                        $subcre = $key->attributes->current_allotments[0]->credits_remaining;
                        $credits_remaining = $credits_remaining + $subcre;
                        $subscription_id = $key->id;
                    }
                    else {
                        $credits_remaining = 0;
                    }
                }
                foreach($jso->data as $key) {
                    if($key->attributes->status == "active" && $key->attributes->current_licenses[0]->redeemable_for == "standard" && $key->attributes->current_allotments[0]->credits_remaining >= 1) {
                        $subscription_id = $key->id;
                    }
                }
                if($subscription_id) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://www.shutterstock.com/studioapi/images/".$id);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
                    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    $headers = array('Referer: https://www.shutterstock.com/home', 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36', 'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7');
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);                                                                                            
                    $rs = curl_exec($ch);
                    curl_close($ch);
                    $jsons = json_decode($rs);
                    if(array_key_exists("vector_eps", $jsons->data->attributes->sizes)) {
                        $content_format = "eps";
                        $content_size = "vector";
                    }
                    else {
                        $content_format = "jpg";
                        $content_size = "huge";
                    }
                    $postdata = '{"country_code":"'.$country.'","required_cookies":"","content":[{"content_format":"'.$content_format.'","content_id":"'.$id.'","content_size":"'.$content_size.'","content_type":"photo","license_name":"standard","show_modal":true}]}';
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://www.shutterstock.com/studioapi/licensees/current/redownload");
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
                    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
                    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
                    curl_setopt($ch, CURLOPT_HEADER, 0); 
                    $headers = array('Referer: '.$referurl, 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36', 'accept: application/json', 'content-type: application/json', 'authorization: Bearer '.$json->access_token, 'x-end-user-country: '.$country, 'x-shutterstock-user-token: '.$json->user_token);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);                                                                                            
                    $rss = curl_exec($ch);
                    curl_close($ch);
                    $jsonss = json_decode($rss);
                    if(!empty($jsonss->meta->licensed_content[0]->download_url) && $jsonss->meta->licensed_content[0]->is_redownload) {
                        $download_url = $jsonss->meta->licensed_content[0]->download_url;
                    }
                    else {
                        $postdata = '{"country_code":"'.$country.'","required_cookies":"","content":[{"content_format":"'.$content_format.'","content_id":"'.$id.'","content_size":"'.$content_size.'","content_type":"photo","license_name":"standard","subscription_id":"'.$subscription_id.'","show_modal":true}]}';
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, "https://www.shutterstock.com/studioapi/licensees/current/relicense");
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
                        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
                        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
                        curl_setopt($ch, CURLOPT_HEADER, 0); 
                        $headers = array('Referer: '.$referurl, 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36', 'accept: application/json', 'content-type: application/json', 'authorization: Bearer '.$json->access_token, 'x-end-user-country: '.$country, 'x-shutterstock-user-token: '.$json->user_token);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        $rss = curl_exec($ch);
                        curl_close($ch);
                        $jsonss = json_decode($rss);
                        $download_url = $jsonss->meta->licensed_content[0]->download_url;
                    }
                    if(!empty($download_url)) {
                        $filename = basename($download_url);
                        $download_url = removeAfter($download_url, '?');
                        $download_url = str_replace('?', '', $download_url);
                        $size = getFileSize($download_url);
                        header('Content-Type: application/json');
                        echo json_encode(array('status' => true, 'name' => $filename, 'size' => $size, 'download' => $download_url, 'credits_remaining' => $credits_remaining));
                    }
                }
            }
        }
    }
}

function getFileSize($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    $data = curl_exec($ch);
    $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    curl_close($ch);
    return (int) $size;
}
function getNumbersEx($string) {
	$return = "";
	for($i = 0; $i < strlen($string); $i++) {
    	if(is_numeric($string[$i])) {
        	$return .= $string[$i];
        }
    }
    return $return;
}
function isFind($string, $find) {
    $pos = stripos($string, $find);
    if($pos === false) {
        return false;
    }
    return true;
}
function getFileNameFromURL($url) {
    $path = parse_url($url, PHP_URL_PATH);
    return basename($path);
}
function removeAfter($string, $remove) {
    if(isFind($string, $remove)) {
        return substr($string, 0, (strpos($string, $remove) + strlen($remove)));
    }
    return $string;
}
function removeBefore($string, $before) {
    $pos = strpos($string, $before);
    return $pos !== false ? substr($string, $pos + strlen($before), strlen($string)) : $in;
}
function getExtension($string) {
    $extension = 'null';
    $ex = explode('.', $string);
    $extension = $ex[(sizeof($ex) - 1)];
    return $extension;
}
