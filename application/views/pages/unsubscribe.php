<?php
    if($message != ''){
?>
        <span style="color: green; font-weight: bold;"><?php echo $message; ?></span><br><br>
<?php
    }
?>

<p>If you would like to unsubscribe from any emails from readbo.com, please enter your email address here:</p><br>
<form id="pages_contact_f" method="post" action="/unsubscribe">
    <input name="email" type="email"/><input type="submit" value="Unsubscribe"/>
</form>