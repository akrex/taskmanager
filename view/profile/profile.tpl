<h2>Настройка профиля</h2>
<?php if($email_checked == 0){ ?>
<p class="bg-danger">
    Вы не подтвердили свою почту! </br>
    Без подтверждённой почты вы не сможете получать уведомления о событиях на свою почту! </br>
    Проверьте свой ящик и подвердите ее!
</p>
<?php } ?>
<?php Helper::BeginForm($this->data, "profile","profile", array("role" => "form")); ?>
    <?php Helper::HiddenFor("username"); ?>
    <?php Helper::InputFor("username", array("class"=>"form-control", "disabled"=>"disabled", "placeholder"=>"Введите имя пользователя")); ?>
    <?php Helper::PasswordFor("password", array("class"=>"form-control", "placeholder"=>"Введите пароль")); ?>
    <?php Helper::PasswordFor("password_repeat", array("class"=>"form-control", "placeholder"=>"Повторите пароль")); ?>
    <?php Helper::InputFor("FIO", array("class"=>"form-control", "placeholder"=>"Введите ваше имя и фамилию")); ?>
    <?php Helper::InputFor("email", array("class"=>"form-control", "placeholder"=>"Введите email")); ?>
    <?php Helper::InputFor("phone", array("class"=>"form-control", "placeholder"=>"Введите номер телефона")); ?>
    <?php Helper::Submit("Сохранить", "btn-warning"); ?>
<?php Helper::EndForm(); ?>