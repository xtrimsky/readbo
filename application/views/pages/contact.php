<?php
    if($error != ''){
?>
        <span class="error_text"><?php echo $error; ?></span><br><br>
<?php
    }
?>

<form id="pages_contact_f" method="post" action="/mail">
<table id="pages_contact_form">
    <tr>
        <td class="left_side">Email *</td>
        <td>
            <input name="form[email]" class="required_input" type="email" value="<?php if(is_object($user)){echo $user->email;} ?>">
        </td>
    </tr>
    <tr>
        <td class="left_side">Name *</td>
        <td>
            <input name="form[name]" class="required_input" type="text" value="<?php if(is_object($user)){echo $user->username;} ?>">
        </td>
    </tr>
    <tr>
        <td colspan="2">
            Message *
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <textarea name="form[text]" class="required_input" style="width: 351px; height: 117px;"></textarea>
        </td>
    </tr>
</table>
<input type="submit" value="Send">
</form>