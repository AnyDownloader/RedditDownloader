# RedditDownloader
Get video source with preview image from Reddit

Install via Composer
```
composer require any-downloader/reddit-downloader
```

You have two options of how to use this package

1. Use it standalone

```php
<?php
use AnyDownloader\DownloadManager\Model\URL;
use AnyDownloader\RedditDownloader\RedditHandler;
use Symfony\Component\HttpClient\HttpClient;

include_once 'vendor/autoload.php';

$redditHandler = new RedditHandler(HttpClient::create());
$url = URL::fromString('https://www.reddit.com/r/IndieDev/comments/oh9hg1/just_a_happy_loki/');
$res = $redditHandler->fetchResource($url);

print_r($res->toArray());
/**
Array
(
    [source_url] => https://www.reddit.com/r/IndieDev/comments/oh9hg1/just_a_happy_loki/
    [preview_image] => Array
        (
            [type] => image
            [format] => jpg
            [quality] => 140x83
            [url] => https://b.thumbs.redditmedia.com/DJQcz_oL9VdQ16XHU-M8APPjS1LSs3JYQ63aLDEl-fg.jpg
            [mime_type] => image/jpg
        )

    [preview_video] => Array
        (
            [type] => video
            [format] => mp4
            [quality] => 800x480
            [url] => https://v.redd.it/3cci7vuyiaa71/DASH_480.mp4?source=fallback
            [mime_type] => video/mp4
        )

    [attributes] => Array
        (
            [title] => Just a happy Loki
            [author] => Array
                (
                    [id] =>
                    [avatar_url] =>
                    [full_name] =>
                    [nickname] => Malbers_Animations
                    [avatar] =>
                )

        )

    [items] => Array
        (
            [video] => Array
                (
                    [0] => Array
                        (
                            [type] => video
                            [format] => mp4
                            [quality] => 800x480
                            [url] => https://v.redd.it/3cci7vuyiaa71/DASH_480.mp4?source=fallback
                            [mime_type] => video/mp4
                        )

                )

            [image] => Array
                (
                    [0] => Array
                        (
                            [type] => image
                            [format] => jpg
                            [quality] => 140x83
                            [url] => https://b.thumbs.redditmedia.com/DJQcz_oL9VdQ16XHU-M8APPjS1LSs3JYQ63aLDEl-fg.jpg
                            [mime_type] => image/jpg
                        )

                )

        )

)

**/
```

2. Use it with DownloadManager.
Useful in case if your application is willing to download files from different sources (i.e. has more than one download handler)

```php
<?php
use AnyDownloader\DownloadManager\DownloadManager;
use AnyDownloader\DownloadManager\Model\URL;
use AnyDownloader\RedditDownloader\RedditHandler;
use Symfony\Component\HttpClient\HttpClient;

include_once 'vendor/autoload.php';


$dm = new DownloadManager();
$dm->addHandler(new RedditHandler(HttpClient::create()));
$url = URL::fromString('https://www.reddit.com/r/IndieDev/comments/oh9hg1/just_a_happy_loki/');
$res = $dm->fetchResource($url);

print_r($res->toArray());
```

[iwannacode.net](https://iwannacode.net)
