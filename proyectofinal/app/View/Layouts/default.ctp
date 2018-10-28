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
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?>:
		<?php echo $this->fetch('title'); ?>
	</title>
	<?php
		echo $this->Html->meta('icon');
        echo $this->Html->css('bootstrap');
        echo $this->Html->css('font-awesome.min');
        echo $this->Html->css('font-awesome-animation.min');
        echo $this->Html->css('main');
        echo $this->Html->css('tables-bootstrap');
        echo $this->Html->css('modal');
        echo $this->Html->css('dropdown');
        echo $this->Html->css('dashboard');
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
        echo $this->Html->script('plotly-latest.min');


		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
    <script type="text/javascript">
        var projectDirectory = '<?php echo Configure::read("PROJECT_DIRECTORY"); ?>';
    </script>
</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/proyectofinal/main/index"><strong>Home</strong> - Financial Forecasting</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#">TFG <strong>José Sanz Durán</strong></a></li>
            </ul>
        </div>

        <!--<div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#">Dashboard</a></li>
                <li><a href="#">Settings</a></li>
                <li><a href="#">Profile</a></li>
                <li><a href="#">Help</a></li>
            </ul>
            <form class="navbar-form navbar-right">
                <input type="text" class="form-control" placeholder="Search...">
            </form>
        </div>-->
    </div>
</nav>


<?php echo $this->Flash->render(); ?>
<?php echo $this->fetch('content'); ?>
<div class="js-modal"></div>
<div id="processAjax" class="alert alert-info fade in" style="background-color: #ffff00; color:#103184" role="alert">
    <strong><i class="fa fa-lg fa-refresh fa-spin fa-margin-right"></i></strong> Procesando
</div>
<div id="processLayout"></div>
</body>
</html>
