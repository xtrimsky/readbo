<?php

require_once('datastore/init.php');
class Lu extends Datastore_Table {

    protected $table = 'lus';

    function allItemsUserRead($user_id) {
        $user_id = $this->escape($user_id);

        $sql = "SELECT * " .
                "FROM {$this->table} " .
                "WHERE user_id = {$user_id}";

        $result = $this->query($sql);

        $response = array();
        foreach ($result as $r) {
            $response[$r->item_sid] = 1;
        }

        return $response;
    }

}