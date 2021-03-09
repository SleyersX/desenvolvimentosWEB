<?php 
$url='https://eselkap.lares.dsd:9200/es-esb-log4j2-*/_search?fields=cmap.STORE_CODE {
  "query": {
    "filtered": {
      "query": {
        "query_string": {
          "query": "cmap.APPLICATION_NAME:START_DAY AND level:ERROR AND cmap.STORE_CODE:<98000",
          "analyze_wildcard": true
        }
      },
      "filter": {
        "bool": {
          "must": [
            {
              "range": {
                "@timestamp": {
                  "gte": "now/d",
                  "lte": "now/d+8h"
                }
              }
            }
          ],
          "must_not": []
        }
      }
    }
  },
  "size": 0,
  "aggs": {
    "by_store": {
      "terms": {
        "field": "cmap.STORE_CODE.raw",
        "size": 9999999
      }
    }
  }
}';

http_response($url,'200',3); // returns true if the response takes less than 3 seconds and the response code is 200 
?> 

<?php 
function http_response($url, $status = null, $wait = 3) 
{ 
        $time = microtime(true); 
        $expire = $time + $wait; 

        // we fork the process so we don't have to wait for a timeout 
        $pid = pcntl_fork(); 
        if ($pid == -1) { 
            die('could not fork'); 
        } else if ($pid) { 
            // we are the parent 
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_HEADER, TRUE); 
            curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
            $head = curl_exec($ch); 
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
            curl_close($ch); 
            
            if(!$head) 
            { 
                return FALSE; 
            } 
            
            if($status === null) 
            { 
                if($httpCode < 400) 
                { 
                    return TRUE; 
                } 
                else 
                { 
                    return FALSE; 
                } 
            } 
            elseif($status == $httpCode) 
            { 
                return TRUE; 
            } 
            
            return FALSE; 
            pcntl_wait($status); //Protect against Zombie children 
        } else { 
            // we are the child 
            while(microtime(true) < $expire) 
            { 
            sleep(0.5); 
            } 
            return FALSE; 
        } 
    } 
?>