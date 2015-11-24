<h2>Востановление пароля</h2>
<?php Helper::BeginForm($this->data, "restorestep2&key=".$key,"profile", array("role" => "form")); ?>
    <?php Helper::PasswordFor("password", array("class"=>"form-control", "placeholder"=>"Введите пароль")); ?>
    <?php Helper::PasswordFor("password_repeat", array("class"=>"form-control", "placeholder"=>"Введите пароль повторно")); ?>
    <?php Helper::Submit("Сохранить", "btn-warning"); ?>
<?php Helper::EndForm(); ?>