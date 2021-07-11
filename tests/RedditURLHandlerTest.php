<?php
namespace AnyDownloader\RedditDownloader\Tests;

use AnyDownloader\DownloadManager\Model\URL;
use AnyDownloader\RedditDownloader\RedditHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

class RedditURLHandlerTest extends TestCase
{
    /** @test */
    public function handler_validates_given_url()
    {
        $handler = new RedditHandler(HttpClient::create());
        $url = URL::fromString('https://www.reddit.com/r/IndieDev/comments/oh9hg1/just_a_happy_loki/');
        $this->assertTrue($handler->isValidUrl($url));
    }

    /** @test */
    public function handler_validates_given_short_url()
    {
        $handler = new RedditHandler(HttpClient::create());
        $url = URL::fromString('https://redd.it/oh9hg1');
        $this->assertTrue($handler->isValidUrl($url));
    }

    /** @test */
    public function handler_validates_given_media_url()
    {
        $handler = new RedditHandler(HttpClient::create());
        $url = URL::fromString('https://www.redditmedia.com/r/IndieDev/comments/oh9hg1/just_a_happy_loki/');
        $this->assertTrue($handler->isValidUrl($url));
    }

    /** @test */
    public function handler_can_not_validate_given_url()
    {
        $handler = new RedditHandler(HttpClient::create());
        $url = URL::fromString('https://breadit.com/watch/demandingpapayawhipaardwolf');
        $this->assertFalse($handler->isValidUrl($url));
    }
}