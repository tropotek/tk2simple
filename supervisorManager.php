<?php
include(dirname(__FILE__) . '/_prepend.php');

use Tk\Table\Cell;
use Tk\Form\Field;

ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <meta name="description" content=""/>
  <meta name="author" content=""/>
  <title>Simple Tk Project - Supervisor Manager</title>

  <!-- Styles -->
  <link href="css/bootstrap.min.css" rel="stylesheet" />
  <link href="css/custom.css" rel="stylesheet" />

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="js/bootstrap.min.js"></script>

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
            <a class="navbar-brand" href="index.php">tk2simple</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="index.php">Home</a></li>
                <li><a href="supervisorManager.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

  <div class="container-fluid">
    <h1><a href="index.php">Supervisor Manager</a></h1>
    <p>&nbsp;</p>
    <div class="content" var="content"></div>
    <p>&nbsp;</p>
  </div>

</body>
</html>
<?php
$buff = trim(ob_get_clean());
$template = \Dom\Template::load($buff);


//$db = \Tk\Config::getInstance()->getDb();

$table = \Tk\Table::create('supervisorManager');
//$table->addParam('renderer', \Tk\Table\Renderer\Dom\Table::create($table)); // (optional, instead of creating the renderer blow)

$table->appendCell(new Cell\Checkbox('id'));
$table->appendCell(new Cell\Text('courseId'));
$table->appendCell(new Cell\Text('firstName'))->addCss('key')->setUrl(\Tk\Uri::create('/supervisorEdit.php'));
$table->appendCell(new Cell\Text('lastName'));
$table->appendCell(new Cell\Text('graduationYear'));
$table->appendCell(new Cell\Text('status'));
$table->appendCell(new Cell\Boolean('private'));
$table->appendCell(new Cell\Date('modified'));
$table->appendCell(new Cell\Date('created'));

// Add filters
$table->appendFilter(new Field\Input('keywords'))->setLabel('')->setAttr('placeholder', 'Keywords');
$table->appendFilter(new Field\Input('firstName'))->setLabel('')->setAttr('placeholder', 'First Name');

// Add Actions?
$table->appendAction(\Tk\Table\Action\Delete::create());
$table->appendAction(\Tk\Table\Action\Csv::create());

$list = \App\Db\SupervisorMap::create()->findFiltered($table->getFilterValues(), $table->getTool());
$table->setList($list);

$tren = \Tk\Table\Renderer\Dom\Table::create($table);
$template->replaceTemplate('content', $tren->show());

echo $template->toString();
