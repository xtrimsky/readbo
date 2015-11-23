<html>
<head>
<title>404 Page Not Found</title>
<style type="text/css">
    body{
        background-color: black;
        font-family: "lucida grande",tahoma,verdana,arial,sans-serif;
	font-size: 20px;
    }

    #error{
        margin: 0 auto;
        background: #000 url(<?php echo MEDIA_SERVER; ?>/images/404.jpg) no-repeat;
        position: relative;
        top: 10px;
        width: 1024px;
        height: 768px;
    }
    
    #bubble{
        background-color: white;
        position: absolute;
        left: 462px;
        top: 80px;
    }
    
    a{
        color: blue;
    }

</style>
</head>
<body>
	<div id="error">
            <div id="bubble">
                Wait, why are you here ?<br>
                <a href="http://readbo.com" title="Go back to Readbo">Go back to Readbo</a>
            </div>
	</div>
</body>
</html>