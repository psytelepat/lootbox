<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 24.07.14
 * Time: 19:43
 */
?>
<?php foreach ($image_types as $image_type): ?>
	<?php if ($zoom):
		echo '<a href="' . Helper_Image::get_filename($image, $image_type, $default_image) . '" class="zoom">';
		echo Helper_Image::get_image($image, $image_type, null, null, $default_image) . '</a>';
	else:
		echo Helper_Image::get_image($image, $image_type, null, null, $default_image);
	endif; ?>
	<br />
<?php endforeach; ?>

	<span<?php echo HTML::attributes($attributes); ?>><input type="file" class="input-fake" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="" /></span>
	<br />
<?php if ($is_allow_remove_image && !empty($image)): ?>
	<label class="btn btn-default">
		<?php echo Form::checkbox($name_remove_image, NULL, false); ?>
		<?php echo __('image.remove'); ?>
	</label>
	<div class="clearfix"></div>
	<br />
<?php endif; ?>