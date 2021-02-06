<html>

    <body>
        
    <p>

        Hey <?php echo $user->username; ?>!

        <br/><br/>
        <?php echo $spotbieUser->first_name . " " . $spotbieUser->last_name; ?>, you have signed up to SpotBie.

        Just <a href='https://spotbie.com/<?php echo $user->confirm; ?>'>click on this link</a> to log-in.

    </p>

    </body>

</html>