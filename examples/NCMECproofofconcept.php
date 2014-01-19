<?php
$uploaderusername = 'Bad User';
$project = 'Wikimedia Commons';
$incfilename = 'badimage.pdf';
$legalapproved = 'Yes';
$whoapproved = 'Luis';
$whynotapproved = null;
$logdata = array ('Test option 1', 'Test option 2', 'A bit longer test option selected by checkbox on the form', 'what is a checkbox?');
$istest = 'Y';
$reportID = null;
$hash = null;
$details = 'This is a showcase of what the NCMEC submission page could look like with a more dynamic/usable interface
This is a 2nd line
And this is a 3rd...............a bit longer then the 2nd';
?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
    <base href='..'>
	<link rel='shortcut icon' href='images/favicon.ico'/>
	<title>NCMEC Submission</title>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	<script src='scripts/jquery-1.10.2.min.js'></script>
	<script src='scripts/jquery.validate.min.js'></script>
	<script src='scripts/lca.js'></script>
	<script>
	$(document).ready(function(){

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



});
    
</script>
	<style type='text/css'>
	<!--/* <![CDATA[ */
	@import 'css/main.css'; 
	@import 'css/pikaday.css';
	@import 'css/lca.css';
	/* ]]> */-->
	.external, .external:visited { color: #222222; }
	.autocomment{color:gray}
	</style>
</head>
<body class='mediawiki'>
	<div id='globalWrapper'>
		<div id='column-content'>
			<div id='content'>
				<h1> Legal Takedown </h1>
				<br />
                <fieldset>
                    <legend>Submission processing</legend>
                    <table border='1' id='mw-movepage-table' style='font-weight:bold;'> 
                        <tr>
                            <td >
                                <u>Step 1:</u> Data gathered and put together:
                            </td>
                            <td >
                                <img id='gathered' src='images/List-remove.svg' width='40px'/>
                            </td>
                            <td >
                                <u>Step 4:</u> File information sent:
                            </td>
                            <td >
                                <img id='file-info' src='images/List-remove.svg' width='40px'/>
                            </td>
                        </tr>
                        <tr>
                            <td >
                                <u>Step 2:</u> Report opened with NCMEC:
                            </td>
                            <td>
                                <img id='opened' src='images/List-remove.svg' width='40px'/>
                            </td>
                            <td >
                                <u>Step 5:</u> Report closed:
                            </td>
                            <td>
                                <img id='closed' src='images/List-remove.svg' width='40px'/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <u>Step 3:</u> File sent:
                            </td>
                            <td>
                                <img id='file-sent' src='images/List-remove.svg' width='40px'/>
                            </td>
                             <td >
                                <u>Step 6:</u> Log created and data stored:
                            </td>
                            <td>
                                <img id='logged' src='images/List-remove.svg' width='40px'/>
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <fieldset>
        <legend> Logged info </legend>
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
                    <div id='hash'></div>
                </td>
            </tr>
            <tr>
                <td>
                    <label for='report-id'>NCMEC Report ID# </label>
                </td>
                <td>
                    <div id='report-id'></div>
                </td>
            </tr>
            <tr>
                <td >
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
    
    <fieldset>
        <legend>Is there any information that you would like to record in the permenant log? </legend>
        <textarea name='details' wrap='virtual' rows='18' cols='70' readonly><?php if(!empty($details)) {
            echo $details;
        }  else { echo 'This option was not set'; } ?></textarea>
    </fieldset>
    </fieldset>
    <fieldset>
        <legend>Debugging information</legend>
        <table border='1' id='mw-movepage-table' style='font-weight:bold;'> 
            <tr>
                <td>
                    <label for='xml-report'>XML send to NCMEC</label>
                </td>
                 <td>
                    <textarea id='xml-report'></textarea>
                </td>
            </tr>
            <tr>
                <td>
                    <label for='xml-report-val'>Validation info for main report</label>
                </td>
                <td>
                    <textarea id='xml-report-val'></textarea>
                </td>
            </tr>
        </table>

     </fieldset>

                </div>
        </div>
            <?php include('../include/lcapage.php'); ?>
        </div>
        <?php
        sleep(1);
        echo '.....';
        echo "<script> $('img#gathered').attr('src', 'images/Dialog-accept.svg'); </script>";
        flush();
        echo '.....';
        sleep(2);
        echo "<script> $('img#opened').attr('src', 'images/Dialog-accept.svg');  
        $('div#report-id').text('12345');</script>";
        flush();
        echo '.....';
        sleep(2);
        echo "<script> $('img#file-sent').attr('src', 'images/Dialog-accept.svg'); 
        $('div#hash').text('IIZHASHHAHAHEHEHE');</script>";
        flush();
        echo '.....';
        sleep(1);
        echo "<script> ($('img#file-info').attr('src', 'images/Dialog-accept.svg')); </script>";
        flush();
        echo '.....';
        sleep(1);
        echo "<script> ($('img#closed').attr('src', 'images/Dialog-accept.svg')); </script>";
        flush();
        echo '.....';
        sleep(1);
        echo "<script> ($('img#logged').attr('src', 'images/Dialog-accept.svg')); </script>";
        flush();
        echo '.....';
        ?>
    </body>
</html>



