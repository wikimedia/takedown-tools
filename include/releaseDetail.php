<p> See below for the information that was submitted. <?php if ($istest === 'Y') { echo '<b> NOTE: This was marked as a test submission </b>'; } ?></p>
<p> This data was submitted by <?php echo $user; ?> with a timestamp of <?php echo $timestamp; ?> UTC </p>
	<fieldset>
		<legend> Release info </legend>
		<table border='1' id='mw-movepage-table'> 
			<tr>
				<td>
					<label for='who-received'> Who was this released too? </label> 
				</td>
				<td>
					<?php if(!empty($who_received)) { 
						foreach ($who_received as $value) {
							echo htmlspecialchars($value) . '<br />';
						}
					} else { echo 'This option was not set'; } ?>
				</td>
			</tr>
			<tr>
				<td>
					<label for='pre-approved'> Was this release pre-approved through the Office of the General Counsel, <br /> <b>OR</b> was it an urgent matter of life and limb? </label>
				</td>
				<td>
					<?php if(!empty($pre_approved)) {
						echo htmlspecialchars($pre_approved);
					} else { echo 'This option was not set'; } ?>
				</td>
			</tr>
			<tr>
				<td>
					<label for='why-released'> Reason for the release.</label>
				</td>
				<td>
					<?php if(!empty($why_released)) {
						foreach ($why_released as $value) {
							echo htmlspecialchars($value) . '<br />';
						}
					} else { echo 'This option was not set'; } ?>
				</td>
			</tr>
			<tr>
				<td>
					<label for='who-released'> Who released the information? </label> 
				<td>
					<?php if(!empty($who_released)) {
						echo htmlspecialchars($who_released);
					} else { echo 'This option was not set'; } ?>
				</td>
			</tr>
			<tr>
				<td>
					<label for='who-released-to'>What is the name of the person to whom it was released?</label>
				</td>
				<td>
					<?php if(!empty($who_released_to)) {
						echo htmlspecialchars($who_released_to);
					} else { echo 'This option was not set'; } ?>
				</td>
			</tr>
			<tr>
				<td>
					<label for='released-to-contact'> If to a non-WMF contact, how could we contact that person if necessary? </label>
				</td>
				<td>
					<?php if(!empty($released_to_contact)) {
						echo htmlspecialchars($released_to_contact);
					} else { echo 'This option was not set'; } ?>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend>Please describe the situation, including any applicable links. </legend>
		<textarea name='details' wrap='virtual' rows='18' cols='70' readonly> <?php if(!empty($details)) {
			echo htmlspecialchars($details);
		}  else { echo 'This option was not set'; } ?></textarea>
	</fieldset>