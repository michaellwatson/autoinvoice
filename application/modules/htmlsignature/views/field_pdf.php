<?php 
if(isset($listing['ad_'.$field['adv_column']])){
	?>
	<table style="width:100%;">
		<tr>
			<td>
				<p>Report prepared by</p>
			</td>
		</tr>
	</table>
	<?php
	switch($listing['ad_'.$field['adv_column']]){
		case "1":
			echo '<img src="'.base_url('assets/uploads/signature/phil_signature.png').'"><br>';
			?>
			<table style="width:100%;">
				<tr>
					<td>
						<p>
						Philip S Diamond MRICS<br>
						Chartered Building Surveyor<br>
						Diamond & Company (Scotland) Ltd /<br>
						Brooker Diamond Chartered Fire Engineering Ltd<br>
						</p>
					</td>
				</tr>
			</table>
			<?php
		break;
		case "2":
			echo '<img src="'.base_url('assets/uploads/signature/steve_brooker.jpg').'"><br>';
			?>
			<table style="width:100%;">
				<tr>
					<td>
					<p>
					Stephen Brooker CEng, MSc, MIFire E<bR>
					Chartered Fire Engineer<br>
					Brooker Diamond Chartered Fire Engineering Ltd<br>
					</p>
					</td>
				</tr>
			</table>
			<?php
		break;
		case "3":
			?>
			<table style="width:100%">
				<tr>
					<td>
						<?php 
							echo '<img src="'.base_url('assets/uploads/signature/phil_signature.png').'"><br>';
						?>
						<p>
						Philip S Diamond MRICS<br>
						Chartered Building Surveyor<br>
						Diamond & Company (Scotland) Ltd /<br>
						Brooker Diamond Chartered Fire Engineering Ltd<br>
						</p>
					</td>
					<td>
						<?php
							echo '<img src="'.base_url('assets/uploads/signature/steve_brooker.jpg').'"><br>';
						?>
						<p>
						Stephen Brooker CEng, MSc, MIFire E<bR>
						Chartered Fire Engineer<br>
						Brooker Diamond Chartered Fire Engineering Ltd<br>
						</p>
					</td>
				</tr>
			</table>
			<?php
		break;
		default:
			echo 'This Report has NOT been signed for';
		break;
	}
}
?>
                