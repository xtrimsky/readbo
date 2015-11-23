<?php
    class Twitter_Model extends Model{
		public function getUserTweets(){
			if(is_null($this->rss)){
				$this->rss = new SimplePie();
			}

			$this->rss->set_feed_url(array(
				$url
			));

			$this->rss->enable_order_by_date(false);
			$this->rss->force_feed(true);
			$this->rss->set_url_replacements(false);
			$this->rss->strip_attributes(array('bgsound', 'class', 'expr', 'id', 'onclick', 'onerror', 'onfinish', 'onmouseover', 'onmouseout', 'onfocus', 'onblur', 'lowsrc', 'dynsrc'));

			$this->rss->init();
			$this->rss->handle_content_type();

			$items = $this->rss->get_items();
			$feed = array();

			$ids = array();
			foreach($items as $item){
				$name = 'Unknown';
				if ($author = $item->get_author())
				{
					$name = $author->get_name();
				}

				$id = $item->get_id(true);
				$ids[] = $id;
				$feed[$id]['content'] = $item->get_content();
				$feed[$id]['title'] = $item->get_title();
				$feed[$id]['date'] = strtotime($item->get_local_date());
				$feed[$id]['link'] = $item->get_permalink();
				$feed[$id]['author'] = $name;
			}

			$feed['ids'] = $ids;

			return $feed;
		}
    }