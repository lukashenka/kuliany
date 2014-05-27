<?php
/*---------------------------------------------------------------------------
Be free to change what you want... @ your own risk :-)
Would you like to use your own template?
1.) Copy this code in a other file and save it with the .tpl prefix (e.g. myfile.tpl)
2.) Select your template on DMSGuestbook admin page for use

Guestbook entries

CSS variables:
css_post_header1 				= id, name
css_post_header2				= url
css_post_header3				= email
css_post_header4				= date, ip
css_post_separator				= separator
css_post_message				= message text
Edit these CSS settings on DMSGuestbook admin panel (CSS section)

Function variables:
$show_id						= id
$message_name					= name
$show_url						= url
$show_email						= email
$gravatar_url					= gravatar url (hash)
$show_ip						= ip
$displaydate 					= date and time
$slash							= separator if ip is visible
$message_text					= guestbook message text
$additional_text				= user defined additional text
Edit these variables on DMSGuestbook admin panel
---------------------------------------------------------------------------*/
            $GuestbookEntries3 = "
            <div>
            <div class='guest-post'>
            <div class='post-name'><strong>$message_name</strong> напісаў:
            </div>



    <!-- $additional_text can be place where ever you want. Using html and css tags to format the appearance of this. -->

    <div class='post_message'>$message_text</div>
    ";

    ?>
