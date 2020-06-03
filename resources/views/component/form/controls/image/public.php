<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 20.09.12
 * Time: 20:47
 */
?>
<?php echo Helper_Image::get_image_from_current_session_or_from_cdn($image, $image_types[0]); ?><br/>
<?php $attributes = !empty($attributes) ? HTML::attributes($attributes) : ''; ?>
<span<?php echo $attributes; ?>><input type="file" class="input-fake" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value=""<?php echo $attributes; ?>/></span>