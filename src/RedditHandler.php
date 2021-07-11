<?php
namespace AnyDownloader\RedditDownloader;

use AnyDownloader\DownloadManager\Exception\BadResponseException;
use AnyDownloader\DownloadManager\Exception\NothingToExtractException;
use AnyDownloader\DownloadManager\Exception\NotValidUrlException;
use AnyDownloader\DownloadManager\Handler\BaseHandler;
use AnyDownloader\DownloadManager\Model\Attribute\AuthorAttribute;
use AnyDownloader\DownloadManager\Model\Attribute\TitleAttribute;
use AnyDownloader\DownloadManager\Model\FetchedResource;
use AnyDownloader\DownloadManager\Model\ResourceItem\ResourceItemFactory;
use AnyDownloader\DownloadManager\Model\URL;
use AnyDownloader\RedditDownloader\Model\RedditFetchedResource;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class RedditHandler extends BaseHandler
{
    const SUCCESS_HTTP_CODE = 200;
    const USER_AGENT = 'Chrome: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36';

    /**
     * @var string[]
     */
    protected $urlRegExPatterns = [
        'regular' => '/[\/\/|www.]reddit\.[a-z]+\/(.*)/',
        'media' => '/[\/\/|www.]redditmedia\.[a-z]+\/(.*)/',
        'short' => '/[\/\/|www.]redd\.it\/(.*)+/'
    ];

    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * RedditHandler constructor.
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param URL $url
     * @return FetchedResource
     * @throws NothingToExtractException
     * @throws TransportExceptionInterface
     * @throws BadResponseException
     * @throws NotValidUrlException
     */
    public function fetchResource(URL $url): FetchedResource
    {
        $resource = new RedditFetchedResource($url);
        if (
            $url->matchPattern($this->urlRegExPatterns['short']) ||
            $url->matchPattern($this->urlRegExPatterns['media'])
        ) {
            $url->followLocation();
        }

        $response = $this->client->request(
            'GET',
            $url->getValue() . '.json',
            [
                'verify_peer' => false,
                'verify_host' => false,
                'headers' => [
                    'user-agent' => self::USER_AGENT
                ]
            ]
        );
        if ($response->getStatusCode() != self::SUCCESS_HTTP_CODE) {
            throw new BadResponseException();
        }

        $json = json_decode($response->getContent());
        if (json_last_error()) {
            throw new NothingToExtractException();
        }
        try {
            if ($json[0]->data->children[0]->data->is_video === false && isset($json[0]->data->children[0]->data->crosspost_parent_list)) {
                $data = $json[0]->data->children[0]->data->crosspost_parent_list[0];
                $video = $json[0]->data->children[0]->data->crosspost_parent_list[0]->media->reddit_video;
            } else {
                $data = $json[0]->data->children[0]->data;
                $video = $json[0]->data->children[0]->data->media->reddit_video;
            }
        } catch (\Exception $exception) {
            throw new NothingToExtractException();
        }
        $videoResourceItem = ResourceItemFactory::fromURL(
            URL::fromString($video->fallback_url),
            $video->width . 'x' . $video->height
        );
        $resource->setVideoPreview($videoResourceItem);
        $resource->addItem($videoResourceItem);

        if (isset($data->thumbnail) && $data->thumbnail !== 'default') {
            $imagePreview = ResourceItemFactory::fromURL(
                URL::fromString($data->thumbnail),
                $data->thumbnail_width . 'x' . $data->thumbnail_height
            );
            $resource->addItem($imagePreview);
            $resource->setImagePreview($imagePreview);
        }

        if (isset($data->title)) {
            $resource->addAttribute(new TitleAttribute($data->title));
        }

        if(isset($data->author)) {
            $resource->addAttribute(new AuthorAttribute('', $data->author));
        }

        return $resource;
    }

}