Managing Readbo releases online<br>
<br>
<label for="name">Generate new release: </label><input id="name" name="name" type="text" placeholder="name"><input id="btnCreateRelease" type="button" value="Go"><br>
<br>
Available releases:
<table>
    <tr>
        <th>Name</th>
        <th>Date</th>
        <th></th>
        <th></th>
    </tr>
    <?php foreach($releases as $r){ ?>
    <tr>
        <td>
            <?php echo $r->name; ?>
        </td>
        <td>
            <?php echo date('Y-m-d H:i',$r->timestamp); ?>
        </td>
        <td>
            <form action="/admin/setProd" method="post">
                <input type="submit" value="Set As Prod">
                <input type="hidden" name="id" value="<?php echo $r->id ?>">
            </form>
        </td>
        <td>
            <form action="/admin/setStage" method="post">
                <input type="submit" value="Set As Stage">
                <input type="hidden" name="id" value="<?php echo $r->id ?>">
            </form>
        </td>
    </tr>
    <?php } ?>
</table>