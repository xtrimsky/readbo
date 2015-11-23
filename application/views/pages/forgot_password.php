<?php
    if($error != ''){
?>
        <span style="color: red; font-weight: bold;"><?php echo $error; ?></span><br><br>
<?php
    }
?>
        
<p>
    What you forgot your password ?<br>
    Who does that ? Your password is the most important thing on the internet!<br>
    Luckily for you, our website has an advanced technology that allows you to reset your password!<br>
    Try it out!
</p>
<form method="post" action="" style="margin-top: 20px;">
    <label>Enter your email here: </label><input name="email" type="email" style="width: 250px;">
    <input type="submit" value="Send email to reset password">
</form>