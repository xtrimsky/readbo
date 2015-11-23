<h2>Dashboard <a style="font-size: 10px; color: blue; text-decoration: none; font-weight: normal;" href="/admin/refreshData" title="Avoid using this often">refresh</a></h2>
Active users: <?php echo $active_users; ?><br>
Visited last 24 hours: <?php echo $active_last_day; ?> (<?php echo intVal($active_last_day / ($active_users/100)); ?>%)<br>
Visited last week: <?php echo $active_last_week; ?> (<?php echo intVal($active_last_week / ($active_users/100)); ?>%)<br>
Logged in Once: <?php echo $loggedin_once; ?> (<?php echo intVal($loggedin_once / ($active_users/100)); ?>%)<br>
Users With Facebook: <?php echo $users_with_facebook; ?> (<?php echo intVal($users_with_facebook / ($active_users/100)); ?>%)<br>
Users With Twitter: <?php echo $users_with_twitter; ?> (<?php echo intVal($users_with_twitter / ($active_users/100)); ?>%)<br>
Waiting List: <?php echo $waiting_list; ?><br>
Memcache running: <?php if($memcache_running){echo 'Yes';}else{echo 'No';} ?><br>
<br>
<button id="sendInvites">Send all invites</button>