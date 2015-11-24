<ul class="nav navbar-nav navbar-right">
<?php if($this->user->IsUserLogger()){ ?>
    <li  class="<?php echo $this->data["section"] == "Profile"?"active":""; ?>"><?php Helper::ActionLink("Привет ".$this->user->GetUserName(),"profile","profile"); ?></li>
<?php if($this->user->GetTaskCount()==0) $mytasktitle = "Мои задачи";
        else $mytasktitle = "Мои задачи (".$this->user->GetTaskCount().")"; ?>
    <li class="<?php echo $this->data["section"] == "Task"?"active":""; ?>"><?php Helper::ActionLink($mytasktitle,"index","task");?></li>
    <li><?php Helper::ActionLink("Выход","logout","profile");?></li>
<?php }else{ ?>
    <li class="<?php echo $this->data["section"] == "Login"?"active":""; ?>"><?php Helper::ActionLink("Вход","login","profile");?></li>
    <li class="<?php echo $this->data["section"] == "Registration"?"active":""; ?>"><?php Helper::ActionLink("Регистрация","register","profile");?></li>
  <?php } ?>
</ul>