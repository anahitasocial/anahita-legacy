<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

//set a max limit for the story
define('STORY_MAX_LIMIT', 5000);

/**
 * Story Controller
 * 
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComStoriesControllerStory extends ComBaseControllerService
{
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		$this->getCommentController()->registerCallback('after.add', array($this, 'createStoryCommentNotification'));		
	}
		
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */	
	protected function _initialize(KConfig $config)
	{
		parent::_initialize($config);
		
		//reset the commentable behavior
		AnHelperArray::unsetValues($config->behaviors, 'commentable');		
		$config->behaviors->append(array('commentable'=>array('publish_comment'=>false)));
		
		$config->behaviors->append(array('publisher'));
	}
				
	/**
	 * Post a story
	 * 
	 * @param  KCommandContext $context
	 * @return string;
	 */
	protected function _actionPost($context)
	{
		$data 	 = $context->data;
		$actor   = $data->actor;
		$viewer	 = get_viewer();
	
		$data['body'] = LibBaseTemplateHelperText::truncate($data['body'], array('length'=>STORY_MAX_LIMIT));
		
		if ( $data->private_message && is_person($actor) && !$actor->eql($viewer) ) {
			$name = 'private_message';
		} else
			$name = 'story_add';
			
		$component = $actor->component ;
		
		$story = $data->entity = $data->story = $this->createStory(array(
		    'component' => $component,
			'name'		=> $name,
			'subject'	=> get_viewer(),
			'target'	=> $actor,
			'owner'		=> $actor,
			'body'		=> $data['body']
		));
;
		if ( $name == 'private_message' ) {
			$data->story->setAccess(array($actor->id, $viewer->id));
		}
    		
		if ( !preg_match('/\S/', $story->body) ) {
		    return false;
		}

		if ( $this->commit($context) === false ) {
		    return false;
		}
				
		$data->actor = $actor;
		
		$this->setView('story')->layout('list');

		$output = $this->render($context);
			
		$helper = clone $this->getView()->getTemplate()->getHelper('parser');
		
		$data   = array(
			'story' 	=> $story,
			'actor' 	=> $actor,
			'viewer'	=> $viewer,
			'channels'	=> $data->channels,
			'data'		=> $helper->parse($story) 
		);
		
		if ( $name != 'private_message' )
		    dispatch_plugin('connect.onPostStory', $data);
        

	    $subscribers = array();
        
	    if ( $actor->isSubscribable() ) {
	        $subscribers   = $actor->subscriberIds->toArray();
            $subscribers[] = $actor;
	    }
	    else 
            $subscribers = array($actor);
            
	    $notifcation = $this->createNotification(array(
	        'component' => $component,   
	        'name'      => $name,
	        'target'    => $actor,
	        'object'         => $story,
	        'subscribers'    => $subscribers
	    ))
        ->setType('post', array('new_post'=>true))
	    ;
		
		return $output;
	}
		
    /**
     * Browse action
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     */
	protected function _actionBrowse($context)
	{
		$data     = $context->data;
		$query 	  = $this->getRepository()->getQuery()			
					->limit( $this->start == 0 ?  20 : 20, $this->start );

		if ( $data->actor ) {
			$query->owner($data->actor);
		} 
		else {
			$ids 	= get_viewer()->leaderIds->toArray();
			$ids[]	= get_viewer()->id;
			$query->where('owner.id','IN', $ids);
		}
	
		$apps 		  =	 $this->getService('repos:apps.app')->fetchSet();
		$summary_keys =  new KConfig();
		if ( count($apps ) )
		foreach($apps as $app) {
			$context = new KCommandContext();
			$app->getDelegate()->setStoryOptions($context);
			$summary_keys->append(array(
				$app->component => pick($context->summarize, array())
			));
		}
		
		$keys = KConfig::unbox($summary_keys);
		
		$data->stories = $data->entities = $query->summerize($keys)->toEntitySet();		
	}
	
	/**
	 * Delete a story
	 * 
	 * @return boolean
	 */
	protected function _actionDelete($context)
	{
		$data = $context->data;
		$data->entity->delete();
	}
	
	/**
	 * Creates a notifiction after a comment
	 * 
	 * @return void
	 */
	public function createStoryCommentNotification(KCommandContext $context)
	{
		$data 	= $context->data;
		
		$owners = array($data->entity->parent->target->id);
		
		if ( $data->entity->parent->isSubscribable() ) 
		{
		    $owners[] = $data->entity->parent->subscriberIds->toArray();
		}
		
		$notification = $this->createNotification(array(
			'name'		      => 'story_comment',
		    'subscribers'     => $owners,			
			'comment'	      => $data->comment			
		));
		
		$notification->save();
	}
}