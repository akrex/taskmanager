<h2>Регистрация</h2>
<?php Helper::BeginForm($this->data, "register","profile", array("role" => "form")); ?>
    <?php Helper::InputFor("username", array("class"=>"form-control", "placeholder"=>"Введите имя пользователя")); ?>
    <?php Helper::PasswordFor("password", array("class"=>"form-control", "placeholder"=>"Введите пароль")); ?>
    <?php Helper::PasswordFor("password_repeat", array("class"=>"form-control", "placeholder"=>"Повторите пароль")); ?>
    <?php Helper::InputFor("FIO", array("class"=>"form-control", "placeholder"=>"Введите ваше имя и фамилию")); ?>
    <?php Helper::InputFor("email", array("class"=>"form-control", "placeholder"=>"Введите email")); ?>
    <?php Helper::InputFor("phone", array("class"=>"form-control", "placeholder"=>"Введите номер телефона")); ?>
    <?php Helper::Submit("Регистрация", "btn-warning"); ?>
<?php Helper::EndForm(); ?>