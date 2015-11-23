<div style="width: 100%; float: right;">
    <div id="close_panel" title="Close panel"></div>
</div>
<table class="panel_container">
    <tr>
        <td>
            <h2>Add news!</h2>
            <p>
                See the big <b>Add news</b> button on the left?<br>
                Click on it! You can follow any news websites you like.<br>
                CNN, Digg, CNBC ... Everything is there.<br>
                <br>
                You can also add specific twitter searches.
            </p>
        </td>
        <?php if(empty($user->facebook_uid)){ ?>
        <td>
            <h2>Facebook</h2>
            <p>
                Have a Facebook account ?<br>
                Connect it to Readbo, you can follow Facebook directly from here.<br>
                <br>
                <div style="text-align: center; width: 100%;">
                    <button class="fb_connect">Connect</button>
                </div>
            </p>
        </td>
        <?php } ?>
        <td>
            <h2>Google Reader</h2>
            <p>
                Already have a Google Reader account ?<br>
                You can import all your feeds to Readbo.<br>
                Visit Readbo's <b>Settings</b> and click on "Import/Export"
            </p>
        </td>
    </tr>
</table>