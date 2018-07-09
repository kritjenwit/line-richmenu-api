<?php

    require 'line_access.php';
    require 'vendor/autoload.php';
    // require 'vendor/linecorp/line-bot-sdk/src/LINEBot.php';
    

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
    
    #  ---------------------- Create Rich Menu With API ---------------------------------

    # ---------------------- DECLARE RICH MENU PROPERTY ------------------------

    $boundBuilder = new RichMenuAreaBoundsBuilder(551,325,321,321); // (x,y,width,height);

    $actionBuilder = array(
        new UriTemplateActionBuilder('Krit','Krit@krit.com'), // ($label,$url);
    );

    
    $areaBuilder = new RichMenuAreaBuilder($boundBuilder, $actionBuilder);
    $sizeBuilder  = RichMenuSizeBuilder::getFull();

    # -------------------- Create Rich Menu ----------------------------------

    $builder = new RichMenuBuilder($sizeBuilder,true,'Controller','Tab to open',$areaBuilder);
    $response = $bot->createRichMenu($builder);

    if($response->isSucceeded()){
        echo 'Success';
        return;
    }else{
        echo 'Error';
        echo $response->getHTTPStatus() . ' ';
        echo '<pre>';
        echo $response->getRawBody();
        echo '</pre>';
    }