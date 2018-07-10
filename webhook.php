<?php 

    require_once 'line_access.php';
    require_once 'vendor/autoload.php';

    $access_token = ACCESS_TOKEN;
    $channelSecret = CHANNEL_SECRET;
    // $user_id = USER_ID;

    $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($access_token);
    $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $channelSecret]);

    # ------------------------------------- USE LINECLASS ----------------------------------------

    use \LINE\LINEBot\RichMenuBuilder;
    use \LINE\LINEBot\RichMenuBuilder\RichMenuSizeBuilder;
    use \LINE\LINEBot\RichMenuBuilder\RichMenuAreaBoundsBuilder;
    use \LINE\LINEBot\RichMenuBuilder\RichMenuAreaBuilder;

    use \LINE\LINEBot\TemplateActionBuilder;
    use \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

    use \LINE\LINEBot\MessageBuilder;
    use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
  


    # ------------------- Get data From user ----------------------------------------------------

    // Get POST body content
    $content = file_get_contents('php://input');    
    // Parse JSON
    $events = json_decode($content, true);

    echo 'Webhook Activated';

    # -------------------------------- Decleare Richmenu Property --------------------------------

    if(!is_null($events['events'])){       
        foreach ($events['events'] as $event) {

            $replyToken = $event['replyToken'];
            $user_id = $event['source']['userId'];
            $msgType = $event['message']['type'];

            if($msgType == 'text'){
                $textMsg = trim(strtolower($event['message']['text']));
                if($textMsg == 'richmenu'){
                    # -------------------------------- Decleare Richmenu Property --------------------------------

                    $sizeBuilder = RichMenuSizeBuilder::getFull();
                    $boundBuilder = new RichMenuAreaBoundsBuilder(0,0,2500,1686);
                    $actionBuilder =  new UriTemplateActionBuilder('Test','http://www.google.com');

                    $areaBuilder = array(
                        new RichMenuAreaBuilder($boundBuilder,$actionBuilder)
                    );

                    $builder = new RichMenuBuilder($sizeBuilder,true,'Controller','Tab to open',$areaBuilder);

                    # -------------------- Create Rich Menu ---------------------------------

                    $response = $bot->createRichMenu($builder);


                    // # ------------ Get Richmenu Id -----------------------

                    $richMenuIdArr = $response->getJSONDecodedBody();
                    $richMenuId = $richMenuIdArr['richMenuId'];

                    # ------------ Link user id with richmenu id --------

                    $link = $bot->linkRichMenu($user_id,$richMenuId);
        
                    # -------------- Insert image to Richmenu -----------
                    
                    $upload = $bot->uploadRichMenuImage($richMenuId,'E:\xampp\htdocs\line-rich\controller.jpg','image/jpg');
                    
                    if($response->isSucceeded()){
                        if($link && $upload){
                            $msg = new TextMessageBuilder('Response, link and upload are success');
                            $bot->replyMessage($replyToken,$msg);
                        }
                        else{
                            $msg = new TextMessageBuilder('Response success but not link and upload');
                            $bot->replyMessage($replyToken,$msg);
                            return;
                        }
                    }else{
                        $msg = new TextMessageBuilder('Response failed');
                        $bot->replyMessage($replyToken,$msg);
                        return;
                    }
                }elseif($textMsg != 'richmenu'){
                    $msg = new TextMessageBuilder('Type Richmenu to activate richmenu');
                    $bot->replyMessage($replyToken,$msg);
                }
            }
        }
    }
    
