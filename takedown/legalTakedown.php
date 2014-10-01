<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='/images/favicon.ico'/>
	<title>Legal Takedowns</title>
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
	    $("#takedown-form1").validate();

		//initialize datepicker
	   var $datepicker = $('#takedown-date').pikaday({
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

	    $('select#project').change( function() {
	    	if ( $('select#project option:selected').val() == 'commons' ) {
	    		$('.commonsonly').show();
	    	} else {
	    		$('.commonsonly').hide();
	    	}
	    	if ( $('select#project option:selected').val() == 'enwiki' ) {
	    		$('.enwikionly').show();
	    	} else {
	    		$('.enwikionly').hide();
	    	}
	    })

	    $('select#content-type').change( function() {
	    	if ( $('select#content-type option:selected').val() == ( 'file' ) || $('select#content-type option:selected').val() == ( 'both' ) ) {
	    		$('.fileonly').show();
	    	} else {
	    		$('.fileonly').hide();
	    	}
	    	if ( $('select#content-type option:selected').val() == ( 'text' ) || $('select#content-type option:selected').val() == ( 'both' ) ) {
	    		$('.textonly').show();
	    	} else {
	    		$('.textonly').hide();
	    	}
	    })


	    var filetakedownwrapper = $("#takedownfiles");
	    var userwrapper = $("#takedownusers");
	    var filetakedownadder = $("#takedownmorefiles");
	    var useradder = $("#notifymoreusers");
	    var uploadewrapper = $('#uploadfiles');
	    var uploadadder = $('#uploadmorefiles');

	    $(filetakedownadder).click(function (e)
	    {

	    	$(filetakedownwrapper).append('<div><input class=\'files-affected\' name=\'files-affected[]\' value=\'\' type=\'text\' size=\'50\'/><img class=\'removefield\' src=\'/images/Emblem-multiply.svg\' width=\'20px\' title=\'remove field\'/></div>');

	    });

	    $(useradder).click(function (e)
	    {

	    	$(userwrapper).append('<div><input class=\'involved-user\' name=\'involved-user[]\' value=\'\' type=\'text\' size=\'15\'/><img class=\'removefield\' src=\'/images/Emblem-multiply.svg\' width=\'20px\' title=\'remove field\'/></div>');

	    });

	    $(uploadadder).click(function (e)
	    {

	    	$(uploadewrapper).append('<div>Supporting file (scanned takedown etc) <input name=\'takedown-files[]\' type=\'file\' /><img class=\'removefield\' src=\'/images/Emblem-multiply.svg\' width=\'20px\' title=\'remove field\'/></div>');

	    });

	    $("body").on("click",".removefield", function(e) {

	    	$(this).parent('div').remove();

	    });

	    $('#takedown-commons-title').keyup(function(e) {
	    	var chr = $(this).val();
	    	$('#takedown-wmf-title').val('DMCA ' + chr);
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
				<h1> Legal Takedown </h1>
				<a href="/takedown/CEonlyTakedown.php"> [ Switch to form that does not post on wiki ] </a> <img class='showTooltip' src='/images/20px-Help.png' title='The form you are on is for logging notices which are being posted on wiki (this is the "normal" way). However you can switch forms if you do not want to post the takedown, for example if you are updating an older log entry or notices that we did not act on.'/>
				<br />
				<form method='post' action='legalTakedownProcessor.php' id='takedown-form1' enctype='multipart/form-data'>
					<fieldset>
						<legend> Processing and Logging information </legend>
						<table border='0' id='mw-movepage-table'>
							<tr class='spaceOut'>
								<td>
									<label for='ce-send'> Send to Chilling Effects?</label>
									<select name='ce-send' id='ce-send'>
										<option>No</option>
										<option>Yes</option>
									</select> <img class='showTooltip' src='/images/20px-Help.png' title='Select Yes to send this report to Chilling Effects, No to internally process only and not send.'/>
								</td>
								<td>
									<label for='is-test'> Is this a test? </label>
									<select name='is-test' id='is-test'>
										<option>No</option>
										<option>Yes</option>
									</select> <img class='showTooltip' src='/images/20px-Help.png' title='Select Yes if this is a test of the processing system. Remember to select No for sending to Chilling Effects. '/>
								</td>
							</tr>
							<tr class='spaceOut'>
								<td>
									<label for='involved-user'> Username who added the content: </label>
								</td>
								<td>
								<div id="takedownusers">
									<span>One name per line, press + to add x to remove. </span> <br />
									<div><input id='involved-user' name='involved-user[]' value='' type='text' size='15' required/><img id='notifymoreusers' src='/images/List-add.svg' width='20px' title='add a user field'/></div>
								</div>
								</td>
							</tr>
							<tr>
								<td>
									<label for='project'> Project where takedown is being implemented: </label>
								</td>
								<td>
									<select id='project' name='project'>
										<option value='commons' selected>Wikimedia Commons</option>
										<option value='enwiki'>English Wikipedia</option>
									</select>
								</td>
							</tr>
							<tr>
								<td>
									<label for='content-type'> What type of content is being taken down? </label>
								</td>
								<td>
									<select id='content-type' name='content-type'>
										<option value='file' selected>File/Image </option>
										<option value='text'> Text </option>
										<option value='both'> Both </option>
									</select>
								</td>
							</tr>
							<tr style='outline: solid black thin;'>
								<td>
									<label for='logging-metadata'> Place a checkmark by all items which are true. </label>
								</td>
								<td>
									<input type='checkbox' name='logging-metadata[]' value='The content was taken down and the user was clearly warned and discouraged from future violations.' /> The content was taken down and the user will be clearly warned and discouraged from future violations.  <br />
									<input type='checkbox' name='logging-metadata[]' value='The content was taken down and we have actual knowledge that the content was infringing copyright ' /> The content was taken down and we have actual knowledge that the content was infringing copyright.  <br />
									<input type='checkbox' name='logging-metadata[]' value='The content was taken down and we have awareness of facts or circumstances from which infringing activity is apparent. ' /> The content was taken down and we have awareness of facts or circumstances from which infringing activity is apparent. <br />
									<input type='checkbox' name='logging-metadata[]' value='The content was taken down pursuant to a DMCA notice.' /> The content was taken down pursuant to a DMCA notice. <br />
								</td>
							</tr>
							<tr style='outline: solid black thin;'>
								<td>
									<label for='strike-note'> The takedown does NOT count as a "strike" for purposes of the repeat infinger policy because: </label>
								</td>
								<td>
									<input type='checkbox' name='strike-note[]' value='The user has filed a successful counter-notification.' /> The user has filed a successful counter-notification.  <br />
									<input type='checkbox' name='strike-note[]' value='The Office of General Counsel has decided that a "strike" is not appropriate because of mitigating circumstances (e.g., the user demonstrates a clear lack of willfulness and a mistaken belief of compliance).' /> The Office of General Counsel has decided that a "strike" is not appropriate because of mitigating circumstances (e.g., the user demonstrates a clear lack of willfulness and a mistaken belief of compliance).  <br />
									<input type='checkbox' name='strike-note[]' value='other' /> Other: <input type='text' id='strike-note-other' name='strike-note-other' size='50' />
								</td>
							</tr>
						</table>

					</fieldset>
					<fieldset>
						<legend>Who sent the takedown?</legend>
						<table border='0' id='mw-movepage-table'>
							<tr>
								<td class='lca-label'>
									<label for='sender-name'>Sender (person or organization)</label>
								</td>
								<td class='lca-input'>
									<input id='sender-name' name='sender-name' value='' type='text' size='50' />
								</td>
							</tr>
							<tr>
								<td class='lca-label'>
									<label for='sender-person'>Attorney or individual signing</label>
								</td>
								<td class='lca-input'>
									<input id='sender-person' name='sender-person' value='' type='text' size='50' />
								</td>
							</tr>
							<tr>
								<td class='lca-label'>
									<label for='sender-firm'>Law Firm or Agent (if any)</label>
								</td>
								<td class='lca-input'>
									<input id='sender-firm' name='sender-firm' value='' type='text' size='50' />
								</td>
							</tr>
							<tr>
								<td class='lca-label'>
									<label for='sender-address1'>Sender Address </label>
								</td>
								<td class='lca-input'>
									<input id='sender-address1' name='sender-address1' value='' type='text' size='50' />
								</td>
							</tr>
							<tr>
								<td class='lca-label'>
									<label for='sender-address2'>Sender Address, line 2</label>
								</td>
								<td class='lca-input'>
									<input id='sender-address2' name='sender-address2' value='' type='text' size='50' />
								</td>
							</tr>
							<tr>
								<td class='lca-label'> Sender City, State, Zip </td>
								<td>
									<input id='sender-city' name='sender-city' value='' type='text' size='25' />, <input id='sender-state' name='sender-state' value='' type='text' size='10' />, <input id='sender-zip' name='sender-zip' value='' type='text' size='10' />
								</td>
							</tr>
							<tr>
								<td class='lca-label'>
									<label for='sender-country'>Country</label>
								</td>
								<td class='lca-input'>
									<select id='sender-country' name='sender-country' value=''>
									<?php include dirname( __FILE__ ) . '/../include/countrySelect.php'; ?>
									</select>
								</td>
							</tr>
						</table>
					</fieldset>
					<fieldset>
						<legend>Takedown meta data</legend>
						<input type='hidden' name='ce-report-type' id ='ce-report-type' value='dmca' />
						<table border='0' id='mw-movepage-table'>
							<tr>
								<td class='lca-label'>
									<label for='takedown-date'> Date the takedown was sent </label>
								</td>
								<td>
									<input id='takedown-date' name='takedown-date' value='' type='text' size='25' required/> <img class='showTooltip' src='/images/20px-Help.png' title='Please use date selector or format as YYYY-MM-DD' />
									<label for='action-taken'> Action taken? </label>
									<select name='action-taken' id='action-taken'>
									<option>Yes</option>
									<option>No</option>
									<option>Partial</option>
									</select>
								</td>
							</tr>
							<tr class='fileonly'>
								<td>
									<label for='files-affected'> File(s) affected </label>
								</td>
								<td>
								<div id="takedownfiles">
									<span>One file name per line (no file:), press + to add x to remove. </span> <br />
									<div><input id='files-affected' name='files-affected[]' value='' type='text' size='50' required/> <img id='takedownmorefiles' src='/images/List-add.svg' width='20px' title='add a file field'/></div>
								</div>
								</td>
							</tr>
							<tr class='textonly' style='display:none;'>
								<td>
									<label for='pages-affected'> Page(s) affected </label>
								</td>
								<td>
									<input id='pages-affected' name='pages-affected' value='' type='text' size='50' required/> <img class='showTooltip' src='/images/20px-Help.png' title='Pages affected by takedown, seperated by commas. Will take up to 5 then ignore the rest.'/>
									<br /> <span style='font-size:0.7em; color:red;'>Pleaes list page names (seperated by comma) including any prefixes . </span>
								</td>
							</tr>
							<tr>
								<td class='lca-label'>
									<label for='takedown-title'> Chilling Effects 'Title' </label>
								</td>
								<td>
									<input id='takedown-title' name='takedown-title' type='text' value='DMCA (Copyright) Complaint to Wikimedia Foundation' size='50'/> <img class='showTooltip' src='/images/20px-Help.png' title='Feel free to override the default with a witty title for Chilling Effects if it suits you.'/>
								</td>
							</tr>
							<tr class='commonsonly'>
								<td class='lca-label'>
									<label for='takedown-commons-title'>Commons Title</label>
								</td>
								<td>
									<input id='takedown-commons-title' name='takedown-commons-title' type='text' value='' size='50' required/> <img class='showTooltip' src='/images/20px-Help.png' title='The title to use for the Commons announcement.'/>
								</td>
							</tr>
							<tr class='enwikionly' style="display:none;">
								<td class='lca-label'>
									<label for='takedown-enwiki-title'>Takedown Title</label>
								</td>
								<td>
									<input id='takedown-enwiki-title' name='takedown-enwiki-title' type='text' value='' size='50' required/> <img class='showTooltip' src='/images/20px-Help.png' title='The title to use for general announcements, typically the name of the painting or artist or company involved.'/>
								</td>
							</tr>
							<tr>
								<td class='lca-label'>
									<label for='takedown-wmf-title'>WMFwiki Title</label>
								</td>
								<td>
									<input id='takedown-wmf-title' name='takedown-wmf-title' type='text' value='DMCA ' size='50' required/> <img class='showTooltip' src='/images/20px-Help.png' title='The title to use for the WMF wiki posting. (Don&#39;t worry; I&#39;ll give you a link on the next page.)'/>
								</td>
							</tr>
							<tr>
								<td class='lca-label'>
									<label for='takedown-method'>How was the C&amp;D sent?</label>
								</td>
								<td>
									<input id='takedown-method' name='takedown-method' value='email' type='text' size='25' /> <img class='showTooltip' src='/images/20px-Help.png' title='(e.g. email, postal mail, fax ...)'/>
								</td>
							</tr>
							<tr>
								<td class='lca-label'>
									<label for='takedown-subject'> Subject Line </label>
								</td>
								<td>
									<input id='takedown-subject' name='takedown-subject' value='' type='text' size='50' /> <img class='showTooltip' src='/images/20px-Help.png' title='Subject line of the email or fax received.'/>
								</td>
							</tr>
						</table>
					</fieldset>
					<fieldset>
						<legend> The Takedown </legend>
						<p> Takedown text - copy and paste email etc. </p>
						<textarea name='takedown-body' wrap='virtual' rows='18' cols='70'></textarea>
						<input type='hidden' name='MAX_FILE_SIZE' value='52428800' />
						<div id='uploadfiles'>
							<div>Supporting file (scanned takedown etc) <input name='takedown-files[]' type='file' /><img id='uploadmorefiles' src='/images/List-add.svg' width='20px' title='add a file field'/></div>
						</div>
						
					</fieldset>
					<input type='submit' value='Process takedown'>
				</form>
			</div>
		</div>
			<?php include dirname( __FILE__ ) . '/../include/lcapage.php'; ?>
		</div>
	</body>
</html>
