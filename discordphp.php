<?php
stream_context_set_default(['http'=>['proxy'=>'192.168.49.1:8282']]);
stream_context_set_default(['https'=>['proxy'=>'192.168.49.1:8282']]);
include __DIR__.'/vendor/autoload.php';
use Discord\DiscordCommandClient;

$token='NzU4ODI2MjAxOTQyMDY1MTU0.X20l-A.AFhwIIJD5nHvmQ1Od-YtQhJxdMA';
#$token=getenv("DISCORD_TOKEN");

$hid="666317117154525185";
$discord = new DiscordCommandClient([
    'token' => $token,
    'prefix' => 'php.',
    'defaultHelpCommand' => false,
    'discordOptions' => [
        'loggerLevel' => 'ERROR',
    ],
]);
$phpver=explode('.',phpversion())[0].".".explode('.',phpversion())[1].".".explode('-',explode('.',phpversion())[2])[0];
$discver=phpversion('team-reflex/discord-php');
function startswith($haystack, $needle) {
	return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}
$ping=function($m){
    $m->channel->sendMessage('Pong!');
};
$pong=function($m){
    $m->channel->sendMessage('Ping!');
};
$tst=function($m){
    $m->channel->sendMessage('I\'m up!');
};
$stop=function($m){
    global $discord,$hid;
    if ($m->author->id==$hid){
        $m->channel->sendMessage('Goodbye!')->then(function($nm) use ($discord){
            $discord->close();
        });
    }
};
$lat=function($m){
    $m->channel->sendMessage("```\nno.\n```");
};
$ver=function($m) use ($phpver,$discver){
    $m->channel->sendMessage("```\nPHP Version: $phpver\nDiscordPHP Version:  $discver\n```");
};
$discord->registerCommand('ping',$ping);
$discord->registerCommand('pong',$pong);
$discord->registerCommand('tst',$tst);
$discord->registerCommand('stop',$stop);
$discord->registerCommand('lat',$lat);
$discord->registerCommand('ver',$ver);
$discord->on('ready', function ($discord) {
    echo "We have logged in as ",$discord->username,"#",$discord->discriminator,"<@!",$discord->id,">",PHP_EOL;
    $activity = $discord->factory(\Discord\Parts\User\Activity::class, [
        'type' => Activity::TYPE_PLAYING,
        'name' => 'tst',
    ]);
    $discord->updatePresence($activity, 'dnd', true);
    $discord->on('message', function ($message) {
        global $discord,$ping,$pong,$tst,$stop,$lat,$ver;
        if ($message->author->id!=$discord->id){
            if(startswith($message->content,"all.ping")){
                $ping($message);
            }elseif(startswith($message->content,"all.pong")){
                $pong($message);
            }elseif(startswith($message->content,"all.tst")){
                $tst($message);
            }elseif(startswith($message->content,"all.stop")){
                $stop($message);
            }elseif(startswith($message->content,"all.lat")){
                $lat($message);
            }elseif(startswith($message->content,"all.ver")){
                $ver($message);
            }
          }
    });
});
$discord->run();
?>