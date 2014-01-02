<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en-US' xml:lang='en-US'>
<head>
	<link rel='shortcut icon' href='images/favicon.ico'/>
	<title>Release of Confidential Information</title>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	<script src='scripts/jquery-1.10.2.min.js'></script>
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
				<h1> Release of Confidential Information </h1>
				<br />
				<form method='post' action='basicReleaseProcessor.php' id='release-form1' enctype='multipart/form-data'>
					<table>
						<tr>
							<td>
								<label for='is-test'> Is this a test? </label>
							</td>
							<td>
								<select name='is-test' id='is-test'>
									<option>No</option>
									<option>Yes</option>
								</select> <img class='showTooltip' src='images/20px-Help.png' title='Select Yes if this is a test of the processing system. Remember to select No for sendign to Chilling Effects '/>
							</td>
						</tr>
					</table>
					<fieldset>
						<legend>Release info</legend>
						<table border='1' id='mw-movepage-table'> 
							<tr>
								<td>
									<label for='who-received'> Who was this released too? </label> <img class='showTooltip' src='images/20px-Help.png' title='Choose as many as make qualify, remember to check the other box if you fill out the text field.'/>
								</td>
								<td>
									<input type='checkbox' name='who-received[]' value='Legal' /> Legal <br />
									<input type='checkbox' name='who-received[]' value='Law Enforcement' /> Law Enforcement <br />
									<input type='checkbox' name='who-received[]' value='Other' /> Other: <input type='text' name ='who-received-other' id='who-received-other' size='30' value=''>
								</td>
							</tr>
							<tr>
								<td>
									<label for='pre-approved'> Was this release pre-approved through the Office of the General Counsel, <br /> <b>OR</b> was it an urgent matter of life and limb? </label>
								</td>
								<td>
									<input type='radio' name='pre-approved' value='Yes' /> Yes <br />
									<input type='radio' name='pre-approved' value='No' /> No
								</td>
							</tr>
							<tr>
								<td>
									<label for='why-released'> Please provide the reason for the release.</label>
								</td>
								<td>
									<input type='checkbox' name='why-released[]' value='Threat of harm to a person or facility.' /> Threat of harm to a person or facility. <br />
									<input type='checkbox' name='why-released[]' value='Terroristic threat' /> Terroristic threat <br />
									<input type='checkbox' name='why-released[]' value='Subpoena compliance' /> Subpoena compliance <br />
									<input type='checkbox' name='why-released[]' value='Other' /> Other: <input type='text' name='why-released-other' id='why-released-other' size='30' value='' />
								</td>
							</tr>
							<tr>
								<td>
									<label for='who-released'> Who released the information? </label> <img class='showTooltip' src='images/20px-Help.png' title='Enter the person&#39;s name'/>
								</td>
								<td>
									<input type='text' value='' name='who-released' id='who-released' size='30' /> 
								</td>
							</tr>
							<tr>
								<td>
									<label for='who-released-to'>What is the name of the person to whom it was released?</label> <img class='showTooltip' src='images/20px-Help.png' title='Enter the contacts name'/>
								</td>
								<td>
									<input type='text' value='' name='who-released-to' id='who-released-to' size='30' /> 
								</td>
							</tr>
							<tr>
								<td>
									<label for='released-to-contact'> If to a non-WMF contact, how could we contact that person if necessary? </label><img class='showTooltip' src='images/20px-Help.png' title='Enter contact information here if they are NOT a WMF employee.'/>
								</td>
								<td>
									<input type='text' name='released-to-contact' id='released-to-contact' size='30' />
								</td>
							</tr>
						</table>
					</fieldset>
					<fieldset>
						<legend>Please describe the situation, including any applicable links. </legend>
						<textarea name='details' wrap='virtual' rows='18' cols='70'></textarea>
					</fieldset>
					<input type='submit' value='Process' /> 
				</form>
			</div>
		</div>
			<?php include('include/lcapage.php'); ?>
		</div>
	</body>
</html>