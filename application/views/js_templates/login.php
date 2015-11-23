<div id="tp_dialog_register">
    <div>
        <div id="register-left-column">
            <h1 id="register-title">Register</h1>
            <div class="form-error">&nbsp;</div>
            <table>
                <tr>
                    <td><label for="txtEmail">Email address</label></td>
                    <td><input id="txtEmail" type="email" tabIndex="1"></td>
                </tr>
                <tr>
                    <td><label for="txtUsername">Username</label></td>
                    <td><input id="txtUsername" type="text" tabIndex="2"></td>
                </tr>
                <tr>
                    <td><label for="txtPassword">Password</label></td>
                    <td><input id="txtPassword" type="password" tabIndex="3"></td>
                </tr>
                <?php /*
                <tr>
                    <td><label for="txtInviteCode">Invitation code</label></td>
                    <td><input id="txtInviteCode" type="text" tabIndex="3"></td>
                </tr> */ ?>
            </table>
            <br>
            <button id="btnCreateAccount" tabIndex="4">Create</button>
        </div>
        <div id="register-right-column" style="line-height: 1.5">
            <p>
                By creating an account you agree to the <a href="/terms" target="_blank">Terms of Use</a> and the <a href="/privacy" target="_blank">Privacy Policy</a>.
            </p>
            <?php /*
            Use your existing account:<br>
            <ul>
                <li><a href="#" class="register-link-facebook"><span class="icon"></span>Facebook</a></li>
                <!--<li><a href="#" class="register-link-google"><span class="icon"></span>Google</a></li> -->
                <!--<li><a href="#" class="register-link-twitter"><span class="icon"></span>Twitter</a></li>-->
            </ul> */ ?>
        </div>
    </div>
    <div class="clear"></div>
</div>