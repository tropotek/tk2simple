<?php
use Tk\Form;
use Tk\Form\Field;
use Tk\Form\Event;

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
    <h1><a href="index.php">Contact Us</a></h1>
    <p>&nbsp;</p>
    <div class="content" var="content"></div>

    <!-- Contact Form -->
    <h3>Send Us a Message</h3>

    <div class="alert alert-success" role="alert" choice="sent">
      <strong>Success!</strong> Your form has been successfully sent.
    </div>

    <div class="contact-form-wrapper">
      <form id="contactForm" method="post" class="form-horizontal" role="form">


        <div class="form-group" var="group-type">
          <label for="prependedInput" class="col-sm-3 control-label">
            <b>Title</b>
          </label>
          <div class="col-sm-9">
            <select class="form-control" name="title" id="title">
              <option value="">-- Select --</option>
              <option value="Mr">Mr</option>
              <option value="Mrs">Mrs</option>
              <option value="Miss">Miss</option>
            </select>
          </div>
        </div>
          
        <div class="form-group">
          <label for="name" class="col-sm-3 control-label">
            <b>Name *</b>
          </label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="name" id="name" placeholder=""/>
          </div>
        </div>

        <div class="form-group">
          <label for="contactEmail" class="col-sm-3 control-label">
            <b>Email *</b>
          </label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="email" id="email" placeholder=""/>
          </div>
        </div>

          <div class="form-group" var="group-type">
            <label for="prependedInput" class="col-sm-3 control-label">
              <b>Topic</b>
            </label>
            <div class="col-sm-9">
              <select class="form-control" name="type[]" id="type" multiple="multiple" size="3">
              <option value="General">General</option>
              <option value="Services">Services</option>
              <option value="Orders">Orders</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="contactEmail" class="col-sm-3 control-label">
            <b>Attach</b>
          </label>
          <div class="col-sm-9">
            <input type="file" name="attach[]" id="attach" multiple="multiple" />
          </div>
        </div>

        <div class="form-group" var="group-message">
          <label for="message" class="col-sm-3 control-label">
            <b>Message *</b>
          </label>
          <div class="col-sm-9">
            <textarea class="form-control" rows="5" name="message" id="message"></textarea>
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-12">
            <button type="submit" class="btn pull-right" name="send">Send</button>
          </div>
        </div>

      </form>

      </div>

    <p>&nbsp;</p>
  </div>

</body>
</html>
<?php
$buff = trim(ob_get_clean());
$template = \Dom\Template::load($buff);


/**
 * doSubmit()
 *
 * @param Form $form
 */
function doSubmit($form)
{
  $values = $form->getValues();
  $config = \Tk\Config::getInstance();

  /** @var \Tk\Form\Field\File $attach */
  $attach = $form->getField('attach');

  if (empty($values['name'])) {
    $form->addFieldError('name', 'Please enter your name');
  }
  if (empty($values['email']) || !filter_var($values['email'], \FILTER_VALIDATE_EMAIL)) {
    $form->addFieldError('email', 'Please enter a valid email address');
  }
  if (empty($values['message'])) {
    $form->addFieldError('message', 'Please enter some message text');
  }
  
  //$form->addFieldError('test', 'ggggg');

  // validate any files
  $attach->isValid();

  if ($form->hasErrors()) {
    return;
  }

  if ($attach->hasFile()) {
    $attach->moveUploadedFile($config->getDataPath() . '/contact/' . date('d-m-Y') . '-' . str_replace('@', '_', $values['email']));
  }
  
  if (sendEmail($form)) {
    //\App\Alert::getInstance()->addSuccess('<strong>Success!</strong> Your form has been sent.');
    error_log('Contact form success.');
  }

  \App\Url::create()->redirect();
}


/**
 * sendEmail()
 *
 * @param Form $form
 * @return bool
 */
function sendEmail($form)
{
    $title = $form->getFieldValue('title');
    $name = $form->getFieldValue('name');
    $email = $form->getFieldValue('email');
    $type = '';
    if (is_array($form->getFieldValue('type')))
    $type = implode(', ', $form->getFieldValue('type'));
    $message = $form->getFieldValue('message');
    $attachCount = '';

  $file = $form->getField('attach');
  if ($file->count()) {
    $attachCount = 'Attachments: ' . $file->count();
  }

    
  $message = <<<MSG
Dear $title $name,

Email: $email
Type: $type

Message:
  $message

$attachCount
MSG;
    
  error_log("\n".$message);
    
  return true;
}


//$form = \App\Form\FormHelper::createForm($template->getForm('contactForm'));
//$domForm = $template->getForm('contactForm');

$form = new Form('contactForm');

$opts = new \Tk\Form\Field\Option\ArrayIterator(array('Mr', 'Mrs', 'Miss'));
$form->addField(new Field\Select('title', $opts));
$form->addField(new Field\Input('name'));
$form->addField(new Field\Input('email'));

//$opts = \Tk\Form\Field\Option\ArrayIterator::create(array('General', 'Services', 'Orders'));
$opts = new \Tk\Form\Field\Option\ArrayIterator(array('General' => 'General', 'Services' => 'Services', 'Orders' => 'Orders'));
$form->addField(new Field\Select('type[]', $opts));

$form->addField(new Field\File('attach[]'));
$form->addField(new Field\Textarea('message'));

$form->addField(new Event\Button('send', 'doSubmit'));

// Init form data and fire any event
$form->execute();


// Render the form
$fren = new \Tk\Form\Renderer\DomStatic($form, $template);
$fren->show();


echo $template->toString();
