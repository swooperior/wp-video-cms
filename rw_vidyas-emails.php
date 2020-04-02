<?php
//rw_vidya emails

function rw_vidya_receipt_email($customer,$video){
    $customer = get_userdata($customer);
    $name = $customer->first_name;
    $vidID = str_replace('rw_vidya_','',$video);
    $vidTitle = get_the_title($vidID);
    $vidPrice = get_post_meta($vidID,'price_meta',true);

    $email = "
        <p>Dear $name,<p>
        <p>Thank you for your purchase!</p>
        <table>
        <b><tr><td>Item</td><td>Price</td></tr></b>
        <tr><td>$vidTitle</td><td>&pound;$vidPrice</td></tr>
        </table>
        <p>You should now be able to view the video you purchased.  If you have any trouble doing so, please contact us by replying to this email.</p>
    ";

    return $email;
}

function rw_vidya_refund_email($customer,$video){
    $customer = get_userdata($customer);
    $name = $customer->first_name;
    $vidID = str_replace('rw_vidya_','',$video);
    $vidTitle = get_the_title($vidID);
    $vidPrice = get_post_meta($vidID,'price_meta',true);

    $email = "
        <p>Dear $name,<p>
        <p>The payment you made for the video $vidTitle has been refunded and your money is on it&apos;s way back to you.</p>
        <table>
        <b><tr><td>Item</td><td>Price</td></tr></b>
        <tr><td>$vidTitle</td><td>&pound;$vidPrice</td></tr>
        </table>
        <p>Your permissions to view the video have been revoked.  If you believe this was an error please get in touch with us by replying to this email.</p>
    ";

    return $email;
}