<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
    <link rel='shortcut icon' href='/images/favicon.ico'/>
    <title>Child Protection Takedown</title>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
    <script src='/scripts/jquery-1.10.2.min.js'></script>
    <script src='/scripts/jquery.validate.min.js'></script>
    <script src='/scripts/moment.min.js'></script>
    <script src='/scripts/pikaday.js'></script>
    <script src='/scripts/pikaday.jquery.js'></script>
    <script src='/scripts/lca.js'></script>
    <script>
    $(document).ready(function(){

        //validate
        $("#ncmec-form1").validate();

        //initialize datepickers

        var $datepicker1 = $('#access-date').pikaday({
            firstDay: 1,
            minDate: new Date('2000-01-01'),
            maxDate: new Date('2020-12-31'),
            yearRange: [2000,2020]
        });

        var $datepicker2 = $('#incident-date').pikaday({
            firstDay: 1,
            minDate: new Date('2000-01-01'),
            maxDate: new Date('2020-12-31'),
            yearRange: [2000,2020]
        });

       //From http://www.alessioatzeni.com/blog/simple-tooltip-with-jquery-only-text/
       $('.showTooltip').hover(function(){
            // Hover over code
            var title = $(this).attr('title');
            $(this).data('tipText', title).removeAttr('title');
            $('<p class="tooltip"></p>')
            .text(title)
            .appendTo('body')
            .fadeIn('slow');
        }, function() {
            // Hover out code
            $(this).attr('title', $(this).data('tipText'));
            $('.tooltip').remove();
        }).mousemove(function(e) {
            var mousex = e.pageX + 20; //Get X coordinates
            var mousey = e.pageY + 10; //Get Y coordinates
            $('.tooltip')
            .css({ top: mousey, left: mousex })
        });

        //remove browser tooltip by removing title on hover
        $('.showTooltip[title]').mouseover(function () {
            $this = $(this);
            $this.data('title', $this.attr('title'));
            // Using null here wouldn't work in IE, but empty string will work just fine.
            $this.attr('title', '');
        }).mouseout(function () {
            $this = $(this);
            $this.attr('title', $this.data('title'));
        });

        var selectedH = <?php echo "'".gmdate( "H", time() )."'"?>;
        $("select#access-time-hour option").filter(function() {
            return $(this).val() == selectedH;
        }).prop('selected', true);

        var selectedi = <?php echo "'".gmdate( "i", time() )."'"?>;
        $("select#access-time-min option").filter(function() {
            return $(this).val() == selectedi;
        }).prop('selected', true);

        $('#legal-approved').change(function(){
            var val = $(this).val();

            if(val == 'Y') {
                $('#who-approved').attr("readonly", false);
                $('#why-not-approved').attr("readonly", true);
            } else {
                $('#why-not-approved').attr("readonly", false);
                $('#who-approved').attr("readonly", true);
            }
        });

        var filenamewrapper = $('#uploadfilenames');
        var uploadadder = $('#uploadmorefiles');
        var uploadnum = 1;

        $(uploadadder).click(function (e)
        {

            $(filenamewrapper).append('<div class=\'addition'+uploadnum+'\' > <img class=\'removefield\' src=\'/images/Emblem-multiply.svg\' width=\'20px\' title=\'remove field\'/>File:<input class=\'addition'+uploadnum+'\' id=\'file-name\' name=\'file-names[]\' value=\'\' type=\'text\' size=\'50\' required /><input name=\'takedown-files[]\' type=\'file\' required/></div>');
            uploadnum++;

        });

         $("body").on("click",".removefield", function(e) {

            var currentclass = '.'+$(this).parent('div').attr('class');
            $(currentclass).remove();

        });


    });

