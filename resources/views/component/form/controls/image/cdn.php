<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 20.09.12
 * Time: 20:47
 */
?>
<table>
	<thead>
	<tr>
		<th>на сервере</th>
		<th>в CDN</th>
	</tr>
	</thead>
	<tbody>
<?php foreach ($image_types as $image_type): ?>
	<tr>
		<td class="table-col-middle"><?php
			if ($zoom):
				echo '<a href="' . Helper_Image::get_filename($image, $image_type) . '" class="zoom">';
				echo Helper_Image::get_image($image, $image_type) . '</a>';
			else:
				echo Helper_Image::get_image($image, $image_type);
			endif;
		?></td>
		<td class="table-col-middle"><?php
			if ($zoom):
				echo '<a href="' . Helper_Image::get_cdn_filename($image, $image_type) . '" class="zoom">';
				echo Helper_Image::get_cdn_image($image, $image_type) . '</a>';
			else:
				echo Helper_Image::get_cdn_image($image, $image_type);
			endif;
		?></td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php $attributes = !empty($attributes) ? HTML::attributes($attributes) : ''; ?>
<span<?php echo $attributes; ?>><input type="file" class="input-fake" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value=""/></span>