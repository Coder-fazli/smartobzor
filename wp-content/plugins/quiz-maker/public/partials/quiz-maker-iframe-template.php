<?php
/**
 * @var int $id The quiz ID.
 * @var array $attr The attributes from shortcode.
 */
?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1">
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">

        <?php echo $this->enqueue_styles(); ?>
    </head>
    <body>
        <?php echo $this->enqueue_scripts($id); ?>
        <?php echo self::ays_quiz_translate_content($this->public_obj->show_quiz( $id, $attr )); ?>
    </body>
</html>
