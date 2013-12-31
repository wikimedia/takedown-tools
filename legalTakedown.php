<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='images/favicon.ico'/>
	<title>DMCA Takedowns</title>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	<script src='scripts/jquery-1.10.2.min.js'></script>
	<script src='scripts/moment.min.js'></script>
	<script src='scripts/pikaday.js'></script>
	<script src='scripts/pikaday.jquery.js'></script>
	<script src='scripts/lca.js'></script>
	<script>
	$(document).ready(function(){

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

});
    
</script>
	<style type='text/css'>
	<!--/* <![CDATA[ */
	@import 'css/main.css'; 
	@import 'css/pikaday.css';
	@import 'css/lca.css';
	/* ]]> */-->
	td { vertical-align: top; }
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
				<form method='post' action='legalTakedownProcessor.php' id='takedown-form1' enctype='multipart/form-data'>
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
									<input id='sender-country' name='sender-country' value='' type='text' size='10' />
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
									<input id='takedown-date' name='takedown-date' value='' type='text' size='25' /> <img class='showTooltip' src='images/20px-Help.png' title='Please use date selector or format as YYYY-MM-DD' />
									<label for='action-taken'> Action taken? </label>
									<select name='action-taken' id='action-taken'>
									<option>Yes</option>
									<option>No</option>
									<option>Partial</option>
									</select>
								</td>
							</tr>
							<tr>
								<td>
									<label for='files-affected'> Files affected </label>
								</td>
								<td>
									<input id='files-affected' name='files-affected' value='' type='text' size='50' /> <img class='showTooltip' src='images/20px-Help.png' title='Files affected by takedown, no File: prefix and seperated by commas. Will take up to 5 then ignore the rest.'/>
								</td>
							</tr>
							<tr>
								<td class='lca-label'>
									<label for='takedown-title'> Chilling Effects 'Title' </label>
								</td>
								<td>
									<input id='takedown-title' name='takedown-title' type='text' value='DMCA (Copyright) Complaint to Wikimedia Foundation' size='50'/> <img class='showTooltip' src='images/20px-Help.png' title='Feel free to override the default with a witty title for Chilling Effects if it suits you.'/>
								</td>
							</tr>
							<tr>
								<td class='lca-label'>
									<label for='takedown-commons-title'>Commons Title</label>
								</td>
								<td>
									<input id='takedown-commons-title' name='takedown-commons-title' type='text' value='' size='50' /> <img class='showTooltip' src='images/20px-Help.png' title='The title to use for the Commons announcement'/>
								</td>
							</tr>
							<tr>
								<td class='lca-label'>
									<label for='takedown-wmf-title'>WMFwiki Title</label>
								</td>
								<td>
									<input id='takedown-wmf-title' name='takedown-wmf-title' type='text' value='' size='50' /> <img class='showTooltip' src='images/20px-Help.png' title='The title to use for the WMF wiki posting (Don&#39;t worry I&#39;ll give you a link on the next page)'/>
								</td>
							</tr>
							<tr>
								<td class='lca-label'>
									<label for='takedown-method'>How was the C&amp;D sent?</label>
								</td>
								<td>
									<input id='takedown-method' name='takedown-method' value='email' type='text' size='25' /> <img class='showTooltip' src='images/20px-Help.png' title='(e.g. email, postal mail, fax ...)'/>
								</td>
							</tr>
							<tr>
								<td class='lca-label'>
									<label for='takedown-subject'> Subject Line </label>
								</td>
								<td>
									<input id='takedown-subject' name='takedown-subject' value='' type='text' size='50' /> <img class='showTooltip' src='images/20px-Help.png' title='Subject line of the email or fax recieved'/>
								</td>
							</tr>
						</table>
					</fieldset>
					<fieldset>
						<legend> The Takedown </legend>
						<p> Takedown text - copy and paste email etc. </p>
						<textarea name='takedown-body' wrap='virtual' rows='18' cols='70'></textarea>
						<input type='hidden' name='MAX_FILE_SIZE' value='52428800' />
						<p>Supporting file 1 (scanned takedown etc) <input name='takedown-file1' type='file' /></p>
						<p>Supporting file 2 (scanned takedown etc) <input name='takedown-file2' type='file' /></p>
					</fieldset>
					<input type='submit' value='Process takedown'>
				</form>
			</div>
		</div>
			<div id='column-one'>
				<div role="navigation" id="p-personal" class="portlet">
					<div class="pBody">
						<ul>
							<li id="pt-login">You are logged in as <b><u><?php echo $_SERVER['PHP_AUTH_USER']; ?></u></b> if you are done or not you <b><a href="#" onclick="javascript: logout();">log out </a> </b></li>
						</ul>
					</div>
				</div>
				<div class='portlet' id='p-logo' role='banner'>
					<a href='index.php' title='Back home'></a>
				</div>
				<div class='no-text-transform portlet' id='p-navigation' role='navigation'>
					<h3>LCA links</h3>
					<div class='pBody'>
						<ul>
							<li id='lca-central-link'>
								<a href='https://sites.google.com/a/wikimedia.org/lca-central/'>LCA Central</a>
							</li>
							<li id='officewiki-link'>
								<a href='https://office.wikimedia.org/wiki/Main_Page'>Office Wiki</a>
							</li>
							<li id='collabwiki-link'>
								<a href='https://collab.wikimedia.org/wiki/Main_Page'>Collab Wiki</a>
							</li>
						</ul>
					</div>
				</div>
				<div class='no-text-transform portlet' id='p-navigation' role='navigation'>
					<h3>LCATools Forms</h3>
					<div class='pBody'>
						<ul>
							<li id='dmca-takedown-form'>
								<a href="legalTakedown.php">DMCA Takedown Form</a>
							</li>
							<li id='ncmec-form'>
								<a href="NCMECreporting.php"> Child Protection Takedown Form </a>
							</li>
						</ul>
					</div>
				</div>
				<div class='no-text-transform portlet' id='p-navigation' role='navigation'>
					<h3>LCATools Special Pages</h3>
					<div class='pBody'>
						<ul>
							<li id='dmca-takedown-form'>
								<a href="CentralLog.php"> Central submission Log </a>
							</li>
						</ul>
					</div>
				</div>
				<div class='no-text-transform portlet' id='p-dmcalinks' role='navigation'>
					<h3>DMCA related links</h3>
					<div class='pBody'>
						<ul>
							<li id='comdmca-link'>
								<a href='https://commons.wikimedia.org/wiki/COM:DMCA'>Commons DMCA Page</a>
							</li>
							<li id='comvp-link'>
								<a href='https://commons.wikimedia.org/wiki/Commons:Village_pump'>Commons Village Pump</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>