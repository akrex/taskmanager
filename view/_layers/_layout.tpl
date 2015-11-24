<!DOCTYPE html>
<html lang="ru">
    <head>
        <title><?php echo $title." - ".SITE_NAME; ?> </title>
        <?php Helper::AddCss("css/bootstrap.css"); ?>
        <?php Helper::AddCss("css/bootstrap-theme.css"); ?>
        <?php Helper::AddCss("css/bootstrap-datepicker3.css"); ?>
        <?php Helper::AddCss("css/bootstrap-datetimepicker.css"); ?>
        <?php Helper::AddJavaScript("javascript/jquery-2.1.4.min.js"); ?>
        <?php Helper::AddJavaScript("javascript/bootstrap.js"); ?>
        <?php Helper::AddJavaScript("javascript/bootstrap-datepicker.js"); ?>
        <?php Helper::AddJavaScript("javascript/locales/bootstrap-datepicker.ru.min.js"); ?>
        <?php Helper::AddJavaScript("javascript/moment-with-locales.js"); ?>
        <?php Helper::AddJavaScript("javascript/bootstrap-datetimepicker.js"); ?>
    </head>
    <body>
        <div class="navbar navbar-inverse" role="navigation">
              <div class="container">
                <div class="navbar-header">
                  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </button>
                    <a class="navbar-brand" href="<?php echo Helper::Action("index","home"); ?>">Task Manager</a>
                </div>
                <div class="collapse navbar-collapse">
                  <ul class="nav navbar-nav">
                    <li class="<?php echo $section == "Home"?"active":""; ?>"><?php Helper::ActionLink("Главная","index","home");?></li>
                    <li class="<?php echo $section == "About"?"active":""; ?>"><?php Helper::ActionLink("О нас","about","home");?></li>
                  </ul>
                    <?php $this->RenderPartical("_layers/_loggin.tpl"); ?>
                </div>
              </div>
            </div>
        <div class="container">
            <?php if(isset($this->request->get["msg"])&& isset($this->msg[$this->request->get["msg"]])){ ?>
                <p class="bg-success">
                <?php echo $this->msg[$this->request->get["msg"]]; ?>
                </p>
            <?php } ?>
            <?php $this->RenderBody() ?>
        </div>
    </body>
</html>