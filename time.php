<?php   
    function getToken() {
        $url = 'https://biglietteria.medmargroup.it/production/booking_api/public/index.php/api/v1/auth/login';
        $data = array('email' => 'bot@med.it', 'password' => 'telegram123');
        
        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) { /* Handle error */ }
        
        $result = json_decode($result, true);
        return $result["output"]["token"];
    }     

    function getTratte($token) {
        $url = 'https://biglietteria.medmargroup.it/production/booking_api/public/index.php/api/v1/tratte';       
        
        $bearer = "Bearer ".$token; 
        $options = array(
            'http' => array(
                'header'  => "Authorization: $bearer",
                'method'  => 'GET'               
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) { /* Handle error */ }
        
        $result = json_decode($result, true);
        return $result["output"];       
    }      

    function getCourse($token, $id) {
        $url = 'https://biglietteria.medmargroup.it/production/booking_api/public/index.php/api/v1/corse/'.$id.'?"partenza_data"="'.date('Y-m-d').'"'; 
       
        $bearer = "Bearer ".$token; 
        $options = array(
            'http' => array(
                'header'  => "Authorization: $bearer",
                'method'  => 'GET'                
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) { /* Handle error */ }
        $result = json_decode($result, true);        
        
        return $result["output"];
           
    }    

    function getNextTime($param) {
        $tz = 'Europe/Rome';
        $timestamp = time();
        $dt = new DateTime("now", new DateTimeZone($tz)); //first argument "must" be a string
        $dt->setTimestamp($timestamp); //adjust the object to correct timestamp
       

        $token = getToken();
        $trattes = getTratte($token);

        $tratteArr = [];

        foreach ($trattes as $tratte) {           
            if(
                strtolower($param) == strtolower($tratte["descrizione"])
            ) {
                $arr = array(
                    "id_tratta" => $tratte["id_tratta"],
                    "descrizione" => $tratte["descrizione"]
                );
                $tratteArr = $arr;
                break;
            }
        }

        $dataArr = [];

        $cources = (count($tratteArr) > 0) ? getCourse($token,  $tratteArr["id_tratta"]) : [];        
       
        foreach ($cources as $cource) {    
            $flag = false; 
            foreach($dataArr as $arr){
                if(
                    $arr["time"] == $cource["partenza_ora"] && 
                    $arr["date"] == $cource["data"]
                ){
                    $flag = true;
                    break; 
                }
            }
            if($flag == true) continue;
            if (
                ($cource["data"] == $dt->format('Y-m-d') && 
                $cource["partenza_ora"] > $dt->format("H:i:s")) ||
                $cource["data"] > $dt->format("Y-m-d")
            ) {
                $arr = array(
                    "time" => $cource["partenza_ora"],
                    "route" => $tratteArr["descrizione"],
                    "ship" => $cource["nave"],
                    "date" => $cource["data"]
                );                      
                array_push($dataArr, $arr);                     
            }                       
        }        

        $text = "";     
        
        if (count($dataArr) > 0) {          
            
            for ($i=0; $i < 10; $i++) { 
                
                $route = $dataArr[$i]["route"];
                $time = $dataArr[$i]["time"];
                $date = $dataArr[$i]["date"];
                
                $text .= $route.": ".substr($time,0,5)." ".$date."\n";
            }      
                
        } else {
            $text = "Nessun dato";
        }

        return $text;
    }

    //echo var_dump(getNextTime("ischia - pozzuoli"));


    
?>