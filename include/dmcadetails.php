<p> See below for the information that was submitted. <?php if ($istest === 'Y') { echo '<b> NOTE: This was marked as a test submission </b>'; } ?></p>
<p> This data was submitted by <?php echo $user; ?> with a timestamp of <?php echo $timestamp; ?> UTC </p>
	<fieldset>
		<legend>Log Details</legend>
		<table border='1' id='mw-movepage-table'> 
			<tr>
				<td>
					<label for='was-sent-ce'>Was this report sent to Chilling Effects?</label>
				</td>
				<td>
					<?php
					if ($ce_url) {
						echo 'Yes';
					} else { echo 'No'; }?>
				</td>
			</tr>
			<tr>
				<td>
					<label for='ce-url'>Chilling Effect Report URL </label>
				</td>
				<td>
					<?php
					if ($ce_url) {
						echo htmlspecialchars($ce_url);
					} else { echo 'You do not appear to have sent anything to CE';}?>
				</td>
			</tr>
			<tr>
				<td>
					<label for='when-takedown'> When was this takedown carried out? </label>
				</td>
				<td>
					<?php
					if ($takedown_date) {
						echo $takedown_date;
					} else { echo 'This does not appear to be set';}?>
			<tr>
				<td>
					<label for='username-involved'> What user uploaded the content? </label>
				</td>
				<td>
					<?php
					if ($involved_user) {
						echo htmlspecialchars($involved_user);
					} else { echo 'This does not appear to be set';}?>
				</td>
			</tr>
			<tr>
				<td>
					<label for='file links'> Links for files involved </label>
				</td>
				<td>
					<?php
					if ($linksarray) {
						foreach ($linksarray AS $url) {
							echo '<a target="_blank" href="'.htmlspecialchars($url).'">'.htmlspecialchars($url).'</a></br>';
						}
					} else { echo 'This does not appear to be set';}?>
				</td>
			</tr>
			<tr>
				<td>
					<label for='wmfWiki-link'> Takedown on WMFwiki </label>
				</td>
				<td>
					<?php
					if ($wmfwiki_title) {
						echo '<a target="_blank" href="https://wikimediafoundation.org/wiki/'.htmlspecialchars($wmfwiki_title).'">'.htmlspecialchars($wmfwiki_title).'</a>';
					} else { echo 'This does not appear to be set';}?>
				</td>
			</tr>
			<tr>
				<td>
					<label for='commons-title'> COM:DMCA Link </label>
				</td>
				<td>
					<?php
					if ($commons_title) {
						echo '<a target="_blank" href="https://commons.wikimedia.org/wiki/Commons:Office_actions/DMCA_notices#'.htmlspecialchars($commons_title).'">'.htmlspecialchars($commons_title).'</a>';
					}  else { echo 'This does not appear to be set';}?>
				</td>
			</tr>
			<tr>
				<td>
					<label for='files-sent'> Any files sent to Chilling Effects: </label>
				</td>
				<td>
					<?php
					if ($filessent) {
						foreach ($filessent as $file) {
							echo htmlspecialchars($file);
						}
					} else { echo 'There does not appear to have been any files sent';}?>
				</td>
			</tr>
			<tr>
				<td>
					<label for='metadata'> Due diligence, these statements are certified as true: </label>
				</td>
				<td>
					<?php
					if ($logging_metadata) {
						foreach ($logging_metadata as $value) {
							echo htmlspecialchars($value).'<br />';
						}
					} else { echo 'This does not appear to be set';}?>
				</td>
			</tr>
			<tr>
				<td>
					<label for='strike-info'>was there any reason not to count this as a strike?</label>
				</td>
				<td>
					<?php
					if ($strike_note) {
						foreach ($strike_note as $value) {
							echo htmlspecialchars($value);
						}
					} else { echo 'This does not appear to be set';}?>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend> Stored comparison data </legend>
		<table border='1' id='mw-movepage-table'> 
			<tr>
				<td>
					<label for='sender-country'>Country of takedown sender</label>
				</td>
				<td>
					<?php
					if ($sender_country) {
						echo $sender_country;
					} else { echo 'This does not appear to be set';}?>
				</td>
			</tr>
			<tr>
				<td>
					<label for='sender-state'>State/Province of takedown sender</label>
				</td>
				<td>
					<?php
					if ($sender_state) {
						echo $sender_state;
					} else { echo 'This does not appear to be set';}?>
				</td>
			</tr>
			<tr>
				<td>
					<label for='sender-zip'>postal code of takedown sender</label>
				</td>
				<td>
					<?php
					if ($sender_zip) {
						echo $sender_zip;
					} else { echo 'This does not appear to be set';}?>
				</td>
			</tr>
			<tr>
				<td>
					<label for='sender-city'> City of takedown sender </label>
				</td>
				<td>
					<?php
					if ($sender_city) {
						echo $sender_city;
					} else { echo 'This does not appear to be set';}?>
				</td>
			</tr>
			<tr>
				<td>
					<label for='action-taken'>Did we carry out the takedown?</label>
				</td>
				<td>
					<?php
					if ($action_taken) {
						echo $action_taken;
					} else { echo 'This does not appear to be set';}?>
				</td>
			</tr>
			<tr>
				<td>
					<label for='takedown-method'>How did we recieve the takedown?</label>
				</td>
				<td>
					<?php
					if ($takedown_method) {
						echo $takedown_method;
					} else { echo 'This does not appear to be set';}?>
				</td>
			</tr>
			</table>
	</fieldset>




