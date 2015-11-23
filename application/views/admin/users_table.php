<table id="users_table">
    <th>ID</th>
    <th>Username</th>
    <th>Email</th>
    <th>Last login</th>
    <th>Logins</th>
    <th>Log as</th>
    <th>Admin</th>
    <?php foreach($users as $user){
        $user->properties = json_decode($user->properties);
    ?>
    <tr>
        <td>
            <?php echo $user->id; ?>
        </td>
        <td>
            <?php echo $user->username; ?>
        </td>
        <td>
            <?php echo $user->email; ?>
        </td>
        <td>
            <?php echo date('m/d/y H:i',$user->last_login); ?>
        </td>
        <td>
            <?php echo $user->logins; ?>
        </td>
        <td>
            <button onClick="window.location = '/admin/logAs/user/<?php echo $user->id; ?>';">Log As</button>
        </td>
        <td>
            <?php
            if(isset($user->properties->isAdmin)){
                echo 'Yes';
            }else{
                ?>
                <button onClick="makeAdmin(<?php echo $user->id; ?>);">Make Admin</button>
                <?php
            }
            ?>
        </td>
    </tr>
    <?php } ?>
</table>