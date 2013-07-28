<?php
class Lib_View_Helper_RenderTopic extends Zend_View_Helper_Abstract
{
    public function renderTopic(Zend_Db_Table_Rowset $posts)
    {
        $class= '';
    	$content  = '<ul>'.PHP_EOL;

        foreach($posts as $post){
        	if(!empty($post->tone) && isset(Data::$tones[$post->tone])){
        		$class = ' class="'. Data::$tones[$post->tone] .'"';	
        	}
        
            $content .= "<li$class>".$post->getDescription().'</li>'.PHP_EOL;
        }
die('prout');
        $content .= '</ul>'.PHP_EOL;
        return $content;
    }
}