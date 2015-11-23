<h2>Invitation Codes</h2>
<form method="post" action="">
    <input type="text" name="code" placeholder="code">
    <input type="text" name="amount" placeholder="amount">
    <input type="submit" value="add">
</form>
<table id="users_table" style="position: relative; top: 20px;">
    <th>ID</th>
    <th>Code</th>
    <th>amount</th>
    <?php foreach($codes as $c){ ?>
    <tr>
        <td>
            <?php echo $c->id; ?>
        </td>
        <td>
            <?php echo $c->code; ?>
        </td>
        <td>
            <?php echo $c->amount; ?>
        </td>
    </tr>
    <?php } ?>
</table>