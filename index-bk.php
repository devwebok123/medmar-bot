<?php 
    include_once("./time.php");

    $BOT_TOKEN = "1447056473:AAH0Sf3qwKVgGz9T9Aau0vY3-S_W9sAPnUI";    

    $update = file_get_contents("php://input");
    $update = json_decode($update, true);
    $userChatId = $update["message"]["from"]["id"] ? $update["message"]["from"]["id"] : null;

    if($userChatId) {
        $userMessage = $update["message"]["text"]?$update["message"]["text"]:"Nothing";
        $firstName = $update["message"]["from"]["first_name"]?$update["message"]["from"]["first_name"]:"N/A";
        $lastName = $update["message"]["from"]["last_name"]?$update["message"]["from"]["last_name"]:"N/A";

        $text = "";
        if(strtolower($userMessage) == "contacts") {           
            $text = "Centro Prenotazioni, tel: 08133344111\n";
            $text .= "Aperto tutti i giorni: \nDal Lunedi al Sabato 09:00 - 19:00\n";
            $text .= "Domenica e festivi 09:00 - 15:40\n\n";
            $text .= "Mail: centroprenotazioni@medmarnavi.it\n";
            $text .= "Info e Prenotazioni su medmarnavi.it";
            $parameters = array(
                "chat_id" => $userChatId,
                "text" => $text,
                "parseMode" => "html",
                "reply_markup" => '{"keyboard": [["Contacts"],["Ischia - Pozzuoli"],["Ischia - Napoli"],["Ischia - Procida"],["Casamicciola - Pozzuoli"],["Casamicciola - Napoli"],["Pozzuoli - Ischia"],["Pozzuoli - Casamicciola"],["Pozzuoli - Procida"],["Napoli - Ischia"],["Napoli- Casamicciola"],["Pozzuoli - Ischia via Procida"],["Procida - Pozzuoli"],["Procida - Napoli"],["Ischia PDF"],["Procida PDF"]], "one_time_keyboard": false}'
            );               
           
            send("sendMessage", $parameters);

        } else if (
            strtolower(trim($userMessage)) == "ischia - pozzuoli" ||
            strtolower(trim($userMessage)) == "casamicciola - pozzuoli" ||
            strtolower(trim($userMessage)) == "pozzuoli - procida" ||
            strtolower(trim($userMessage)) == "pozzuoli - casamicciola" ||
            strtolower(trim($userMessage)) == "napoli - ischia" ||
            strtolower(trim($userMessage)) == "procida - pozzuoli" ||
            strtolower(trim($userMessage)) == "ischia - napoli" ||
            strtolower(trim($userMessage)) == "ischia - procida" ||
            strtolower(trim($userMessage)) == "procida - napoli" ||
            strtolower(trim($userMessage)) == "pozzuoli - ischia" ||
            strtolower(trim($userMessage)) == "casamicciola - napoli" ||
            strtolower(trim($userMessage)) == "pozzuoli - ischia via procida" ||
            strtolower(trim($userMessage)) == "napoli- casamicciola"
        ) {            
            $text = getNextTime(trim($userMessage));
            $parameters = array(
                "chat_id" => $userChatId,
                "text" => $text,
                "parseMode" => "html",
                "reply_markup" => '{"keyboard": [["Contacts"],["Ischia - Pozzuoli"],["Ischia - Napoli"],["Ischia - Procida"],["Casamicciola - Pozzuoli"],["Casamicciola - Napoli"],["Pozzuoli - Ischia"],["Pozzuoli - Casamicciola"],["Pozzuoli - Procida"],["Napoli - Ischia"],["Napoli- Casamicciola"],["Pozzuoli - Ischia via Procida"],["Procida - Pozzuoli"],["Procida - Napoli"],["Ischia PDF"],["Procida PDF"]], "one_time_keyboard": false}'
            );               
           
            send("sendMessage", $parameters);

        } else if (strtolower(trim($userMessage)) == "ischia pdf") {
            $filepath = "https://bot.medmarnavi.it/pdf/ischia.pdf";
            $parameters = array(
                "chat_id" => $userChatId,
                "document" => $filepath,
                "reply_markup" => '{"keyboard": [["Contacts"],["Ischia - Pozzuoli"],["Ischia - Napoli"],["Ischia - Procida"],["Casamicciola - Pozzuoli"],["Casamicciola - Napoli"],["Pozzuoli - Ischia"],["Pozzuoli - Casamicciola"],["Pozzuoli - Procida"],["Napoli - Ischia"],["Napoli- Casamicciola"],["Pozzuoli - Ischia via Procida"],["Procida - Pozzuoli"],["Procida - Napoli"],["Ischia PDF"],["Procida PDF"]], "one_time_keyboard": false}'
            );               
           
            send("sendDocument", $parameters);
        } else if (strtolower(trim($userMessage)) == "procida pdf") {
            $filepath = "https://bot.medmarnavi.it/pdf/procida.pdf";
            $parameters = array(
                "chat_id" => $userChatId,
                "document" => $filepath,
                "reply_markup" => '{"keyboard": [["Contacts"],["Ischia - Pozzuoli"],["Ischia - Napoli"],["Ischia - Procida"],["Casamicciola - Pozzuoli"],["Casamicciola - Napoli"],["Pozzuoli - Ischia"],["Pozzuoli - Casamicciola"],["Pozzuoli - Procida"],["Napoli - Ischia"],["Napoli- Casamicciola"],["Pozzuoli - Ischia via Procida"],["Procida - Pozzuoli"],["Procida - Napoli"],["Ischia PDF"],["Procida PDF"]], "one_time_keyboard": false}'
            );               
           
            send("sendDocument", $parameters);
        } else {
            $text .= "Ciao, sono Medy, il bot di Medmar Navi\nScegli una tratta per conoscere le prossime partenze";
            $parameters = array(
                "chat_id" => $userChatId,
                "text" => $text,
                "parseMode" => "html",
                "reply_markup" => '{"keyboard": [["Contacts"],["Ischia - Pozzuoli"],["Ischia - Napoli"],["Ischia - Procida"],["Casamicciola - Pozzuoli"],["Casamicciola - Napoli"],["Pozzuoli - Ischia"],["Pozzuoli - Casamicciola"],["Pozzuoli - Procida"],["Napoli - Ischia"],["Napoli- Casamicciola"],["Pozzuoli - Ischia via Procida"],["Procida - Pozzuoli"],["Procida - Napoli"],["Ischia PDF"],["Procida PDF"]], "one_time_keyboard": false}'
            );               
           
            send("sendMessage", $parameters);
        };
        
        
    }

    function send($method, $data) {
        global $BOT_TOKEN;
        $url = "https://api.telegram.org/bot$BOT_TOKEN/".$method;

        if(!$curid = curl_init()) {
            exit;
        }
        curl_setopt($curid, CURLOPT_POST, true);
        curl_setopt($curid, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curid, CURLOPT_URL, $url);
        curl_setopt($curid, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($curid);
        curl_close($curid);
        return $output;
    }
    
?>
