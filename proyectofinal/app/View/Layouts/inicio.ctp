<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version())
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="proyectofinal" content="">
    <meta name="josesanz" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>
        TFG_GIISI
        <?php echo $this->fetch('title'); ?>
    </title>
    <?php echo $this->Html->charset(); ?>

    <?php
    echo $this->Html->meta('icon');
    echo $this->Html->css('bootstrap');
    echo $this->Html->css('font-awesome.min');
    echo $this->Html->css('font-awesome-animation.min');
    echo $this->Html->css('tables-bootstrap');
    echo $this->Html->css('modal');
    echo $this->Html->css('dropdown');
    echo $this->Html->css('inicio');
    echo $this->Html->script('jquery');
    echo $this->Html->script('jquery.dataTables.min');
    echo $this->Html->script('dataTables.select.min');
    echo $this->Html->script('jquery.easyui.min');
    echo $this->Html->script('bootstrap');
    echo $this->Html->script('tables.min');
    echo $this->Html->script('tables-bootstrap.min');
    echo $this->Html->script('modal');
    echo $this->Html->script('jquery.number.min');
    echo $this->Html->script('ajax');
    echo $this->Html->script('moment');
    echo $this->Html->script('init');
    echo $this->Html->script('js.cookie');
    echo $this->Html->script('jquery.dataTables.min');
    echo $this->Html->script('dataTables.select.min');


    echo $this->fetch('meta');
    echo $this->fetch('css');
    echo $this->fetch('script');
    ?>
    <script type="text/javascript">
        var projectDirectory = '<?php echo Configure::read("PROJECT_DIRECTORY"); ?>';
    </script>

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

</head>
<body>
<?php $projectDirectory = '<?php echo Configure::read("PROJECT_DIRECTORY"); ?>'; ?>
<?php echo $this->Flash->render(); ?>
<?php echo $this->fetch('content'); ?>

</body>
</html>
