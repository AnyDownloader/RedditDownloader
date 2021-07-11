<?php
namespace AnyDownloader\TikTokDownloader\Test;

use AnyDownloader\DownloadManager\Model\URL;
use AnyDownloader\RedditDownloader\Model\RedditFetchedResource;
use AnyDownloader\RedditDownloader\Test\Mock\HttpClientMock;
use AnyDownloader\RedditDownloader\RedditHandler;
use PHPUnit\Framework\TestCase;

class RedditHandlerParserTest extends TestCase
{
    /** @test */
    public function handler_parses_page_correctly_and_returns_resource_model()
    {
        $url = URL::fromString('https://www.reddit.com/r/IndieDev/comments/oh9hg1/just_a_happy_loki/');

        $handler = new RedditHandler(new HttpClientMock());

        $res = $handler->fetchResource($url);

        $this->assertInstanceOf(RedditFetchedResource::class, $res);

        $this->assertCount(2, $res->getItems());

        $this->assertEquals(
            'https://v.redd.it/3cci7vuyiaa71/DASH_480.mp4?source=fallback',
            $res->getPreviewVideo()->getUrl()->getValue()
        );

        $this->assertEquals(
            'https://b.thumbs.redditmedia.com/DJQcz_oL9VdQ16XHU-M8APPjS1LSs3JYQ63aLDEl-fg.jpg',
            $res->getPreviewImage()->getUrl()->getValue()
        );

        $this->assertEquals([
            'source_url' => 'https://www.reddit.com/r/IndieDev/comments/oh9hg1/just_a_happy_loki/',
            'preview_image' => [
                'type' => 'image',
                'format' => 'jpg',
                'title' => '140x83',
                'url' => 'https://b.thumbs.redditmedia.com/DJQcz_oL9VdQ16XHU-M8APPjS1LSs3JYQ63aLDEl-fg.jpg',
                'mime_type' => 'image/jpg',
            ],
            'preview_video' => [
                'type' => 'video',
                'format' => 'mp4',
                'title' => '800x480',
                'url' => 'https://v.redd.it/3cci7vuyiaa71/DASH_480.mp4?source=fallback',
                'mime_type' => 'video/mp4',
            ],
            'attributes' => [
                'title' => 'Just a happy Loki',
                'likes_count' => 477,
                'comments_count' => 3,
                'author' => [
                    'id' => '',
                    'avatar_url' => '',
                    'full_name' => '',
                    'nickname' => 'Malbers_Animations',
                    'avatar' => null
                ]
            ],
            'items' => [
                'video' => [
                    [
                        'type' => 'video',
                        'format' => 'mp4',
                        'title' => '800x480',
                        'url' => 'https://v.redd.it/3cci7vuyiaa71/DASH_480.mp4?source=fallback',
                        'mime_type' => 'video/mp4',
                    ],
                ],
                'image' => [
                    [
                        'type' => 'image',
                        'format' => 'jpg',
                        'title' => '140x83',
                        'url' => 'https://b.thumbs.redditmedia.com/DJQcz_oL9VdQ16XHU-M8APPjS1LSs3JYQ63aLDEl-fg.jpg',
                        'mime_type' => 'image/jpg',
                    ],
                ]
            ],
        ], $res->toArray());
    }
}