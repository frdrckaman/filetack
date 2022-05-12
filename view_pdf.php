<?php
require_once 'php/core/init.php';
require_once 'pdf.php';

$pdf = new Pdf();
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();
if ($_GET['pdf'] == 1) {
    foreach ($override->get('file_request', 'file_id', $_GET['sid']) as $files) {
        $study = $override->get('study_files', 'id', $files['file_id'])[0];
        $staff = $override->get('user', 'id', $files['requesting_staff_id'])[0];
        $approve = $override->get('user', 'id', $files['approve_staff'])[0];
        $return = $override->get('user', 'id', $files['return_staff'])[0];
        $receive = $override->get('user', 'id', $files['received_staff'])[0];

        $output .= '
            <table width="100%" border="1" cellpadding="5" cellspacing="0">
                <tr>
                    <td colspan="2" align="center" style="font-size: 18px">
                        <b>Dispense Order</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                    <table width="100%" cellpadding="5">
                        <tr>
                            <td width="65%">
                                To,<br />
                                    <b>RECEIVER </b><br />
                                    Name : ' . $study["name"] . '<br />
                                    Address : ' . $study["name"] . '<br />
                            </td>
                            <td width="35%">
                                Reverse Charge<br />
                                Order No : ' . $study["name"] . '<br />
                                Order Date : ' . $study["name"] . '<br />
                            </td>
                        </tr>
                    </table>
                    <br />
                    <table width="100%" border="1" cellpadding="5" cellspacing="0">
                    <tr>
                        <th rowspan="2">Sr No.</th>
                        <th rowspan="2">Product</th>
                        <th rowspan="2">Quantity</th>
                        <th rowspan="2">Unit</th>
                        <th rowspan="2">Actual Amt.</th>
                        <th rowspan="2">Total</th>
                    </tr>
                    <tr>

                    </tr>
               ';

        foreach ($staff as $row) {
            $count = $count + 1;

            $output .= '
                <tr>
                    <td>' . $count . '</td>
                    <td>' . $row["id"] . '</td>
                    <td>' . $row["id"] . '</td>
                    <td align="right">' . $row["id"] . '</td>
                    <td align="right">' . number_format($count, 2) . '</td>
                    <td align="right">' . number_format($count, 2) . '</td>
                </tr>
            ';
        }

        $output .= '
        <tr>
            <td align="right" colspan="4"><b>Total</b></td>
            <td align="right"><b>' . number_format($count, 2) . '</b></td>
            <td align="right"><b>' . number_format($count, 2) . '</b></td>
        </tr>
        ';

        $output .= '
            </table>
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <p align="right">---------------------------------------<br />Receiver Signature</p>
                <br />
                <br />
                <br />
            </td>
        </tr>
    </table>    
    ';
    }

    $file_name = 'Order-' . $row["name"] . '.pdf';
    $pdf->loadHtml($output);
    $pdf->render();
    $pdf->stream($file_name, array("Attachment" => false));
}
