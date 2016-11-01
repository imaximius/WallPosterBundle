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

	public function __construct($page ,$appId, $appSecret, $accessToken = '')
	{
		$this->page = $page;

		FacebookSession::setDefaultApplication($appId,$appSecret);

		if($accessToken)
		{
			$this->facebookSession = new FacebookSession($accessToken);
		}
		else
		{
			$this->facebookSession= FacebookSession::newAppSession();
		}
	}

    public function publish(Post $post)
    {
	if($this->facebookSession)
	{
	    if($post->getImages())
	    {
		$images = $post->getImages();
		foreach($images as $image) {
		    $facebookImageRequest = new FacebookRequest(
			    $this->facebookSession,
			    'POST',
			    '/'.$this->page.'/photos',
			    [
				'url' => $image->getBrowserUrl(),
				'caption' => $post->getMessage()
			    ]
		    );

		    /** @var GraphObject $graphObject */
		    try
		    {
			$graphImageObject = $facebookImageRequest->execute()->getGraphObject();
			$imageId = $graphImageObject->getProperty('id');

			return $imageId;
		    }
		    catch(\Exception $ex)
		    {
			return $ex->getMessage();
		    }
		}
	    }
	}
	return false;
    }
} 