</script>
    <style type='text/css'>
    <!--/* <![CDATA[ */
    @import '/css/main.css';
    @import '/css/pikaday.css';
    @import '/css/lca.css';
    /* ]]> */-->
    .external, .external:visited { color: #222222; }
    .autocomment{color:gray}
    </style>
</head>
<body class='mediawiki'>
    <div id='globalWrapper'>
        <div id='column-content'>
            <div id='content'>
                <h1>Child Protection Takedown</h1>
                <table border='1' style='position:absolute; left:10spx; top:40px;'>
                        <tr>
                            <td>
                                Server/Connection Status
                            </td>
                            <td >
                                Production
                                <img id='ncmec-prod' src='/images/List-remove.svg' width='15px'/>
                            </td>
                            <td>
                                Test
                                <img id='ncmec-test' src='/images/List-remove.svg' width='15px'/>
                            </td>
                        </tr>
                </table>
                <br />
                <form method='post' action='NCMECprocessing.php' id='ncmec-form1' enctype='multipart/form-data'>
                    <fieldset>
                        <legend>Logging Data</legend>
                        <table border='1' id='mw-movepage-table'>
                            <tr>
                                <td>
                                    <label for='legal-approved'> Was this release to NCMEC Approved by the legal department? </label>
                                </td>
                                <td>
                                    <select id='legal-approved' name='legal-approved' required>
                                        <option value='Y'>Yes</option>
                                        <option value='N'>No</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='who-approved'>If Yes: Who in the legal department approved the release?</label>
                                </td>
                                <td>
                                    <input id ='who-approved' name='who-approved' type='text' size='30' value=''>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='why-not-approved'> If No: Why not? </label>
                                </td>
                                <td>
                                    <input id='why-not-approved' name='why-not-approved' type='text' size='50' value='' readonly='true' />
                                </td>
                            </tr>
                            <tr>
                                <td >
                                    <label for='logging-metadata'> Please check all statements which are true. </label>
                                </td>
                                <td>
                                    <input type='checkbox' name='logging-metadata[]' value='The content was taken down and suppressed.'> The content was taken down and suppressed. <br />
                                    <input type='checkbox' name='logging-metadata[]' value='An email was sent to legal@rt.wikimedia.org with the file name asking for it to be deleted.'> An email was sent to <a href='mailto:legal@rt.wikimedia.org' target='_blank'>legal@rt.wikimedia.org</a> with the file name asking for it to be deleted.<br />
                                    <input type='checkbox' name='logging-metadata[]' value='The user who uploaded the content has been locked.'> The user who uploaded the content has been locked. <br />
                                    <input type='checkbox' name='logging-metadata[]' value='Checkuser data was available and is being included below.'> Checkuser data was available and is being included below.
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='logging-details'> Is there any information that you would like to record in the permanent log? <br /> <b><u>Note:</u> Do not include Checkuser data you are not ok with being permanently kept </b></label>
                                </td>
                                <td>
                                    <textarea id ='logging-details' name='logging-details' wrap='virtual' rows='4' ></textarea>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <fieldset>
                        <legend> Information about the reporter </legend>
                        <table border='0' id='mw-movepage-table'>
                            <tr>
                                <td>
                                    <label for='is-test'> Is this a test? </label>
                                </td>
                                <td>
                                    <select name='is-test' id='is-test'>
                                        <option value='N' selected>No</option>
                                        <option value='Y'>Yes</option>
                                    </select> <img class='showTooltip' src='/images/20px-Help.png' title='Select Yes if this is a test of the processing system. Remember to select No for sending to NCMEC. '/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='access-date'>When did you you access the content? (UTC)</label>
                                </td>
                                <td>
                                    <input id='access-date' name='access-date' value='' type='text' size='15' required/>
                                    <select id='access-time-hour' name='access-time-hour' required>
                                        <option value='01'>01</option>
                                        <option value='02'>02</option>
                                        <option value='03'>03</option>
                                        <option value='04'>04</option>
                                        <option value='05'>05</option>
                                        <option value='06'>06</option>
                                        <option value='07'>07</option>
                                        <option value='08'>08</option>
                                        <option value='09'>09</option>
                                        <option value='10'>10</option>
                                        <option value='11'>11</option>
                                        <option value='12'>12</option>
                                        <option value='13'>13</option>
                                        <option value='14'>14</option>
                                        <option value='15'>15</option>
                                        <option value='16'>16</option>
                                        <option value='17'>17</option>
                                        <option value='18'>18</option>
                                        <option value='19'>19</option>
                                        <option value='20'>20</option>
                                        <option value='21'>21</option>
                                        <option value='22'>24</option>
                                        <option value='23'>23</option>
                                    </select>
                                    <select id='access-time-min' name='access-time-min' required>
                                        <option value='01'>01</option>
                                        <option value='02'>02</option>
                                        <option value='03'>03</option>
                                        <option value='04'>04</option>
                                        <option value='05'>05</option>
                                        <option value='06'>06</option>
                                        <option value='07'>07</option>
                                        <option value='08'>08</option>
                                        <option value='09'>09</option>
                                        <option value='10'>10</option>
                                        <option value='11'>11</option>
                                        <option value='12'>12</option>
                                        <option value='13'>13</option>
                                        <option value='14'>14</option>
                                        <option value='15'>15</option>
                                        <option value='16'>16</option>
                                        <option value='17'>17</option>
                                        <option value='18'>18</option>
                                        <option value='19'>19</option>
                                        <option value='20'>20</option>
                                        <option value='21'>21</option>
                                        <option value='22'>24</option>
                                        <option value='23'>23</option>
                                        <option value='24'>24</option>
                                        <option value='25'>25</option>
                                        <option value='26'>26</option>
                                        <option value='27'>27</option>
                                        <option value='28'>28</option>
                                        <option value='29'>29</option>
                                        <option value='30'>30</option>
                                        <option value='31'>31</option>
                                        <option value='32'>32</option>
                                        <option value='33'>33</option>
                                        <option value='34'>34</option>
                                        <option value='35'>35</option>
                                        <option value='36'>36</option>
                                        <option value='37'>37</option>
                                        <option value='38'>38</option>
                                        <option value='39'>39</option>
                                        <option value='40'>40</option>
                                        <option value='41'>41</option>
                                        <option value='42'>42</option>
                                        <option value='43'>43</option>
                                        <option value='44'>44</option>
                                        <option value='45'>45</option>
                                        <option value='46'>46</option>
                                        <option value='47'>47</option>
                                        <option value='48'>48</option>
                                        <option value='49'>49</option>
                                        <option value='50'>50</option>
                                        <option value='51'>51</option>
                                        <option value='52'>52</option>
                                        <option value='53'>53</option>
                                        <option value='54'>54</option>
                                        <option value='55'>55</option>
                                        <option value='56'>56</option>
                                        <option value='57'>57</option>
                                        <option value='58'>58</option>
                                        <option value='59'>59</option>
                                    </select>
                                    <img class='showTooltip' src='/images/20px-Help.png' title='All date/times UTC. Please use date selector or format as YYYY-MM-DD for first box. This should be the time and date that YOU (the reporter) accessed the file.
                                    It is preset to the report time as a rough guess given WMF standard process however if you are filling this out much later you should adjust it.' />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='reporter-fName'> Your name: </label>
                                </td>
                                <td>
                                    <input id='reporter-fName' name='reporter-fName' type='text' size='25' value='' required/> <input id='reporter-lName' name='reporter-lName' type='text' size='25' value='' />  <img class='showTooltip' src='/images/20px-Help.png' title='This is YOUR name as the reporter.' required/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for'reporter-email'> Your email: </label>
                                </td>
                                <td>
                                    <input id='reporter-email' name='reporter-email' type='text' size='50' value=<?php echo "'".$_SERVER['PHP_AUTH_USER']."@wikimedia.org'";?> required email/> <img class='showTooltip' src='/images/20px-Help.png' title='YOUR email as the reporter, defaults to username logged in.' />
                                </td>
                            </tr>
                        <!--<tr>
                                <td>
                                    <label for='reporter-phone'> Phone number (xxx-xxx-xxx) <br /> and extension (xxxx) if necessary: </label>
                                </td>
                                <td>
                                    <input id='reporter-phone' name='reporter-phone' type='text' size='20' value='415-839-6885' /> <input id='reporter-phone-ext' name='reporter-phone-ext' type='text' size='5' /> <img class='showTooltip' src='/images/20px-Help.png' title='Phone number for followup, defaults to office with no extension' />
                                </td>
                            </tr> REMOVED FOR NOW BECAUSE OF CONTACT INFO BEING SENT ELSEWHERE-->
                        </table>
                    </fieldset>
                    <fieldset>
                        <legend> Incident Information</legend>
                        <table border='0' id='mw-movepage-table'>
                            <tr>
                                <td>
                                    <label for='file-name'> File name (without File:) </label>
                                </td>
                                <td>
                                    <div id='uploadfilenames'>
                                    <div><img id='uploadmorefiles' src='/images/List-add.svg' width='20px' title='add a user field'/>File:<input id='file-name' name='file-names[]' value='' type='text' size='50' required /><input name='takedown-files[]' type='file' required/></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='project'> Project where file was uploaded </label>
                                </td>
                                <td>
                                    <select id='project' name='project' required>
                                        <option value='Wikimedia Commons' selected>Wikimedia Commons </option>
                                        <option value='Wikipedia'> Wikipedia </option>
                                        <option value='Wikidata'> Wikidata </option>
                                        <option value='Wikibooks'> Wikibooks </option>
                                        <option value='Meta'> Meta </option>
                                        <option value='Wikiquote'> Wikiquote </option>
                                        <option value='Wikispecies'> Wikispecies </option>
                                        <option value='Wikivoyage'> Wikivoyage </option>
                                        <option value='Wiktionary'> Wiktionary </option>
                                        <option value='Wikisource'> Wikisource </option>
                                        <option value='Wikiversity'> Wikiversity</option>
                                    </select> <img class='showTooltip' src='/images/20px-Help.png' title='The project where the image was uploaded (usually Commons). [note: Non Commons sites not YET implemented]' />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='project-language'>Project language code (if necessary)</label>
                                </td>
                                <td>
                                    <input type='text' size='10' value='' name='project-language' id='project-language' /><img class='showTooltip' src='/images/20px-Help.png' title='For projects which require a language code, type it here (not for commons/wikidata/meta) etc.' />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='incident-date'>Date and time of upload: </label>
                                </td>
                                <td>
                                    <input id='incident-date' name='incident-date' value='' type='text' size='15' required/>
                                    <select id='incident-time-hour' name='incident-time-hour' required>
                                        <option value='01'>01</option>
                                        <option value='02'>02</option>
                                        <option value='03'>03</option>
                                        <option value='04'>04</option>
                                        <option value='05'>05</option>
                                        <option value='06'>06</option>
                                        <option value='07'>07</option>
                                        <option value='08'>08</option>
                                        <option value='09'>09</option>
                                        <option value='10'>10</option>
                                        <option value='11'>11</option>
                                        <option value='12'>12</option>
                                        <option value='13'>13</option>
                                        <option value='14'>14</option>
                                        <option value='15'>15</option>
                                        <option value='16'>16</option>
                                        <option value='17'>17</option>
                                        <option value='18'>18</option>
                                        <option value='19'>19</option>
                                        <option value='20'>20</option>
                                        <option value='21'>21</option>
                                        <option value='22'>24</option>
                                        <option value='23'>23</option>
                                    </select>
                                    <select id='incident-time-min' name='incident-time-min' required>
                                        <option value='01'>01</option>
                                        <option value='02'>02</option>
                                        <option value='03'>03</option>
                                        <option value='04'>04</option>
                                        <option value='05'>05</option>
                                        <option value='06'>06</option>
                                        <option value='07'>07</option>
                                        <option value='08'>08</option>
                                        <option value='09'>09</option>
                                        <option value='10'>10</option>
                                        <option value='11'>11</option>
                                        <option value='12'>12</option>
                                        <option value='13'>13</option>
                                        <option value='14'>14</option>
                                        <option value='15'>15</option>
                                        <option value='16'>16</option>
                                        <option value='17'>17</option>
                                        <option value='18'>18</option>
                                        <option value='19'>19</option>
                                        <option value='20'>20</option>
                                        <option value='21'>21</option>
                                        <option value='22'>24</option>
                                        <option value='23'>23</option>
                                        <option value='24'>24</option>
                                        <option value='25'>25</option>
                                        <option value='26'>26</option>
                                        <option value='27'>27</option>
                                        <option value='28'>28</option>
                                        <option value='29'>29</option>
                                        <option value='30'>30</option>
                                        <option value='31'>31</option>
                                        <option value='32'>32</option>
                                        <option value='33'>33</option>
                                        <option value='34'>34</option>
                                        <option value='35'>35</option>
                                        <option value='36'>36</option>
                                        <option value='37'>37</option>
                                        <option value='38'>38</option>
                                        <option value='39'>39</option>
                                        <option value='40'>40</option>
                                        <option value='41'>41</option>
                                        <option value='42'>42</option>
                                        <option value='43'>43</option>
                                        <option value='44'>44</option>
                                        <option value='45'>45</option>
                                        <option value='46'>46</option>
                                        <option value='47'>47</option>
                                        <option value='48'>48</option>
                                        <option value='49'>49</option>
                                        <option value='50'>50</option>
                                        <option value='51'>51</option>
                                        <option value='52'>52</option>
                                        <option value='53'>53</option>
                                        <option value='54'>54</option>
                                        <option value='55'>55</option>
                                        <option value='56'>56</option>
                                        <option value='57'>57</option>
                                        <option value='58'>58</option>
                                        <option value='59'>59</option>
                                    </select>
                                    <img class='showTooltip' src='/images/20px-Help.png' title='Date and time of the image upload, please use the date picker or the format YYYY-MM-DD' />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='incident-location'> Incident location: </label>
                                </td>
                                <td>
                                    <select name='incident-location' id='incident-location'>
                                        <option value='webPageIncident' selected>Web Page </option>
                                    </select> <img class='showTooltip' src='/images/20px-Help.png' title='Where the incident happened (web page/IRC/email etc) currently only supporting web page.' />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='uploader-username'> Uploaded by: </label>
                                </td>
                                <td>
                                    User:<input type='text' size='50' name='uploader-username' id='uploader-username' required/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='uploader-ip'> IP used for upload: </label>
                                </td>
                                <td>
                                    <input type='text' size='25' name='uploader-ip' id='uploader-ip'/>
                            <tr>
                                <td>
                                    <label for='uploader-email'> Email of uploader: </label>
                                </td>
                                <td>
                                    <input type='text' size='50' name='uploader-email' id='uploader-email' /> <img class='showTooltip' src='/images/20px-Help.png' title='This may be available in the database, James or a developer with full DB access can check.'/>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <fieldset>
                        <legend> Additional Information (CU data, other info we may have etc) </legend>
                        <textarea name='comments' wrap='virtual' rows='18' cols='70'></textarea>
                        <input type='submit' value='Process takedown and send to NCMEC'>
                    </fieldset>
                </form>
            </div>
        </div>
        <?php include dirname( __FILE__ ) . '/../include/lcapage.php'; ?>
    </div>
    <?php
flush();
require_once dirname( __FILE__ ) . '/../include/multiuseFunctions.php';
$config = parse_ini_file( dirname( __FILE__ ) . '/../lcaToolsConfig.ini' );
$NCMEC_URL_Production = $config['NCMEC_URL_Production'].'status';
$NCMEC_URL_Test = $config['NCMEC_URL_Test'].'status';
$produsername = $config['NCMEC_user_prod'];
$prodpassword = $config['NCMEC_password_prod'];
$testusername = $config['NCMEC_user_test'];
$testpassword = $config['NCMEC_password_test'];
$prodresult = NCMECstatus( $produsername, $prodpassword, $NCMEC_URL_Production );
if ( $prodresult === '0' ) {
    echo "<script> $('#ncmec-prod').attr('src', '/images/Dialog-accept.svg');</script>".PHP_EOL;
} else {
    echo "<script> $('#ncmec-prod').attr('src', '/images/Dialog-error-round.svg'); </script>".PHP_EOL;
}
flush();
$testresult = NCMECstatus( $testusername, $testpassword, $NCMEC_URL_Test );
if ( $testresult === '0' ) {
    echo "<script> $('#ncmec-test').attr('src', '/images/Dialog-accept.svg');</script>".PHP_EOL;
} else {
    echo "<script> $('#ncmec-test').attr('src', '/images/Dialog-error-round.svg'); </script>".PHP_EOL;
}
flush();
?>
</body>
</html>
