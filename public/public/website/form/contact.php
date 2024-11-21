<?php
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $subject = $_POST["subject"];
    $toEmail = "saadey7@gmail.com";
    $message = ' 
                <html>
                <body> 
                    <table style="width: 100%;"> 
                        <tr> 
                            <th style="font-size: 18px;">Name:</th>
                            <td style="font-size: 15px;">' . $name . '</td> 
                        </tr>
                        <tr> 
                            <th style="font-size: 18px;">Email:</th>
                            <td style="font-size: 15px;">' . $email . '</td> 
                        </tr>
                        <tr> 
                            <th style="font-size: 18px;">Phone:</th>
                            <td style="font-size: 15px;">' . $phone . '</td> 
                        </tr>
                        <tr> 
                            <th style="font-size: 18px;">Date:</th>
                            <td style="font-size: 15px;">' . date('d M Y') . '</td> 
                        </tr>
                        <tr> 
                            <th style="font-size: 18px;">Subject:</th>
                            <td style="font-size: 15px;">' . $subject . '</td> 
                        </tr>
                    </table> 
                    <br>
                    <p>' . $_POST["message"] . '</p>
                </body> 
                </html>';
    
    // Set content-type header for sending HTML email 
    $headers = "MIME-Version: 1.0" . "\r\n"; 
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n"; 
     
    // Additional headers  

    $mail =  mail($toEmail, $_POST["subject"], $message, $headers);

    if($mail)
    {
        print "<p class='success'>Thank you for getting in touch!</p>";
    } else {
        print "<p class='Error'>Problem in Sending Mail.</p>";
    }
?>