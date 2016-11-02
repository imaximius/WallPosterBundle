<?php
/**
 * This file is part of the Wall Poster bundle.
 *
 * (c) Ilya Pokamestov
 *
 * @author Ilya Pokamestov
 * @email dario_swain@yahoo.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WallPosterBundle\Provider;

use Facebook\FacebookRequest;
use Facebook\FacebookSession;
use Facebook\GraphObject;
use WallPosterBundle\Post\Post;

class FacebookProvider extends Provider
{
    protected $facebookSession;
    protected $page;

    /**
     * FacebookProvider constructor.
     *
     * @param        $page
     * @param        $appId
     * @param        $appSecret
     * @param string $accessToken
     */
    public function __construct($page, $appId, $appSecret, $accessToken = '')
    {
        $this->page = $page;

        FacebookSession::setDefaultApplication($appId, $appSecret);

        if ($accessToken) {
            $this->facebookSession = new FacebookSession($accessToken);
        } else {
            $this->facebookSession = FacebookSession::newAppSession();
        }
    }

    /**
     * Publish to facebook group
     *
     * @param \WallPosterBundle\Post\Post $post
     * @return bool|string
     */
    public function publish(Post $post)
    {
        $postImages = [];
        if ($this->facebookSession) {
            if ($post->getImages()) {
                $images = $post->getImages();
                foreach ($images as $image) {
                    $postImages[] = $image->getBrowserUrl();
                }
            }

            $facebookRequest = new FacebookRequest(
                $this->facebookSession,
                'POST',
                '/'.$this->page.'/feed',
                [
                    'picture' => implode(',', $postImages),
                    'message' => $post->getMessage(),
                    'link' => $post->getLink()->getUrl(),
                ]
            );

            /** @var GraphObject $graphObject */
            try {
                $graphImageObject = $facebookRequest->execute()->getGraphObject();
                $imageId = $graphImageObject->getProperty('id');

                return $imageId;
            } catch (\Exception $ex) {
                return $ex->getMessage();
            }
        }

        return false;
    }

    /**
     * Edit published post
     *
     * @param \WallPosterBundle\Post\Post $post
     * @param                             $id
     * @return bool|string
     */
    public function edit(Post $post, $id)
    {
        $postImages = [];
        if ($this->facebookSession) {
            if ($post->getImages()) {
                $images = $post->getImages();
                foreach ($images as $image) {
                    $postImages[] = $image->getBrowserUrl();
                }
            }

            $facebookRequest = new FacebookRequest(
                $this->facebookSession,
                'POST',
                "/$id",
                [
                    'picture' => implode(',', $postImages),
                    'message' => $post->getMessage(),
                    'link' => $post->getLink()->getUrl(),
                ]
            );

            /** @var GraphObject $graphObject */
            try {
                $graphImageObject = $facebookRequest->execute()->getGraphObject();
                $imageId = $graphImageObject->getProperty('id');

                return $imageId;
            } catch (\Exception $ex) {
                return $ex->getMessage();
            }
        }

        return false;
    }
} 