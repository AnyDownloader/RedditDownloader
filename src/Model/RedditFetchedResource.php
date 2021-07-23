<?php
namespace AnyDownloader\RedditDownloader\Model;

use AnyDownloader\DownloadManager\Model\FetchedResource;

final class RedditFetchedResource extends FetchedResource
{
    /**
     * @return string
     */
    public function getExtSource(): string
    {
        return 'reddit';
    }
}

