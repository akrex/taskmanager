<h2>Востановление пароля</h2>
<?php Helper::BeginForm($this->data, "restore","profile", array("role" => "form")); ?>
    <?php Helper::InputFor("email", array("class"=>"form-control", "placeholder"=>"Введите email для востановления")); ?>
    <?php Helper::Submit("Отправить", "btn-warning"); ?>
<?php Helper::EndForm(); ?>