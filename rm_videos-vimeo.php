<?php

//VIMEO API UPLOAD ACCESS REQUIRED FOR THIS SCRIPT

require __DIR__.'/vendor/autoload.php';
use Vimeo\Vimeo;

$client = new Vimeo("bb455bdb3c510a796f9cf61c3d59997c9b89b0bb","CNhY3qNmD3tDlyA8abL3r6Op3M5w/WcnhHSaW2/O7uMLVTmi/KzO//cde4oOq1G52B9NXZxdQ5dEuI8Tssy1fzH4iE+ZvUAHHKYDdsIZnWQQXe4aRwdXmCf8JJRPJAuy", "f11234f24624f0efc9a6a0a9136d4b7e");
?>
<form action="<?php echo __FILE__; ?>" method="POST">
    <label for="video_uri">Video Upload:</label>
    <input type="file" name="video_uri" id="video_uri">
</form>
<?php
if(isset($_POST['video_uri'])){
    $file_name = $_POST['video_uri'];
    $uri = $client->upload($file_name, array(
        "name" => $file_name,
        "description" => "Video uploaded from rm_videos."
    ));

    $response = $client->request($uri . '?fields=transcode.status');
    if ($response['body']['transcode']['status'] === 'complete') {
    print 'Your video finished transcoding.';
    $response = $client->request($uri . '?fields=link');
    
    echo "Your video link is: " . $response['body']['link'];
    } elseif ($response['body']['transcode']['status'] === 'in_progress') {
    print 'Your video is still transcoding.';
    } else {
    print 'Your video encountered an error during transcoding.';
    }

}
