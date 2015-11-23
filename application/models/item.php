<?php

require_once('datastore/init.php');
class Item extends Datastore_Table {
    
    protected $table = 'items';
    
    function getSID($item_id){
        $item = $this->get(array('id'=>$item_id), true);
        
        if(!is_object($item)){
            return false;
        }
        
        return $item->item_sid;
    }

    function getBySID($ids, $single_row = false) {
        if (is_array($ids)) {
            if(empty($ids)){
                return $ids;
            }

            $result = $this->getIn('item_sid', $ids, $single_row);
        } else {
            $result = $this->get(array('item_sid' => $ids), $single_row);
        }

        return $result;
    }
    
    function increaseItemViewed($item_id){
        $item_id = $this->escape($item_id);
        
        $sql = "UPDATE items SET read_count = read_count + 1 WHERE id = {$item_id}";
        
        $this->query($sql);
    }
    
    /*
     * an item can be marked as shared, this will prevent the item from being deleted/purged
     */
    function markItemAsShared($item_id){
        $item_id = $this->escape($item_id);
        
        $sql = "UPDATE items SET was_shared = '1' WHERE id = {$item_id}";
        
        $this->query($sql);
    }
    
    function getSIDsInFeeds($feed_ids, $user_id){
        $user_id = $this->escape($user_id);
        
        if (is_array($feed_ids))
            $ids = implode(',', $this->escapeArray($feed_ids));
        else
            $ids = $feed_ids;
        
        $sql =  "SELECT item_sid, feed_id ".
                "FROM items ".
                "WHERE feed_id IN ({$ids})";
                
        $result = $this->query($sql);

        return $result;
    }

    function getUserItems($feed_ids, $user_id, $start, $start_time, $view_all_items) {
        $user_id = $this->escape($user_id);

        //multiple feeds or not
        if (is_array($feed_ids))
            $ids = implode(',', $this->escapeArray($feed_ids));
        else
            $ids = $feed_ids;
        
        if(empty($ids)){return array();}
        
        $limit = '';
        if(!is_null($start)){
            $start = intVal($start);
            $limit = "LIMIT {$start},20";
        }
        
        $hide_lus = "AND lus.user_id IS NULL ";
        if($view_all_items){
            $hide_lus = '';
        }
        
        $query = "SELECT items.id, lus.user_id AS lu, items.item_sid, items.feed_id, items.title, items.content, items.date, items.link, items.author, items.picture, " .
                "likes.user_id AS s_like ".
                "FROM items " .
                "LEFT JOIN lus " .
                "ON (items.item_sid = lus.item_sid AND lus.user_id = {$user_id} AND lus.time <= {$start_time}) " .
                "LEFT JOIN likes ".
                "ON (items.item_sid = likes.item_sid AND likes.user_id = {$user_id}) ".
                "LEFT JOIN items_filtered AS f " .
                "ON (items.item_sid = f.item_sid AND f.user_id = {$user_id}) " .
                "WHERE items.feed_id IN ({$ids}) " .
                "AND f.user_id IS NULL ".
                $hide_lus.
                "AND items.date <= {$start_time} ".
                "AND (likes.user_id IS NULL OR likes.user_id = {$user_id}) " .
                "ORDER BY items.date DESC " . $limit;

        $result = $this->query($query);

        return $result;
    }
    
    function isRead($item_sid, $user_id){
        $count = $this->lu->count(array(
            'user_id' => $user_id,
            'item_sid' => $item_sid
        ));
        
        return $count > 0;
    }
    
    function itemExists($item_sid){
        return !$this->getBySID($item_sid,true) ? false : true;
    }
        
    function markAsRead($item_sid, $user_id, $feed_id){
        if ($this->isRead($item_sid, $user_id) || !$this->itemExists($item_sid)){
            return false;
        }

        $data = array(
            'item_sid' => $item_sid,
            'user_id' => $user_id,
            'feed_id' => $feed_id,
            'time' => time()
        );
        $this->lu->insert($data);

        return true;
    }
    
    function markAsUnread($item_sid, $user_id) {
        $data = array(
            'item_sid' => $item_sid,
            'user_id' => $user_id
        );

        return $this->lu->delete($data);
    }
    
