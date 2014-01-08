<p> See below for the NCMEC information that was submitted. <?php if ($istest === 'Y') { echo '<b> NOTE: This was marked as a test submission </b>'; } ?></p>
<p> This data was submitted by <?php echo $user; ?> with a timestamp of <?php echo $submittime; ?> UTC </p>
    <fieldset>
        <legend> Release info </legend>
         <table border='1' id='mw-movepage-table'> 
            <tr>
                <td>
                    <label for='legal-approved'> Was this release to NCMEC Approved by the legal department? </label>
                </td>
                <td>
                    <?php
                    if (!empty($legalapproved)) {
                        echo htmlspecialchars($legalapproved);
                    } else { echo 'this field does not appear to have been set';} ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for='who-approved'>If Yes: Who in the legal department approved the release?</label>
                </td>
                <td> 
                    <?php
                    if ($whoapproved) {
                        echo htmlspecialchars($whoapproved);
                    } else { echo '';} ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for='why-not-approved'> If No: Why not? </label>
                </td>
                <td>
                    <?php
                    if ($whynotapproved) {
                        echo htmlspecialchars($whynotapproved);
                    } else { echo '';} ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for='user-involved'> Which user uploaded the image(s) involved? </label>
                </td>
                <td>
                    <?php
                    if ($uploaderusername) {
                        echo htmlspecialchars($uploaderusername);
                    } else { echo 'This option was not set'; } ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for='project-involved'> Which project did the incident occur on?</label>
                </td>
                <td>
                    <?php
                    if ($project) {
                        echo htmlspecialchars($project);
                    } else { echo 'This option was not set'; } ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for='file-involved'> Which was the name of the file involved? </label>
                </td>
                <td>
                    <?php
                    if ($incfilename) {
                        echo htmlspecialchars($incfilename);
                    } else { echo 'This option was not set'; } ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for='hash'> File Hash: </label>
                </td>
                <td>
                    <?php
                    if ($hash) {
                        echo $hash;
                    } else { echo 'There does not appear to have been a hash stored'; } ?>
                </td>
            </tr>
            <tr>
                <td style='vertical-align:middle'>
                    <label for='logging-metadata'> Please check all statements which are true </label>
                </td>
                <td>
                    <?php if(!empty($logdata)) {
                        foreach ($logdata as $value) {
                            echo htmlspecialchars($value) . '<br />';
                        }
                    } else { echo 'This option was not set'; } ?>
                </td>
            </tr>
        </table>
    </fieldset>
    <fieldset>
        <legend>Is there any information that you would like to record in the permenant log? </legend>
        <textarea name='details' wrap='virtual' rows='18' cols='70' readonly> <?php if(!empty($details)) {
            echo $details;
        }  else { echo 'This option was not set'; } ?></textarea>
    </fieldset>