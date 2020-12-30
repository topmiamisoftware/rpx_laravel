<html>

    <body>
        
    <p>

        Hey <?php echo $user->username; ?>!

        <br/><br/>
        <?php echo $spotbieUser->first_name . " " . $spotbieUser->last_name; ?>, you have signed up to SpotBie.
        Don't forget to <a href='https://spotbie.com/?c=<?php echo $user->confirm; ?>'>log-in</a> to confirm your account.
        Just <a href='https://spotbie.com/?c=<?php echo $user->confirm; ?>'>click on this link</a>, enter your password, and you are all set.

        <br/><br/>

        Account Confirm Link: <a href='https://spotbie.com/?c=<?php echo $user->confirm; ?>'>https://spotbie.com/?c=<?php echo $user->confirm; ?></a>
        
        <br/><br/>

        Account Confirm Code: <b><?php echo $user->confirm; ?></b>

    </p>

    </body>

</html>