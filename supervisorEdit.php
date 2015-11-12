<?php
use Tk\Form;
use Tk\Form\Type;
use \Tk\Form\Renderer\Dom\FieldFactory;

include(dirname(__FILE__) . '/vendor/autoload.php');
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
    <h1><a href="index.php">Create Supervisor</a></h1>
    <p>&nbsp;</p>
    <div class="content" var="content"></div>
    <p>&nbsp;</p>
  </div>

</body>
</html>
<?php
$buff = trim(ob_get_clean());
$template = \Dom\Template::load($buff);

$request = $_REQUEST;
$supervisor = new \App\Db\Supervisor();
if (isset($request['supervisorId'])) {
  $supervisor = \App\Db\Supervisor::getMapper()->find($request['supervisorId']);
}


/**
 * @param \Tk\Form $form
 */
function doSubmit($form)
{
  $supervisor = $form->getParam('supervisor');

  if (!$supervisor instanceof \App\Db\Supervisor) return;

  // Load the object with data from the form using a helper object
  \App\Form\ModelLoader::loadObject($form, $supervisor);

  if (!$supervisor->title) {
    $form->addFieldError('title', 'Invalid field value.');
  }
  if (!$supervisor->status) {
    $form->addFieldError('status', 'Invalid field value.');
  }
  if (!$supervisor->firstName) {
    $form->addFieldError('firstName', 'Invalid field value.');
  }

  if ($form->hasErrors()) {
    return;
  }

  $supervisor->save();

  if ($form->getTriggeredEvent()->getName() == 'update')
    \App\Url::create('/supervisorManager.php')->redirect();
  \App\Url::create()->redirect();
}


$form = new Form('formEdit', array('supervisor' => $supervisor));

$form->addField(FieldFactory::createText('courseId'))->setRequired(true);
$form->addField(FieldFactory::createText('title'));
$form->addField(FieldFactory::createText('firstName'));
$form->addField(FieldFactory::createText('lastName'));
$form->addField(FieldFactory::createText('graduationYear'));
$list = new \Tk\Form\Field\Option\ArrayIterator(array('-- Select --' => '', 'Approved' => 'approved', 'Not Approved' => 'not approved', 'Pending' => 'pending'));
$form->addField(FieldFactory::createSelect('status', $list))->setRequired(true);
$form->addField(FieldFactory::createCheckbox('private'));

$form->addField(FieldFactory::createButton('update', 'doSubmit')->setIcon('fa fa-mail-reply'));
$form->addField(FieldFactory::createButton('save', 'doSubmit')->setIcon('fa fa-save'));
$form->addField(FieldFactory::createLink('cancel', \App\Url::create('/supervisorManager.php')));

$form->loadFromObject($supervisor);
$form->execute($request);


// SHOW
$fren = \Tk\Form\Renderer\Dom\Form::create($form)->show();
$template->insertTemplate('content', $fren->getTemplate());

if ($supervisor->title) {
  $template->insertText('title', 'Edit Supervisor: ' . $supervisor->title . ' ' . $supervisor->firstName . ' ' . $supervisor->lastName);
}


echo $template->toString();
