<?php
$firstParaMsg = ("This link will become invalid in $expirationTime hours and you have to request again.");
?>
<table cellspacing="0" cellpadding="0" width="100%" class="w320" style="border-collapse: collapse !important; font-family: Helvetica, Arial, sans-serif;">
	<tbody>
		<tr style="font-family: Helvetica, Arial, sans-serif;">
			<td class="header-lg" style="border-collapse: collapse; color: #4d4d4d; font-family: Helvetica, Arial, sans-serif; font-size: 32px; font-weight: 700; line-height: normal; padding: 35px 0 0; text-align: center;">
				<?= ('Activate Your New Email!') ?>
			</td>
		</tr>
		<tr style="font-family: Helvetica, Arial, sans-serif;">
			<td class="free-text" style="border-collapse: collapse; color: #777777; font-family: Helvetica, Arial, sans-serif; font-size: 14px; line-height: 21px; padding: 10px 60px 0px; text-align: center; width: 100% !important;">
				<?= ("To activate your new email address, please click on below button.") ?>
			</td>
		</tr>
		<tr style="font-family: Helvetica, Arial, sans-serif;">
			<td class="button" style="border-collapse: collapse; color: #777777; font-family: Helvetica, Arial, sans-serif; font-size: 14px; line-height: 21px; padding: 30px 0; text-align: center;">
				<div style="font-family: Helvetica, Arial, sans-serif;">
					<a href="<?= $activation_url ?>" target="_blank" style="-webkit-text-size-adjust: none; background-color: #2BAC32;border-color:#119242; border-radius: 5px; color: #ffffff; display: inline-block; font-family: 'Cabin', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: regular; line-height: 45px; mso-hide: all; text-align: center; text-decoration: none !important; width: 155px;"><?= ('Activate New Email') ?></a></div>
			</td>
		</tr>
		<tr style="font-family: Helvetica, Arial, sans-serif;">
			<td class="free-text" style="border-collapse: collapse; color: #777777; font-family: Helvetica, Arial, sans-serif; font-size: 18px; line-height: 21px; padding: 10px 60px 0px; text-align: center; width: 100% !important;">
				<?= e($firstParaMsg) ?>
			</td>
		</tr>
		<tr style="font-family: Helvetica, Arial, sans-serif;">
			<td class="free-text" style="border-collapse: collapse; color: #777777; font-family: Helvetica, Arial, sans-serif; font-size: 13px; line-height: 21px; padding: 10px 60px 0px; text-align: center; width: 100% !important;">
				<?= ('Your New email details are as follows:') ?>
			</td>
		</tr>
		<tr>
			<td class="mini-container-right" style="border-collapse: collapse; color: #777777; font-family: Helvetica, Arial, sans-serif; font-size: 14px; line-height: 21px; padding: 10px 14px 10px 15px; text-align: center; width: 278px;">
				<table cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse !important; font-family: Helvetica, Arial, sans-serif;">
					<tbody>
						<tr style="font-family: Helvetica, Arial, sans-serif;">
							<td class="mini-block-padding" style="border-collapse: collapse; color: #777777; font-family: Helvetica, Arial, sans-serif; font-size: 14px; line-height: 21px; text-align: center;">
								<table cellspacing="0" cellpadding="0" width="100%" style="border-collapse: collapse !important; font-family: Helvetica, Arial, sans-serif;">
									<tbody>
										<tr style="font-family: Helvetica, Arial, sans-serif;">
											<td class="mini-block" style="background-color: #ffffff; border-collapse: collapse; border-radius: 8px; color: #777777; font-family: Helvetica, Arial, sans-serif; font-size: 14px; line-height: 21px; padding: 12px 15px 15px; text-align: center; width: 253px;">
												<span class="header-sm" style="color: #4d4d4d; font-family: Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 700; line-height: 1.3; padding: 5px 0;"> <?= ('Email Details') ?></span><br style="font-family: Helvetica, Arial, sans-serif;">
												<strong><?= ('Email :') ?> </strong> <a href="<?= e($newEmail) ?>" style="color: #2ba6cb; text-decoration: none;"><?= e($newEmail) ?></a>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>