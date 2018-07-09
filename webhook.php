<?php

    require 'line_access.php';
    require 'vendor/autoload.php';    

    $access_token = ACCESS_TOKEN;
    $channelSecret = CHANNEL_SECRET;
    $pushID = USER_ID;

    $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($access_token);
    $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $channelSecret]);

    #  --------------------- Declare CLASS -------------------------------------

    use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
    use \LINE\LINEBot\RichMenuBuilder\RichMenuAreaBoundsBuilder;
    use \LINE\LINEBot\RichMenuBuilder\RichMenuAreaBuilder;
    use \LINE\LINEBot\RichMenuBuilder\RichMenuSizeBuilder;
    use \LINE\LINEBot\TemplateActionBuilder;
    use \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
    use \LINE\LINEBot\Event\PostbackEvent;
    use \LINE\LINEBot;
    use \LINE\LINEBot\RichMenuBuilder;
   

    # ----------------------- Get Text from user ----------------------------------

    $content = file_get_contents('php://input');
    $events = json_decode($content,true);

    # ----------------- IF word = 'rich menu' it will show rich menu --------------

    if(!is_null($events['events'])){

        # ------- Code to check the data input -------------------------

        // $replyToken = $events['events'][0]['replyToken'];

        // $msg = new TextMessageBuilder(json_encode($events));

        // $response = $bot->replyMessage($replyToken, $msg);

        # -------------------------------------------------------------

        foreach($events['events'] as $event){
            $replyToken = $event['replyToken'];
            $msgType = $event['message']['type'];
            $userId = $event['source']['userId'];

            if($msgType == 'text'){
                $msg = trim(strtolower($event['message']['text']));
                if($msg == 'richmenu'){
                    # ----------------------- DECLEAR Rich Menu Property --------------------------
                    $sizeBuilder = new RichMenuSizeBuilder(1686,2500);
                    $boundBuilder = new RichMenuAreaBoundsBuilder(0,0,2500,1686);
                    $actionBuilder = new UriTemplateActionBuilder('Google','htttp://www.google.com');
                    $areaBuilder = new RichMenuAreaBuilder($boundBuilder,$actionBuilder);

                    # -------------------- Create Rich Menu ----------------------------------

                    $builder = new RichMenuBuilder($sizeBuilder,true,'Controller','Tab to open',$areaBuilder);

                    $response = $bot->createRichmenu($builder);

                    #------------------------- Get rich menu id -----------------------------

                    $richMenuIdArray = $response->getJSONDecodedBody();
                    $richMenuId = $richMenuIdArray['richMenuId'];

                    # ----------------------- DELETE existing Rich menu ---------------------------

                    // $bot->deleteRichMenu($richMenuId);

                    # ----------------------- Link User id with Richmenu ID -----------------

                    $link = $bot->linkRichMenu($userId,$richMenuId);

                    # ----------------------- Upload img to line Rich menu ------------------
                    $uploadImg = $bot->uploadRichMenuImage($richMenuId,'E:\xampp\htdocs\line-rich\controller.jpg','image/jpg');

                    if($response->isSucceeded()){
                    $msg = new TextMessageBuilder('Rich menu create');
                    $bot->pushMessage($userId,$msg);
                    }else{
                    echo 'Error';
                    echo $response->getHTTPStatus() . ' ';
                    echo '<pre>';
                    echo $response->getRawBody();
                    echo '</pre>';
                    }
                    # ------------------------------------------------------------------------
                }
            }
        }
    }
