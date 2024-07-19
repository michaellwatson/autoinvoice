<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?php echo $listing['ad_invoicenumber'];?></title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        /*
        th {
            background-color: #f2f2f2;
        }
        */
        .no-border td {
            border: none;
        }
        body{
            font-family:Arial, Helvetica, sans-serif;
            font-size:11px;
        }
    </style>
</head>

<body>
    <table>
        <tr class="no-border">
            <td colspan="3" style="width:50%;"><h2>INVOICE</h2></td>
            <td>
                <p>Atlis Management Services Limited<br>31 Alder Road<br>London</p>
                <br>
                <br>
                <br>
                <?php echo $listing['ad_dateofinvoice'];?>
            </td>
        </tr>
        <tr class="no-border">
            <td colspan="3"><?php echo $listing['ad_salutation'];?> <?php echo $listing['ad_firstname'];?> <?php echo $listing['ad_lastname'];?><br><?php echo $listing['ad_address1'];?><br><?php echo $listing['ad_address2'];?><br><?php echo $listing['ad_county'];?><br><?php echo $listing['ad_postcode'];?><br></td>
            <td>
                <table style="width:100%;">

                    <tr>
                        <td style="padding:0px;">
                            <strong>VAT registration no.</strong>
                        </td>
                        <td style="border:1px solid black;text-align:center;">
                            262832696
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0px;">
                            <strong>Invoice no.</strong>
                        </td>
                        <td style="border:1px solid black;text-align:center;">
                            <?php echo $listing['ad_invoicenumber'];?>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
        <tr class="no-border">
            <td colspan="4">
                <br>
                <br>
                Invoice for services due under Letter Of Engagement Agreement Date: <?php echo $listing['ad_letterofengagementdate'];?>
                    
            </td>
        </tr>
    </table>
    <br>
    <br>
    <table>
        <tr>
            <th style="text-align:center;">Ref</th>
            <th>Due Date</th>
            <th>Net £</th>
            <th>VAT (20%)</th>
            <th>Gross £</th>
        </tr>
        <tr>
            <td><?php echo $listing['ad_invoicenumber'];?></td>
            <td><?php echo $listing['ad_dateofpayment'];?></td>
            <td><?php echo $listing['ad_netamount'];?></td>
            <td><?php echo $listing['ad_vatamount'];?></td>
            <td><?php echo $listing['ad_grossamount'];?></td>
        </tr>
        <tr style="border-bottom:2px black solid;border-top:1px solid black !important;padding-top:20px;">
            <th colspan="2">TOTAL</th>
            <th><?php echo $listing['ad_netamount'];?></th>
            <th><?php echo $listing['ad_vatamount'];?></th>
            <th><?php echo $listing['ad_grossamount'];?></th>
        </tr>
    </table>
    <table>
        <tr class="no-border">
            <td colspan="5">
                <br>
                <br>
                <p><i>Please make payment listed above <b>ON THE DUE DATE</b> to our bank account detailed below:</i></p>
                <p>

                <table style="width:40%;">
                    <tr>
                        <td style="padding:0px;">Account Name:</td>
                        <td style="text-align: right;">Atlis Management</td>
                    </tr>

                    <tr>
                        <td style="padding:0px;">Sort Code:</td>
                        <td style="text-align: right;">20-72-33</td>
                    </tr>

                    <tr>
                        <td style="padding:0px;">Account Number:</td>
                        <td style="text-align: right;">70009741</td>
                    </tr>
                </table>

                </p>
            </td>
        </tr>
        <tr class="no-border">
            <td colspan="5">
                <!--<p>Atlis Management Services Limited<br>31 Alder Road<br>London</p>-->
            </td>
        </tr>
    </table>
</body>
</html>