    function markAllAsRead($items, $user_id){
        foreach($items as $item){
            $this->markAsRead($item->item_sid, $user_id, $item->feed_id);
        }
    }
    
    function getList($feed_ids, $user, $start = 0, $start_time = 0, $search = null) {
        $view_all_items = (isset($user->properties->show_read) && $user->properties->show_read);
        
        $items = $this->getUserItems($feed_ids, $user->id, $start, $start_time, $view_all_items);

        $this->load->model('subscription');
        $subscriptions = $this->subscription->getSubscriptionsByFeed($user, $feed_ids);

        $result = array();
        
        foreach ($items as $item) {
            
            
            $subscription =  $subscriptions[$item->feed_id];
            
            $item->isRead = $item->lu == '1';
            
            $item->type = $subscription->type;
            
            $item->is_liked = false;
            if(!is_null($item->s_like)){$item->is_liked = true;}
            unset($item->s_like);
            
            if($item->type == 'rss'){
                $item->author = $subscription->name;
            }

            $result[] = (array) $item;
        }
        
        return $result;
    }
    
    function getLastSID($feed_id){
        $sql =  "SELECT * ".
                "FROM items ".
                "WHERE ".
                "feed_id = {$feed_id} ".
                "ORDER BY date DESC ".
                "LIMIT 1";
        
        $result = $this->query($sql);
        
        if(empty($result)){return '';}
        
        return current($result)->item_sid;
    }
    
    function get20TrendingItems($renew = false){
        require_once(APPPATH.'models/cacheManagers/trends_cm.php');
        $cm = new trends_cm();
        if(!$renew){
            $cache = $cm->fetch();
            if($cache){return $cache;}
            return array();
        }
        
        require_once(APPPATH.'models/objects/view.php');
        $view = new View();
        
        $data = array(
            'min_timestamp' => (time() - (48 * 3600))
        );
        $sql = $view->render(APPPATH.'models/sql/trending.sql',$data);
        
        $result = $this->query($sql);
        $cm->add($result);
        
        return $result;
    }
    
    /*
     * get all old items
     */
    function getOldItems($timestamp){
        $timestamp = $this->escape($timestamp);
        
        $sql = "SELECT item_sid FROM {$this->table} WHERE date <= {$timestamp}";
        
        return $this->query($sql);
    }
    
    function clearOldItemData(){
        ini_set('memory_limit', '512M'); //uses a lot of memory
        set_time_limit(3600); //maximum one hour
        
        $this->load->model('feed');
        $feed_ids = $this->feed->getAllFeedIds();
        
        $feeds_with_items_to_clean = array();
        $oldestTime = time() - (3600 * 24 * 20); //20 days
        
        //finding maximum time for each items
        foreach($feed_ids as $id){
            if($id == 8){continue;} //CNN does some weird things
            
            $sql = "SELECT date FROM items WHERE feed_id = '{$id}' ORDER BY date DESC LIMIT 50,1";
            $item = current( $this->query($sql) );
            
            if(!empty($item)){
                $date = $item->date;
                
                if($date > $oldestTime){
                    $date = $oldestTime;
                }
                
                $feeds_with_items_to_clean[$id] = $date;
            }
        }
        unset($feed_ids);
        
        foreach($feeds_with_items_to_clean as $feed_id => $item_date){
            $sql = "SELECT item_sid, was_shared FROM items WHERE feed_id = {$feed_id} AND date < {$item_date} LIMIT 1000";
            $items = $this->query($sql);
            
            if(!empty($items)){
                foreach($items as $item){
                    
                    if($item->was_shared == 1){continue;}
                    
                    $sql1 = "DELETE FROM items_filtered WHERE item_sid = '{$item->item_sid}'";
                    $sql2 = "DELETE FROM likes WHERE item_sid = '{$item->item_sid}'";
                    $sql3 = "DELETE FROM viewed_posts WHERE item_sid = '{$item->item_sid}'";
                    $sql4 = "DELETE FROM lus WHERE item_sid = '{$item->item_sid}'";
                    $sql5 = "DELETE FROM items WHERE item_sid = '{$item->item_sid}'";

                    $this->query($sql1);
                    $this->query($sql2);
                    $this->query($sql3);
                    $this->query($sql4);
                    $this->query($sql5);
                }
            }
        }
        unset($feeds_with_items_to_clean);
        
    }

}