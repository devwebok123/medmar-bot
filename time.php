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
                strtolower($tratte["descrizione"]) == strtolower("REG | ".$param) ||
                strtolower($tratte["descrizione"]) == strtolower($param)
            ) {
                $arr = array(
                    "id_tratta" => $tratte["id_tratta"],
                    "descrizione" => $tratte["descrizione"]
                );
                array_push($tratteArr, $arr);
                //break;
            }
        }

        $dataArr = [];

        for ($i=0; $i < count($tratteArr); $i++) { 
            $cources = (count($tratteArr) > 0) ? getCourse($token,  $tratteArr[$i]["id_tratta"]) : [];        
       
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
                        "route" => $tratteArr[$i]["descrizione"],
                        "ship" => $cource["nave"],
                        "date" => $cource["data"]
                    );                      
                    array_push($dataArr, $arr);                     
                }                       
            }        
        }        
        
        $dataArr1 = array_map('data2Object', $dataArr);
        usort($dataArr1, 'comparator');       

        $text = "";     
        $dateITALY = "";
        if (count($dataArr1) > 0) {          
            
            //for ($i=0; $i < 20; $i++) { 
            $c = 0;
            foreach ($dataArr1 as $data) {

                if ($c > 10) break;

                $c++;
                
                $route = $data->route;
                $time = $data->time;
                $date = $data->date;
                
                list($y, $m, $d)=explode("-", $date);
                $dateIT = $d."-".$m."-".$y;

                if($dateITALY != $dateIT){
                    $dateITALY = $dateIT;
                    $text .="Giorno: ".$dateIT."\n";
                }

                $text .= $route.": ".substr($time,0,5)."\n";
            }      
                
        } else {
            $text = "Nessun dato";
        }

        return $text;
    }

    class geekData {      
   
        // Constructor for class initialization
        function __construct($data) {
            $this->time = $data['time'];
            $this->route = $data['route'];
            $this->ship = $data['ship'];
            $this->date = $data['date'];
        }
    }
    function data2Object($data) {
        $class_object = new geekData($data);
        return $class_object;
    }

    function comparator($object1, $object2) {
        return (($object1->date > $object2->date) || 
            ($object1->time > $object2->time && 
            $object1->date == $object2->date)
        );
    }

    // echo var_dump(getNextTime("ischia - pozzuoli"));


    
?>